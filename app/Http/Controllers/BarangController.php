<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     */
    public function index()
    {
        $barangs = Barang::latest()->get(); // Urutkan dari yang terbaru
        return view('barangs.index', compact('barangs'));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        return view('barangs.create');
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs',
            'stok' => 'required|integer|min:0', // Stok bisa 0 saat awal
            'harga' => 'required|numeric',
            'minimal_stok' => 'required|integer|min:0', // Kolom baru
            'tanggal_kadaluarsa' => 'nullable|date', // Kolom baru
        ]);

        $barang = Barang::create($request->only([
            'nama_barang', 'kode_barang', 'stok', 'harga', 'minimal_stok', 'tanggal_kadaluarsa'
        ]));

        // CATAT PERUBAHAN STOK (Stok awal)
        \App\Models\StockHistory::create([
            'barang_id' => $barang->id,
            'user_id' => auth()->id(),
            'old_stock' => 0, // Stok awal biasanya 0 sebelum diisi
            'new_stock' => $barang->stok,
            'change_quantity' => $barang->stok,
            'reason' => 'Penerimaan Awal / Input Barang Baru',
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan.');
    }
    /**
     * Menampilkan form untuk mengedit barang. (BARU)
     */
    public function edit(Barang $barang)
    {
        return view('barangs.edit', compact('barang'));
    }

    /**
     * Memperbarui data barang di database. (BARU)
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric',
            'minimal_stok' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);

        $oldStock = $barang->stok; // Simpan stok lama
        $changeQuantity = $request->stok - $oldStock; // Hitung perubahan

        $barang->update($request->only([
            'nama_barang', 'kode_barang', 'stok', 'harga', 'minimal_stok', 'tanggal_kadaluarsa'
        ]));

        // CATAT PERUBAHAN STOK (Penyesuaian manual)
        if ($changeQuantity !== 0) { // Hanya catat jika ada perubahan stok
            \App\Models\StockHistory::create([
                'barang_id' => $barang->id,
                'user_id' => auth()->id(),
                'old_stock' => $oldStock,
                'new_stock' => $barang->stok,
                'change_quantity' => $changeQuantity,
                'reason' => 'Penyesuaian Manual',
            ]);
        }

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang dari database. (BARU)
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}
