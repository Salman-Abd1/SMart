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
                foreach ($request->items as $itemData) {
                    $barang = Barang::findOrFail($itemData['id']);
                    $oldStock = $barang->stok; // Simpan stok lama

                    // ... cek stok ...

                    $total = $barang->harga * $itemData['quantity'];
                    $changeQuantity = -$itemData['quantity']; // Perubahan negatif karena keluar

                    $barang->stok += $changeQuantity; // Stok baru
                    $barang->save();

                    // CATAT PERUBAHAN STOK
                    \App\Models\StockHistory::create([
                        'barang_id' => $barang->id,
                        'user_id' => auth()->id(), // User yang sedang login
                        'old_stock' => $oldStock,
                        'new_stock' => $barang->stok,
                        'change_quantity' => $changeQuantity,
                        'reason' => 'Penjualan',
                        'reference_type' => \App\Models\Transaksi::class, // Opsional: Tipe model referensi
                        'reference_id' => null, // Akan diisi setelah Transaksi dibuat jika Transaksi ID diperlukan
                    ]);

                    // Buat record transaksi
                    $transaksi = Transaksi::create([ // Simpan ke variabel untuk mendapatkan ID
                        'barang_id' => $barang->id,
                        'jumlah' => $itemData['quantity'],
                        'total_harga' => $total,
                    ]);

                    // Update reference_id di StockHistory jika diperlukan
                    \App\Models\StockHistory::where('barang_id', $barang->id)
                                            ->where('user_id', auth()->id())
                                            ->where('reason', 'Penjualan')
                                            ->latest() // Ambil yang terakhir dibuat
                                            ->first()
                                            ->update(['reference_id' => $transaksi->id]);
                }
            });

            // ... return success ...
        } catch (\Exception $e) {
            // ... return error ...
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
