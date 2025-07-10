<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;

class StockHistoryController extends Controller
{
    public function index()
    {
        $history = StockHistory::with(['barang', 'user']) // Load relasi barang dan user
                                ->latest() // Urutkan dari terbaru
                                ->paginate(15); // Tambahkan paginasi

        return view('stock_history.index', compact('history'));
    }
}
