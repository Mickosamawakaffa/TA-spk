{{-- Skeleton Loader Utility Script --}}
<script>
// ========== SKELETON LOADER UTILITIES ==========
function showSkeletonLoader(containerId, type = 'card', count = 1) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const skeletonHTML = getSkelethonHTML(type);
    let html = '';
    
    for (let i = 0; i < count; i++) {
        html += skeletonHTML;
    }
    
    container.innerHTML = html;
}

function getSkelethonHTML(type) {
    const baseStyles = `
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
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
        </style>
    `;
    
    switch(type) {
        case 'card':
            return `
                <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 15px;">
                    <div class="skeleton" style="width: 100%; height: 200px; margin-bottom: 0; border-radius: 12px 12px 0 0;"></div>
                    <div style="padding: 15px;">
                        <div class="skeleton" style="width: 80%; height: 16px; margin-bottom: 10px;"></div>
                        <div class="skeleton" style="width: 60%; height: 16px; margin-bottom: 15px;"></div>
                        <div class="skeleton" style="width: 100px; height: 40px; border-radius: 8px;"></div>
                    </div>
                </div>
            `;
        
        case 'stats':
            return `
                <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 15px;">
                    <div style="padding: 20px; display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex-grow: 1;">
                            <div class="skeleton" style="width: 70%; height: 12px; margin-bottom: 10px;"></div>
                            <div class="skeleton" style="width: 40%; height: 28px; margin-bottom: 10px;"></div>
                            <div class="skeleton" style="width: 50%; height: 12px;"></div>
                        </div>
                        <div class="skeleton" style="width: 60px; height: 60px; border-radius: 50%;"></div>
                    </div>
                </div>
            `;
        
        case 'chart':
            return `
                <div class="card border-0 shadow-sm" style="margin-bottom: 15px;">
                    <div style="padding: 20px;">
                        <div class="skeleton" style="width: 200px; height: 16px; margin-bottom: 20px;"></div>
                        <div class="skeleton" style="width: 100%; height: 300px; border-radius: 8px;"></div>
                    </div>
                </div>
            `;
        
        case 'table':
            return `
                <div style="padding: 15px; margin-bottom: 10px; display: flex; gap: 15px; border-bottom: 1px solid #e0e0e0;">
                    <div class="skeleton" style="width: 30px; height: 20px;"></div>
                    <div class="skeleton" style="flex: 1; height: 20px;"></div>
                    <div class="skeleton" style="width: 100px; height: 20px;"></div>
                    <div class="skeleton" style="width: 80px; height: 20px;"></div>
                </div>
            `;
        
        default:
            return `<div class="skeleton" style="width: 100%; height: 20px; margin-bottom: 10px;"></div>`;
    }
}

// Show skeleton loader with message
function showLoadingState(containerId, message = 'Loading...') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = `
        <div style="text-align: center; padding: 40px 20px;">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">${message}</p>
        </div>
    `;
}

// Hide skeleton loader
function hideSkeletonLoader(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '';
    }
}

// Auto-hide skeleton after delay
function autoHideSkeleton(containerId, delayMs = 2000) {
    setTimeout(() => {
        hideSkeletonLoader(containerId);
    }, delayMs);
}
</script>
