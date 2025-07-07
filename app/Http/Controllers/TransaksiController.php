<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::with('barang')->latest()->get();
        return view('transaksis.index', compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = Barang::all();
        return view('transaksis.create', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi bahwa 'items' adalah array dan setiap elemen di dalamnya valid
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    // Pastikan barang_id dan jumlah ada sebelum diproses
                    if (!isset($item['barang_id']) || !isset($item['jumlah'])) {
                        continue; // Lewati item yang tidak lengkap
                    }

                    $barang = Barang::findOrFail($item['barang_id']);

                    // Cek stok untuk setiap barang
                    if ($barang->stok < $item['jumlah']) {
                        // Melempar exception akan otomatis membatalkan seluruh transaksi
                        throw new \Exception("Stok untuk barang '{$barang->nama_barang}' tidak cukup. Sisa stok: {$barang->stok}.");
                    }

                    $total = $barang->harga * $item['jumlah'];

                    // Kurangi stok
                    $barang->stok -= $item['jumlah'];
                    $barang->save();

                    // Buat record transaksi untuk setiap item
                    Transaksi::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'total_harga' => $total,
                    ]);
                }
            });

            // Di dalam method store()
        return back()->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            // Tangkap error (misalnya stok tidak cukup) dan kembalikan dengan pesan error
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        //
    }
}
