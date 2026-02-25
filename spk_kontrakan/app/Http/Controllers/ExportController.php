<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export data Kontrakan ke Excel
     */
    public function kontrakanExcel(Request $request)
    {
        try {
            // Gunakan query yang sama dengan index
            $query = Kontrakan::query();
            
            // Filter search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%")
                      ->orWhere('fasilitas', 'like', "%$search%");
                });
            }
            
            // Filter harga
            if ($request->filled('harga_min')) {
                $query->where('harga', '>=', $request->harga_min);
            }
            if ($request->filled('harga_max')) {
                $query->where('harga', '<=', $request->harga_max);
            }
            
            // Filter jarak
            if ($request->filled('jarak_max')) {
                $jarakMeter = $request->jarak_max * 1000;
                $query->where('jarak', '<=', $jarakMeter);
            }
            
            // Filter jumlah kamar
            if ($request->filled('jumlah_kamar_min')) {
                $query->where('jumlah_kamar', '>=', $request->jumlah_kamar_min);
            }
            if ($request->filled('jumlah_kamar_max')) {
                $query->where('jumlah_kamar', '<=', $request->jumlah_kamar_max);
            }
            
            $kontrakan = $query->get();
            
            // Log activity
            ActivityLog::log('export', "Export data Kontrakan ke CSV ({$kontrakan->count()} items)", 'Kontrakan', null);
            
            // Gunakan CSV export sebagai alternatif
            $filename = 'data_kontrakan_' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($kontrakan) {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['ID', 'Nama', 'Alamat', 'Harga', 'Fasilitas', 'Jumlah Kamar', 'Jarak (meter)', 'Latitude', 'Longitude']);
                
                foreach ($kontrakan as $item) {
                    fputcsv($file, [
                        $item->id,
                        $item->nama,
                        $item->alamat,
                        $item->harga,
                        $item->fasilitas,
                        $item->jumlah_kamar,
                        $item->jarak,
                        $item->latitude,
                        $item->longitude
                    ]);
                }
                
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
            
        } catch (Exception $e) {
            Log::error('Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export data Kontrakan ke PDF
     */
    public function kontrakanPDF(Request $request)
    {
        try {
            // Gunakan query yang sama dengan index
            $query = Kontrakan::query();
            
            // Filter search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%")
                      ->orWhere('fasilitas', 'like', "%$search%");
                });
            }
            
            // Filter harga
            if ($request->filled('harga_min')) {
                $query->where('harga', '>=', $request->harga_min);
            }
            if ($request->filled('harga_max')) {
                $query->where('harga', '<=', $request->harga_max);
            }
            
            // Filter jarak
            if ($request->filled('jarak_max')) {
                $jarakMeter = $request->jarak_max * 1000;
                $query->where('jarak', '<=', $jarakMeter);
            }
            
            // Filter jumlah kamar
            if ($request->filled('jumlah_kamar_min')) {
                $query->where('jumlah_kamar', '>=', $request->jumlah_kamar_min);
            }
            if ($request->filled('jumlah_kamar_max')) {
                $query->where('jumlah_kamar', '<=', $request->jumlah_kamar_max);
            }
            
            $kontrakan = $query->get();
            
            // Log activity
            ActivityLog::log('export', "Export data Kontrakan ke PDF ({$kontrakan->count()} items)", 'Kontrakan', null);
            
            // Generate HTML content for PDF
            $html = view('exports.kontrakan-pdf', compact('kontrakan'))->render();
            
            // Return as downloadable HTML file (can be printed as PDF by browser)
            return Response::make($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="data_kontrakan_' . now()->format('Y-m-d-H-i-s') . '.html"'
            ]);
            
        } catch (Exception $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export data Laundry ke CSV
     */
    public function laundryExcel(Request $request)
    {
        try {
            $query = Laundry::with('layanan');
            
            // Filter search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%");
                });
            }
            
            // Filter harga
            if ($request->filled('harga_min')) {
                $query->whereHas('layanan', function($q) use ($request) {
                    $q->where('harga', '>=', $request->harga_min);
                });
            }
            if ($request->filled('harga_max')) {
                $query->whereHas('layanan', function($q) use ($request) {
                    $q->where('harga', '<=', $request->harga_max);
                });
            }
            
            // Filter jarak
            if ($request->filled('jarak')) {
                $jarakMeter = $request->jarak * 1000;
                $query->where('jarak', '<=', $jarakMeter);
            }
            
            $laundry = $query->get();
            
            // Log activity
            ActivityLog::log('export', "Export data Laundry ke CSV ({$laundry->count()} items)", 'Laundry', null);
            
            $filename = 'data_laundry_' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($laundry) {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['Nama Laundry', 'Alamat', 'Fasilitas', 'Layanan & Harga', 'No. WhatsApp', 'Tanggal Input']);
                
                foreach ($laundry as $item) {
                    $layananInfo = $item->layanan->map(function($svc) {
                        return ucfirst($svc->jenis_layanan) . ' (Rp ' . number_format($svc->harga, 0, ',', '.') . ')';
                    })->implode(', ');
                    
                    fputcsv($file, [
                        $item->nama,
                        $item->alamat ?? '-',
                        $item->fasilitas ?? '-',
                        $layananInfo ?: '-',
                        $item->no_whatsapp ?? '-',
                        $item->created_at ? $item->created_at->format('d/m/Y') : '-',
                    ]);
                }
                
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
            
        } catch (Exception $e) {
            Log::error('Export CSV Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export CSV: ' . $e->getMessage());
        }
    }

    /**
     * Export data Laundry ke PDF
     */
    public function laundryPDF(Request $request)
    {
        try {
            $query = Laundry::with('layanan');
            
            // Filter search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%");
                });
            }
            
            // Filter harga
            if ($request->filled('harga_min')) {
                $query->where('harga', '>=', $request->harga_min);
            }
            if ($request->filled('harga_max')) {
                $query->where('harga', '<=', $request->harga_max);
            }
            
            $laundry = $query->get();
            
            // Log activity
            ActivityLog::log('export', "Export data Laundry ke PDF ({$laundry->count()} items)", 'Laundry', null);
            
            // Generate HTML content for PDF
            $html = view('exports.laundry-pdf', compact('laundry'))->render();
            
            // Return as downloadable HTML file (can be printed as PDF by browser)
            return Response::make($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="data_laundry_' . now()->format('Y-m-d-H-i-s') . '.html"'
            ]);
            
        } catch (Exception $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export hasil SAW ke PDF
     */
    public function sawResultsPDF(Request $request)
    {
        try {
            // Ambil data dari session atau request
            $hasilJson = $request->input('hasil_json');
            $hasil = json_decode($hasilJson, true);
            $tipe = $request->input('tipe');
            $jenisLayanan = $request->input('jenis_layanan');
            
            if (empty($hasil)) {
                return redirect()->back()->with('error', 'Tidak ada data untuk di-export');
            }
            
            // Log activity
            ActivityLog::log('export', "Export hasil SAW ke PDF ({$tipe})", 'SAW', null);
            
            // Generate HTML content for PDF
            $html = view('exports.saw-results-pdf', compact('hasil', 'tipe', 'jenisLayanan'))->render();
            
            // Return as downloadable HTML file (can be printed as PDF by browser)
            return Response::make($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="hasil_saw_' . now()->format('Y-m-d-H-i-s') . '.html"'
            ]);
            
        } catch (Exception $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export hasil SAW ke CSV
     */
    public function sawResultsExcel(Request $request)
    {
        try {
            $hasilJson = $request->input('hasil_json');
            $hasil = json_decode($hasilJson, true);
            $tipe = $request->input('tipe');
            
            if (empty($hasil)) {
                return redirect()->back()->with('error', 'Tidak ada data untuk di-export');
            }
            
            // Log activity
            ActivityLog::log('export', "Export hasil SAW ke CSV ({$tipe})", 'SAW', null);
            
            $filename = 'hasil_saw_' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($hasil, $tipe) {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['Ranking', 'Nama ' . ucfirst($tipe), 'Alamat', 'Nilai SAW']);
                
                foreach ($hasil as $item) {
                    fputcsv($file, [
                        $item['ranking'] ?? '-',
                        $item['nama'] ?? '-',
                        $item['alamat'] ?? '-',
                        number_format($item['nilai'] ?? ($item['skor'] ?? 0), 4),
                    ]);
                }
                
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
            
        } catch (Exception $e) {
            Log::error('Export CSV Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export CSV: ' . $e->getMessage());
        }
    }
}
