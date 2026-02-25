<!-- Toast Notification Component -->
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
</div>

<style>
    .toast-container {
        pointer-events: none;
    }

    .toast {
        pointer-events: auto;
        min-width: 300px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast.hide {
        animation: slideOut 0.3s ease-in forwards;
    }

    @keyframes slideOut {
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-success {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .toast-error {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: white;
    }

    .toast-warning {
        background: linear-gradient(135deg, #ffa502 0%, #ffb84d 100%);
        color: white;
    }

    .toast-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .toast .toast-header {
        background: transparent;
        border: none;
        color: white;
        padding: 0;
    }

    .toast .toast-body {
        padding: 0;
        font-size: 0.95rem;
    }
</style>

<script>
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();
        
        const icons = {
            'success': 'bi-check-circle-fill',
            'error': 'bi-exclamation-triangle-fill',
            'warning': 'bi-exclamation-circle-fill',
            'info': 'bi-info-circle-fill'
        };

        const html = `
            <div id="${toastId}" class="toast show toast-${type}" role="alert">
                <div class="d-flex align-items-center p-3">
                    <i class="bi ${icons[type] || icons.info} me-2"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        const toastElement = document.getElementById(toastId);

        if (duration > 0) {
            setTimeout(() => {
                toastElement.classList.add('hide');
                setTimeout(() => toastElement.remove(), 300);
            }, duration);
        }

        // Close button
        toastElement.querySelector('.btn-close').addEventListener('click', () => {
            toastElement.classList.add('hide');
            setTimeout(() => toastElement.remove(), 300);
        });
    }

    // Auto-show session toasts
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if(session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif

        @if(session('info'))
            showToast("{{ session('info') }}", 'info');
        @endif
    });
</script>
