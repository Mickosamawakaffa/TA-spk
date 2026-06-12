<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('model_type')) {
            $query->byModel($request->model_type);
        }

        if ($request->filled('days')) {
            $query->recent($request->days);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if (auth()->user()->role === 'super_admin' && $request->filled('security_check')) {
            $suspiciousUsers = ActivityLog::getSecurityStats()['suspicious_users'];

            if (!empty($suspiciousUsers)) {
                session()->flash('warning', 'Detected suspicious activity from ' . count($suspiciousUsers) . ' users');
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        $stats = ActivityLog::getSummaryStats();
        $modelStats = ActivityLog::getModelStats();
        $securityStats = auth()->user()->role === 'super_admin'
            ? ActivityLog::getSecurityStats()
            : [];

        return view('admin.activity-logs.index', compact('logs', 'stats', 'modelStats', 'securityStats'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'loggable']);

        $relatedModel = $activityLog->loggable;

        return view('admin.activity-logs.show', compact('activityLog', 'relatedModel'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $csv = "Tanggal,User,Action,Deskripsi,Model Type,Model ID,IP Address\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-',
                $log->user->name ?? 'Unknown',
                $log->action,
                $log->description,
                $log->model_type,
                $log->model_id,
                $log->ip_address
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="activity_logs_' . now()->format('Y-m-d-H-i-s') . '.csv"');
    }

    public function clear(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $days = $request->input('days', 30);

        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success', "Dihapus $deleted log aktivitas lebih dari $days hari.");
    }

    public function dashboard()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $stats = ActivityLog::getSummaryStats();
        $modelStats = ActivityLog::getModelStats();
        $securityStats = ActivityLog::getSecurityStats();
        $topUsers = ActivityLog::getTopUsers();

        $dailyActivity = ActivityLog::getActivityByTimeRange(
            now()->subDays(30),
            now(),
            'day'
        );

        $hourlyActivity = ActivityLog::getActivityByTimeRange(
            now()->subDay(),
            now(),
            'hour'
        );

        return view('admin.activity-logs.dashboard', compact(
            'stats',
            'modelStats',
            'securityStats',
            'topUsers',
            'dailyActivity',
            'hourlyActivity'
        ));
    }

    public function archive(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $keepDays = $request->input('keep_days', 365);

        $result = ActivityLog::archiveOldLogs($keepDays);

        if ($result['archived_count'] > 0) {
            return redirect()->back()->with('success',
                "Successfully archived {$result['archived_count']} logs to {$result['archived_file']} and deleted {$result['deleted_count']} records from database."
            );
        }

        return redirect()->back()->with('info', 'No old logs found to archive.');
    }

    public function security()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $securityStats = ActivityLog::getSecurityStats();

        $suspiciousActivity = ActivityLog::select('user_id', 'ip_address')
            ->selectRaw('COUNT(*) as activity_count')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('user_id', 'ip_address')
            ->having('activity_count', '>', 50)
            ->with('user')
            ->get();

        $failedActions = ActivityLog::where('action', 'like', 'failed_%')
            ->recent(7)
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.activity-logs.security', compact(
            'securityStats',
            'suspiciousActivity',
            'failedActions'
        ));
    }

    public function apiStats()
    {
        return response()->json([
            'summary' => ActivityLog::getSummaryStats(),
            'security' => ActivityLog::getSecurityStats(),
            'recent_activity' => ActivityLog::with('user')
                ->latest()
                ->limit(10)
                ->get(),
            'last_updated' => now()->toISOString()
        ]);
    }
}