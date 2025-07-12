<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang; // Import model Barang
use App\Models\IncomingTransaction; // Import model IncomingTransaction
use App\Models\IncomingTransactionItem; // Import model IncomingTransactionItem
use Illuminate\Support\Facades\DB; // Import DB facade for transactions

class IncomingTransactionController extends Controller
{

    /**
     * Menampilkan daftar transaksi masuk.
     */
    public function index()
    {
        $incomingTransactions = IncomingTransaction::with('user')
                                                 ->latest()
                                                 ->paginate(10); // Ambil 10 per halaman, terbaru dulu

        return view('incoming_transactions.index', compact('incomingTransactions'));
    }

    /**
     * Menampilkan formulir untuk membuat transaksi masuk baru.
     */
    public function create()
    {
        $barangs = Barang::all(); // Ambil semua data barang untuk dropdown

        return view('incoming_transactions.create', compact('barangs'));
    }

    /**
     * Menyimpan transaksi masuk yang baru dibuat ke storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalAmount = 0;

                // Buat transaksi masuk utama
                $incomingTransaction = IncomingTransaction::create([
                    'supplier_name' => $request->supplier_name,
                    'reference_number' => $request->reference_number,
                    'notes' => $request->notes,
                    'user_id' => auth()->id(), // User yang sedang login
                    'total_amount' => 0, // Akan diupdate setelah semua item diproses
                ]);

                foreach ($request->items as $itemData) {
                    $barang = Barang::findOrFail($itemData['barang_id']);
                    $oldStock = $barang->stok; // Simpan stok lama

                    $quantity = $itemData['quantity'];
                    $unitCost = $itemData['unit_cost'] ?? 0;
                    $subTotal = $quantity * $unitCost;

                    // Tambah stok barang
                    $barang->stok += $quantity;
                    $barang->save();

                    // Catat perubahan stok di history
                    \App\Models\StockHistory::create([
                        'barang_id' => $barang->id,
                        'user_id' => auth()->id(),
                        'old_stock' => $oldStock,
                        'new_stock' => $barang->stok,
                        'change_quantity' => $quantity, // Perubahan positif
                        'reason' => 'Penerimaan Barang',
                        'reference_type' => IncomingTransaction::class,
                        'reference_id' => $incomingTransaction->id,
                    ]);

                    // Buat detail item transaksi masuk
                    IncomingTransactionItem::create([
                        'incoming_transaction_id' => $incomingTransaction->id,
                        'barang_id' => $barang->id,
                        'quantity' => $quantity,
                        'unit_cost' => $unitCost,
                        'sub_total' => $subTotal,
                    ]);

                    $totalAmount += $subTotal;
                }

                // Update total_amount pada transaksi masuk utama setelah semua item diproses
                $incomingTransaction->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('incoming_transactions.index')->with('success', 'Transaksi masuk berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan transaksi masuk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail transaksi masuk tertentu.
     */
    public function show(IncomingTransaction $incomingTransaction)
    {
        // Muat relasi item-item dan barang terkait untuk tampilan detail
        $incomingTransaction->load('items.barang', 'user');

        return view('incoming_transactions.show', compact('incomingTransaction'));
    }
}
