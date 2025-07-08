<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon; // <-- TAMBAHKAN INI

class DashboardController extends Controller
{
    public function index()
    {
        // --- Logika Notifikasi Stok Rendah (Sudah Ada) ---
        // Kita gunakan perbandingan dengan kolom 'minimal_stok' yang sudah kita buat
        $stokHampirHabis = Barang::whereColumn('stok', '<=', 'minimal_stok')->get();

        // --- Logika Notifikasi Kadaluarsa (BARU) ---
        $tanggalPeringatan = Carbon::now()->addDays(30); // Peringatan untuk 30 hari ke depan
        $barangAkanKadaluarsa = Barang::whereNotNull('tanggal_kadaluarsa')
                                    ->where('tanggal_kadaluarsa', '<=', $tanggalPeringatan)
                                    ->get();

        // --- Data Lainnya (Sudah Ada) ---
        $totalBarang = Barang::count();
        $totalTransaksi = Transaksi::count();

        // Kirim semua data ke view, termasuk data kadaluarsa
        return view('dashboard', [
            'totalBarang' => $totalBarang,
            'totalTransaksi' => $totalTransaksi,
            'stokHampirHabis' => $stokHampirHabis,
            'barangAkanKadaluarsa' => $barangAkanKadaluarsa // <-- TAMBAHKAN INI
        ]);
    }
}
