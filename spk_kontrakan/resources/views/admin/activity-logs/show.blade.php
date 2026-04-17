@extends('layouts.admin')

@section('title', 'Detail Activity Log')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">Activity Log</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Detail Aktivitas
                    </h5>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Waktu</label>
                            <div class="fw-semibold">{{ $activityLog->created_at->format('d M Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">User</label>
                            <div class="fw-semibold">{{ $activityLog->user->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $activityLog->user->email ?? '-' }}</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Action</label>
                            <div class="fw-semibold text-uppercase">{{ $activityLog->action }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Model Type</label>
                            <div class="fw-semibold">{{ $activityLog->model_type ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Model ID</label>
                            <div class="fw-semibold">{{ $activityLog->model_id ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted mb-1">Deskripsi</label>
                            <div class="p-3 rounded border bg-light">{{ $activityLog->description }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">IP Address</label>
                            <div class="fw-semibold">{{ $activityLog->ip_address ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">User Agent</label>
                            <div class="fw-semibold text-break">{{ $activityLog->user_agent ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedModel)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-diagram-3 me-2"></i>Data Terkait
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Class</label>
                            <div class="fw-semibold">{{ get_class($relatedModel) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">ID</label>
                            <div class="fw-semibold">{{ $relatedModel->id ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
