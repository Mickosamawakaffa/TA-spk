@extends('layouts.admin')

@section('title', 'Activity Log Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Dashboard Activity Log</h4>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="alert alert-info border-0">
        Halaman ringkasan aktivitas tersedia. Gunakan halaman ini untuk melihat statistik aktivitas sistem.
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Total Aktivitas</small>
                    <h5 class="mb-0">{{ $stats['total_logs'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Hari Ini</small>
                    <h5 class="mb-0">{{ $stats['today_logs'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">7 Hari</small>
                    <h5 class="mb-0">{{ $stats['week_logs'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">30 Hari</small>
                    <h5 class="mb-0">{{ $stats['month_logs'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
