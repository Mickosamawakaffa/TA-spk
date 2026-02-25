@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid px-4">
    <style>
        .action-badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .action-create { background: #d4edda; color: #155724; }
        .action-update { background: #d1ecf1; color: #0c5460; }
        .action-delete { background: #f8d7da; color: #721c24; }
        .action-export { background: #fff3cd; color: #856404; }
        .action-login { background: #cfe2ff; color: #084298; }
    </style>

    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 2rem; color: white; margin-bottom: 2rem;">
        <h2 class="mb-2">
            <i class="bi bi-clock-history me-3"></i>Activity Log
        </h2>
        <p class="mb-0 fs-6">Riwayat semua aktivitas user dalam sistem</p>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-2">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua User</option>
                        @foreach(\App\Models\User::all() as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-2">Action</label>
                    <select name="action" class="form-select">
                        <option value="">Semua Action</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="export" {{ request('action') == 'export' ? 'selected' : '' }}>Export</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-2">Model Type</label>
                    <select name="model_type" class="form-select">
                        <option value="">Semua</option>
                        <option value="Kontrakan" {{ request('model_type') == 'Kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                        <option value="Laundry" {{ request('model_type') == 'Laundry' ? 'selected' : '' }}>Laundry</option>
                        <option value="Kriteria" {{ request('model_type') == 'Kriteria' ? 'selected' : '' }}>Kriteria</option>
                        <option value="User" {{ request('model_type') == 'User' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2"></i>Daftar Aktivitas ({{ $logs->total() }})
                </h5>
                <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-sm" style="border: 2px solid #667eea; color: #667eea; background: transparent;">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada aktivitas ditemukan</h5>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Deskripsi</th>
                            <th>Model</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="align-middle">
                            <td>
                                <small>{{ $log->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $log->user->name ?? 'Unknown' }}</strong><br>
                                <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="action-badge action-{{ strtolower($log->action) }}">
                                    <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                {{ $log->description }}
                            </td>
                            <td>
                                @if($log->model_type)
                                    <small class="text-muted">
                                        {{ $log->model_type }}
                                        @if($log->model_id)
                                            #{{ $log->model_id }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted font-monospace">{{ $log->ip_address }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
