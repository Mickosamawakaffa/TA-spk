{{-- Skeleton Loader Component --}}
<style>
    .skeleton {
        background: linear-gradient(90deg, var(--skeleton-bg1, #e0e0e0) 25%, var(--skeleton-bg2, #f0f0f0) 50%, var(--skeleton-bg1, #e0e0e0) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    html.dark-mode .skeleton {
        --skeleton-bg1: #3a3a3a;
        --skeleton-bg2: #4a4a4a;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 16px;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .skeleton-text.short {
        width: 60%;
    }

    .skeleton-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-block;
    }

    .skeleton-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .skeleton-card-image {
        width: 100%;
        height: 200px;
        margin-bottom: 15px;
        border-radius: 8px;
    }

    .skeleton-card-body {
        padding: 15px;
    }

    .skeleton-table-row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .skeleton-table-cell {
        flex: 1;
        height: 20px;
        border-radius: 4px;
    }

    .skeleton-button {
        height: 40px;
        width: 100px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .skeleton-chart {
        height: 300px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>

{{-- Card Skeleton Loader --}}
@if($type === 'card')
    <div class="card border-0 shadow-sm skeleton-card">
        <div class="skeleton skeleton-card-image" style="margin-bottom: 0;"></div>
        <div class="skeleton-card-body">
            <div class="skeleton skeleton-text" style="width: 80%; margin-bottom: 10px;"></div>
            <div class="skeleton skeleton-text short" style="margin-bottom: 15px;"></div>
            <div class="skeleton skeleton-button"></div>
        </div>
    </div>
@endif

{{-- Table Row Skeleton Loader --}}
@if($type === 'table-row')
    <tr>
        <td>
            <div class="skeleton skeleton-table-cell" style="width: 20px; height: 20px;"></div>
        </td>
        <td>
            <div class="skeleton skeleton-text" style="width: 70%;"></div>
        </td>
        <td>
            <div class="skeleton skeleton-text" style="width: 50%;"></div>
        </td>
        <td>
            <div class="skeleton skeleton-text short"></div>
        </td>
        <td>
            <div class="skeleton skeleton-text short"></div>
        </td>
    </tr>
@endif

{{-- Stats Card Skeleton --}}
@if($type === 'stats')
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div style="flex-grow: 1;">
                    <div class="skeleton skeleton-text short" style="width: 70%;"></div>
                    <div class="skeleton skeleton-text" style="width: 40%; height: 28px;"></div>
                    <div class="skeleton skeleton-text short" style="width: 50%;"></div>
                </div>
                <div class="skeleton skeleton-avatar"></div>
            </div>
        </div>
    </div>
@endif

{{-- Chart Skeleton --}}
@if($type === 'chart')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="skeleton skeleton-text" style="width: 200px;"></div>
        </div>
        <div class="card-body">
            <div class="skeleton skeleton-chart"></div>
        </div>
    </div>
@endif

{{-- Text Lines Skeleton --}}
@if($type === 'text-lines')
    <div class="mb-3">
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text short"></div>
    </div>
@endif

{{-- Minimal --}}
@if($type === 'minimal')
    <div class="skeleton skeleton-text" style="width: {{ $width ?? '100%' }}; height: {{ $height ?? '20px' }};"></div>
@endif
