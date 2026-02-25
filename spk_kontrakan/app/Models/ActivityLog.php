<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship for better flexibility
    public function loggable()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // Performance Query Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public static function log($action, $description, $modelType = null, $modelId = null, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Security & Monitoring Enhancement
    public static function detectSuspiciousActivity($userId, $threshold = 50)
    {
        $recentActivity = self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHour())
            ->count();
            
        return $recentActivity > $threshold;
    }

    public static function logFailedAction($action, $reason, $ip = null)
    {
        return self::create([
            'user_id' => null,
            'action' => 'failed_' . $action,
            'description' => "Failed: $reason",
            'model_type' => null,
            'model_id' => null,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => $ip ?: request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getSecurityStats()
    {
        return [
            'failed_attempts_today' => self::where('action', 'like', 'failed_%')
                ->whereDate('created_at', today())
                ->count(),
            'unique_ips_today' => self::whereDate('created_at', today())
                ->distinct('ip_address')
                ->count('ip_address'),
            'suspicious_users' => self::select('user_id')
                ->where('created_at', '>=', now()->subHour())
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 50')
                ->pluck('user_id')
        ];
    }

    // Data Lifecycle Management
    public static function cleanup($keepDays = 365)
    {
        return self::where('created_at', '<', now()->subDays($keepDays))->delete();
    }

    public static function archiveOldLogs($keepDays = 365)
    {
        $oldLogs = self::where('created_at', '<', now()->subDays($keepDays))->get();
        
        if ($oldLogs->count() > 0) {
            $filename = 'activity_logs_archive_' . date('Y_m_d_His') . '.json';
            Storage::put("archives/$filename", $oldLogs->toJson());
            
            $deleted = self::where('created_at', '<', now()->subDays($keepDays))->delete();
            
            return [
                'archived_file' => $filename,
                'archived_count' => $oldLogs->count(),
                'deleted_count' => $deleted
            ];
        }
        
        return ['archived_count' => 0, 'deleted_count' => 0];
    }

    // Dashboard Statistics
    public static function getSummaryStats($userId = null)
    {
        $query = $userId ? self::where('user_id', $userId) : self::query();
        
        return [
            'total_actions' => $query->count(),
            'today_actions' => $query->whereDate('created_at', today())->count(),
            'this_week_actions' => $query->where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month_actions' => $query->whereMonth('created_at', now()->month)->count(),
            'most_common_action' => self::select('action')
                ->groupBy('action')
                ->orderByRaw('count(*) desc')
                ->value('action'),
            'actions_breakdown' => self::select('action')
                ->selectRaw('count(*) as count')
                ->groupBy('action')
                ->orderByRaw('count(*) desc')
                ->pluck('count', 'action'),
            'recent_activity' => $query->with('user')
                ->latest()
                ->limit(10)
                ->get()
        ];
    }

    public static function getModelStats()
    {
        return [
            'models_breakdown' => self::whereNotNull('model_type')
                ->select('model_type')
                ->selectRaw('count(*) as count')
                ->groupBy('model_type')
                ->orderByRaw('count(*) desc')
                ->pluck('count', 'model_type'),
            'daily_activity' => self::selectRaw('DATE(created_at) as date, count(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
        ];
    }

    // Utility Methods
    public static function getTopUsers($limit = 10)
    {
        return self::with('user')
            ->select('user_id')
            ->selectRaw('count(*) as activity_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByRaw('count(*) desc')
            ->limit($limit)
            ->get();
    }

    public static function getActivityByTimeRange($startDate, $endDate, $groupBy = 'day')
    {
        $format = match($groupBy) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };
        
        return self::selectRaw("DATE_FORMAT(created_at, '$format') as period, count(*) as count")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period');
    }
}
