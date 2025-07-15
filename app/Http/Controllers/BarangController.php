<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     */
    public function index()
    {
        // Menggunakan with('category') untuk eager loading
        $barangs = Barang::with('category')->latest()->get();
        return view('barangs.index', compact('barangs'));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        // Mengambil semua kategori untuk ditampilkan di dropdown
        $categories = Category::orderBy('name')->get();
        return view('barangs.create', compact('categories'));
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric',
            'minimal_stok' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);

        // Menggunakan $request->all() karena semua input sudah divalidasi
        $barang = Barang::create($request->all());

        // CATAT PERUBAHAN STOK (Stok awal)
        \App\Models\StockHistory::create([
            'barang_id' => $barang->id,
            'user_id' => auth()->id(),
            'old_stock' => 0,
            'new_stock' => $barang->stok,
            'change_quantity' => $barang->stok,
            'reason' => 'Penerimaan Awal / Input Barang Baru',
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        // Mengambil semua kategori untuk ditampilkan di dropdown
        $categories = Category::orderBy('name')->get();
        return view('barangs.edit', compact('barang', 'categories'));
    }

    /**
     * Memperbarui data barang di database.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric',
            'minimal_stok' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);

        $oldStock = $barang->stok;
        $changeQuantity = $request->stok - $oldStock;

        // Menggunakan $request->all() untuk memperbarui semua data yang divalidasi
        $barang->update($request->all());

        // CATAT PERUBAHAN STOK (Penyesuaian manual)
        if ($changeQuantity !== 0) {
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
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}
