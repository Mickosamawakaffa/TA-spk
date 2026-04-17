@extends('layouts.admin')

@section('title', 'Security Monitoring')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Security Monitoring</h4>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="alert alert-warning border-0 mb-4">
        Gunakan halaman ini untuk memonitor aktivitas mencurigakan dan anomali penggunaan.
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <strong>Suspicious Activity</strong>
                </div>
                <div class="card-body">
                    <p class="mb-0">Jumlah data: {{ $suspiciousActivity->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <strong>Failed Actions</strong>
                </div>
                <div class="card-body">
                    <p class="mb-0">Jumlah data: {{ $failedActions->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
