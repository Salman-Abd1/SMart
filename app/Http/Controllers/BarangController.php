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
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|unique:barangs,kode_barang',
            'stok' => 'required|integer',
            'minimal_stok' => 'required|integer',
            'harga' => 'required|numeric',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);

        Barang::create($request->all());

        return redirect()->route('barangs.index')->with('success', 'Barang baru berhasil ditambahkan.');
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
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|unique:barangs,kode_barang,' . $barang->id,
            'stok' => 'required|integer',
            'minimal_stok' => 'required|integer',
            'harga' => 'required|numeric',
            'tanggal_kadaluarsa' => 'nullable|date',
        ]);

        $barang->update($request->all());

        return redirect()->route('barangs.index')->with('success', 'Data barang berhasil diperbarui.');
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
