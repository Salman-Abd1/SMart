<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan riwayat transaksi penjualan.
     */
    public function index()
    {
        $transaksis = Transaksi::with('barang')->latest()->paginate(50);
        return view('transaksis.index', compact('transaksis'));
    }

    /**
     * Menampilkan form untuk membuat transaksi penjualan baru.
     */
    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('transaksis.create', compact('barangs'));
    }

    /**
     * Menyimpan transaksi penjualan dan mengurangi stok.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    if (!isset($item['barang_id']) || !isset($item['jumlah'])) {
                        continue;
                    }

                    $barang = Barang::findOrFail($item['barang_id']);

                    if ($barang->stok < $item['jumlah']) {
                        throw new \Exception("Stok untuk barang '{$barang->nama_barang}' tidak cukup.");
                    }

                    $total = $barang->harga * $item['jumlah'];

                    $barang->stok -= $item['jumlah'];
                    $barang->save();

                    // Membuat catatan di tabel transaksi
                    Transaksi::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'total_harga' => $total,
                    ]);
                }
            });

            return back()->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form untuk mencatat pembelian barang (stok masuk).
     */
    public function createPembelian()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('pembelian.create', compact('barangs'));
    }

    /**
     * Menyimpan data pembelian dan menambah stok barang.
     */
    public function storePembelian(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    if (!isset($item['barang_id']) || !isset($item['jumlah'])) {
                        continue;
                    }

                    $barang = Barang::findOrFail($item['barang_id']);

                    $barang->stok += $item['jumlah'];
                    $barang->save();
                }
            });

            return back()->with('success', 'Data pembelian berhasil disimpan dan stok telah diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Transaksi $transaksi) {}
    public function edit(Transaksi $transaksi) {}
    public function update(Request $request, Transaksi $transaksi) {}
    public function destroy(Transaksi $transaksi) {}
}
