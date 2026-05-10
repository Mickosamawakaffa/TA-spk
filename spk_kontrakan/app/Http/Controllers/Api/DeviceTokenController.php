<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminDeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
                'error_code' => 'FORBIDDEN',
            ], 403);
        }

        $data = $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string|max:32',
        ]);

        $record = AdminDeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => $user->id,
                'platform' => $data['platform'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token tersimpan',
            'data' => [
                'id' => $record->id,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'token' => 'required|string',
        ]);

        AdminDeviceToken::where('token', $data['token'])
            ->where('user_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device token dihapus',
        ]);
    }
}
