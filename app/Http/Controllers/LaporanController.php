<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk transaksi
        $query = Transaksi::with('barang')->latest();

        // Logika untuk filter periode
        if ($request->has('periode')) {
            switch ($request->periode) {
                case 'harian':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'mingguan':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'bulanan':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        // Ambil data transaksi dengan paginasi (50 data per halaman)
        $transaksis = $query->paginate(50)->withQueryString();

        // Hitung total penjualan HANYA dari data yang difilter
        $total = (clone $query)->sum('total_harga');

        // --- Data untuk Visualisasi Grafik ---
        // Mengambil total penjualan harian untuk 30 hari terakhir
        $penjualanHarian = Transaksi::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Format data untuk Chart.js
        $labels = $penjualanHarian->pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $data = $penjualanHarian->pluck('total');
        // --- Akhir Data Grafik ---

        return view('laporan.index', compact('transaksis', 'total', 'labels', 'data'));
    }
}
