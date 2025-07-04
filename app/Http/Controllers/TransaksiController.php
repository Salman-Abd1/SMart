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
    $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'jumlah' => 'required|integer|min:1',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);

            if ($barang->stok < $request->jumlah) {
                // Melempar exception akan otomatis membatalkan transaksi
                throw new \Exception('Stok tidak cukup');
            }

            $total = $barang->harga * $request->jumlah;

            // Kurangi stok
            $barang->stok -= $request->jumlah;
            $barang->save();

            // Buat record transaksi
            Transaksi::create([
                'barang_id' => $barang->id,
                'jumlah' => $request->jumlah,
                'total_harga' => $total,
            ]);
        });

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil disimpan.');

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
