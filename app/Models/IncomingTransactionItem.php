<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_transaction_id',
        'barang_id',
        'quantity',
        'unit_cost',
        'sub_total',
    ];

    // Relasi ke transaksi masuk induk
    public function incomingTransaction(): BelongsTo
    {
        return $this->belongsTo(IncomingTransaction::class);
    }

    // Relasi ke barang yang diterima
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
