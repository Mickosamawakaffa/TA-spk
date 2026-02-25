@extends('layouts.app')

@section('title', 'Sistem Rekomendasi Kontrakan')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sistem Rekomendasi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center py-5">
                    <h2 class="mb-2"><i class="bi bi-award me-2"></i>Sistem Rekomendasi Kontrakan</h2>
                    <p class="mb-0 opacity-75">Temukan kontrakan terbaik sesuai prioritas Anda menggunakan Metode SAW</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    @if($kontrakans->isEmpty())
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Peringatan!</strong> Belum ada data kontrakan. Silakan tambahkan data kontrakan terlebih dahulu.
        </div>
    @endif

    <!-- Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            
            <!-- Info Box -->
            <div class="alert alert-info border-0 mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Cara Kerja:</strong> Pilih cara yang sesuai untuk Anda. Sistem akan menampilkan bobot yang digunakan untuk transparansi perhitungan.
            </div>

            <form action="{{ route('rekomendasi.hitung') }}" method="POST" id="bobotForm">
                @csrf

                <!-- Mode Selection -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="mode-card" onclick="selectMode('preset')" id="mode-preset">
                            <div class="card border-2 h-100 cursor-pointer">
                                <div class="card-body text-center p-4">
                                    <div class="display-4 mb-3">🎯</div>
                                    <h5 class="card-title">Gunakan Profil</h5>
                                    <p class="card-text text-muted">Pilih profil sesuai kebutuhan Anda. Cocok untuk pemula.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mode-card" onclick="selectMode('manual')" id="mode-manual">
                            <div class="card border-2 h-100 cursor-pointer">
                                <div class="card-body text-center p-4">
                                    <div class="display-4 mb-3">⚙️</div>
                                    <h5 class="card-title">Atur Manual</h5>
                                    <p class="card-text text-muted">Sesuaikan prioritas sendiri. Untuk user advanced.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preset Section -->
                <div id="preset-section" class="mb-4">
                    <h5 class="mb-3"><i class="bi bi-bookmark-star me-2"></i>Pilih Profil Sesuai Kebutuhan</h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="preset-card" onclick="selectPreset('mahasiswa')" id="preset-mahasiswa">
                                <div class="card h-100 text-center">
                                    <div class="card-body">
                                        <div class="fs-2 mb-2">🎓</div>
                                        <h6 class="card-title">Mahasiswa</h6>
                                        <p class="card-text small text-muted">Prioritas harga terjangkau</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="preset-card" onclick="selectPreset('pekerja')" id="preset-pekerja">
                                <div class="card h-100 text-center">
                                    <div class="card-body">
                                        <div class="fs-2 mb-2">💼</div>
                                        <h6 class="card-title">Pekerja</h6>
                                        <p class="card-text small text-muted">Prioritas jarak dekat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="preset-card" onclick="selectPreset('keluarga')" id="preset-keluarga">
                                <div class="card h-100 text-center">
                                    <div class="card-body">
                                        <div class="fs-2 mb-2">👨‍👩‍👧</div>
                                        <h6 class="card-title">Keluarga</h6>
                                        <p class="card-text small text-muted">Prioritas kamar banyak</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="preset-card" onclick="selectPreset('balanced')" id="preset-balanced">
                                <div class="card h-100 text-center">
                                    <div class="card-body">
                                        <div class="fs-2 mb-2">⚖️</div>
                                        <h6 class="card-title">Seimbang</h6>
                                        <p class="card-text small text-muted">Semua aspek berimbang</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Section -->
                <div id="manual-section" class="mb-4" style="display:none;">
                    <h5 class="mb-3"><i class="bi bi-sliders me-2"></i>Atur Prioritas Manual</h5>
                    
                    <!-- Harga -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label fw-bold">💰 Harga</label>
                            <span class="badge bg-primary fs-6" id="val_harga">35%</span>
                        </div>
                        <input type="range" class="form-range" id="slider_harga" min="10" max="70" value="35" oninput="adjustSliders('harga')">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Min: 10%</span>
                            <span>Semakin murah semakin baik</span>
                            <span>Max: 70%</span>
                        </div>
                    </div>

                    <!-- Jarak -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label fw-bold">📍 Jarak</label>
                            <span class="badge bg-primary fs-6" id="val_jarak">25%</span>
                        </div>
                        <input type="range" class="form-range" id="slider_jarak" min="10" max="70" value="25" oninput="adjustSliders('jarak')">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Min: 10%</span>
                            <span>Semakin dekat semakin baik</span>
                            <span>Max: 70%</span>
                        </div>
                    </div>

                    <!-- Kamar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label fw-bold">🚪 Jumlah Kamar</label>
                            <span class="badge bg-primary fs-6" id="val_kamar">20%</span>
                        </div>
                        <input type="range" class="form-range" id="slider_kamar" min="10" max="70" value="20" oninput="adjustSliders('kamar')">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Min: 10%</span>
                            <span>Semakin banyak semakin baik</span>
                            <span>Max: 70%</span>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label fw-bold">⭐ Fasilitas</label>
                            <span class="badge bg-primary fs-6" id="val_fasilitas">20%</span>
                        </div>
                        <input type="range" class="form-range" id="slider_fasilitas" min="10" max="70" value="20" oninput="adjustSliders('fasilitas')">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Min: 10%</span>
                            <span>Semakin lengkap semakin baik</span>
                            <span>Max: 70%</span>
                        </div>
                    </div>
                </div>

                <!-- Bobot Display (Always Visible) -->
                <div class="card border-0" style="background: linear-gradient(135deg, #e7f3ff 0%, #d4edff 100%);">
                    <div class="card-body">
                        <h5 class="text-center mb-4">📊 Bobot yang Akan Digunakan</h5>
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="bg-white rounded p-3">
                                    <div class="small text-muted mb-1">💰 Harga</div>
                                    <div class="fs-3 fw-bold text-primary" id="display_harga">35%</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-white rounded p-3">
                                    <div class="small text-muted mb-1">📍 Jarak</div>
                                    <div class="fs-3 fw-bold text-primary" id="display_jarak">25%</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-white rounded p-3">
                                    <div class="small text-muted mb-1">🚪 Kamar</div>
                                    <div class="fs-3 fw-bold text-primary" id="display_kamar">20%</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="bg-white rounded p-3">
                                    <div class="small text-muted mb-1">⭐ Fasilitas</div>
                                    <div class="fs-3 fw-bold text-primary" id="display_fasilitas">20%</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <h4 class="text-success mb-0">Total: <span id="total">100</span>% ✅</h4>
                        </div>
                    </div>
                </div>

                <!-- Hidden Inputs -->
                <input type="hidden" name="bobot_harga" id="input_harga" value="35">
                <input type="hidden" name="bobot_jarak" id="input_jarak" value="25">
                <input type="hidden" name="bobot_kamar" id="input_kamar" value="20">
                <input type="hidden" name="bobot_fasilitas" id="input_fasilitas" value="20">
                <input type="hidden" name="mode" id="input_mode" value="preset">
                <input type="hidden" name="preset_type" id="input_preset" value="">

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" {{ $kontrakans->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-calculator me-2"></i>Hitung Rekomendasi Terbaik
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
    .mode-card .card, .preset-card .card {
        cursor: pointer;
        transition: all 0.3s;
    }
    .mode-card .card:hover, .preset-card .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .mode-card.active .card {
        border-color: #667eea !important;
        background: linear-gradient(135deg, #e7f3ff 0%, #d4edff 100%);
    }
    .preset-card.selected .card {
        border-color: #28a745 !important;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    }
    .form-range::-webkit-slider-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .form-range::-moz-range-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection

@section('scripts')
<script>
    const MIN_BOBOT = 10;
    const MAX_BOBOT = 70;
    const KRITERIA = ['harga', 'jarak', 'kamar', 'fasilitas'];
    let currentMode = 'preset';

    // Mode Selection
    function selectMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-card').forEach(el => el.classList.remove('active'));
        document.getElementById('mode-' + mode).classList.add('active');
        
        document.getElementById('preset-section').style.display = mode === 'preset' ? 'block' : 'none';
        document.getElementById('manual-section').style.display = mode === 'manual' ? 'block' : 'none';
        document.getElementById('input_mode').value = mode;
    }

    // Preset Selection
    function selectPreset(type) {
        document.querySelectorAll('.preset-card').forEach(el => el.classList.remove('selected'));
        document.getElementById('preset-' + type).classList.add('selected');
        document.getElementById('input_preset').value = type;
        
        let bobot = {};
        switch(type) {
            case 'mahasiswa': bobot = {harga: 50, jarak: 20, kamar: 15, fasilitas: 15}; break;
            case 'pekerja': bobot = {harga: 20, jarak: 50, kamar: 15, fasilitas: 15}; break;
            case 'keluarga': bobot = {harga: 25, jarak: 15, kamar: 45, fasilitas: 15}; break;
            case 'balanced': default: bobot = {harga: 35, jarak: 25, kamar: 20, fasilitas: 20}; break;
        }
        
        KRITERIA.forEach(k => document.getElementById('slider_' + k).value = bobot[k]);
        updateDisplay();
    }

    // Manual Slider Adjustment
    function adjustSliders(changed) {
        let changedValue = parseInt(document.getElementById('slider_' + changed).value);
        if (changedValue < MIN_BOBOT) changedValue = MIN_BOBOT;
        if (changedValue > MAX_BOBOT) changedValue = MAX_BOBOT;
        document.getElementById('slider_' + changed).value = changedValue;
        
        const values = {};
        KRITERIA.forEach(k => values[k] = parseInt(document.getElementById('slider_' + k).value));
        
        let total = Object.values(values).reduce((a, b) => a + b, 0);
        if (total !== 100) {
            const diff = total - 100;
            const others = KRITERIA.filter(k => k !== changed);
            const otherTotal = others.reduce((sum, k) => sum + values[k], 0);
            
            if (otherTotal > 0) {
                let distributed = 0;
                others.forEach((k, index) => {
                    const proportion = values[k] / otherTotal;
                    let reduction = Math.round(diff * proportion);
                    if (index === others.length - 1) reduction = diff - distributed;
                    
                    let newVal = values[k] - reduction;
                    if (newVal < MIN_BOBOT) newVal = MIN_BOBOT;
                    if (newVal > MAX_BOBOT) newVal = MAX_BOBOT;
                    
                    document.getElementById('slider_' + k).value = newVal;
                    distributed += reduction;
                });
            }
        }
        updateDisplay();
    }

    // Update Display
    function updateDisplay() {
        let total = 0;
        KRITERIA.forEach(k => {
            const value = parseInt(document.getElementById('slider_' + k).value);
            total += value;
            document.getElementById('val_' + k).textContent = value + '%';
            document.getElementById('display_' + k).textContent = value + '%';
            document.getElementById('input_' + k).value = value;
        });
        document.getElementById('total').textContent = total;
    }

    // Form Validation
    document.getElementById('bobotForm').addEventListener('submit', function(e) {
        if (currentMode === 'preset' && !document.getElementById('input_preset').value) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Perhatian', text: 'Silakan pilih profil terlebih dahulu!'});
            return false;
        }
        
        let total = 0;
        KRITERIA.forEach(k => total += parseInt(document.getElementById('input_' + k).value));
        if (total !== 100) {
            e.preventDefault();
            Swal.fire({icon: 'error', title: 'Error', text: `Total bobot harus 100%! Sekarang: ${total}%`});
            return false;
        }
    });

    // Initialize
    selectPreset('balanced');
    updateDisplay();
</script>
@endsection