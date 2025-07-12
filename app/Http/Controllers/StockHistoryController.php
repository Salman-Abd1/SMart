<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;

class StockHistoryController extends Controller
{
    public function index()
    {
        $history = StockHistory::with(['barang', 'user'])
                                ->latest()
                                ->paginate(15);

        return view('stock_history.index', compact('history'));
    }
}
