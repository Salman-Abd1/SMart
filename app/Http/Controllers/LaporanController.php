<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;


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

        // --- Filter Rentang Tanggal Kustom (BARU) ---
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // --- Akhir Filter Rentang Tanggal Kustom ---


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
        public function inventaris()
    {
        $barangs = Barang::orderBy('nama_barang', 'asc')->paginate(50);

        // Hitung total nilai inventaris (Harga Beli * Stok)
        $totalNilaiInventaris = Barang::sum(DB::raw('harga * stok'));

        return view('laporan.inventaris', compact('barangs', 'totalNilaiInventaris'));
    }
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'laporan-penjualan-' . Carbon::now()->format('Y-m-d') . '.csv';

        $query = Transaksi::with('barang')->latest();

        // (Salin logika filter yang sama dari metode index)
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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $transaksis = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Transaksi', 'Nama Barang', 'Kode Barang', 'Jumlah', 'Total Harga', 'Tanggal Transaksi'];

        $callback = function() use($transaksis, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transaksis as $transaksi) {
                $row['ID Transaksi']  = $transaksi->id;
                $row['Nama Barang']    = $transaksi->barang->nama_barang ?? 'N/A';
                $row['Kode Barang']  = $transaksi->barang->kode_barang ?? 'N/A';
                $row['Jumlah']  = $transaksi->jumlah;
                $row['Total Harga'] = $transaksi->total_harga;
                $row['Tanggal Transaksi'] = $transaksi->created_at->format('Y-m-d H:i:s');

                fputcsv($file, [
                    $row['ID Transaksi'],
                    $row['Nama Barang'],
                    $row['Kode Barang'],
                    $row['Jumlah'],
                    $row['Total Harga'],
                    $row['Tanggal Transaksi']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
