<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\IncomingTransactionItem;
use App\Models\User;

class IncomingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'reference_number',
        'notes',
        'user_id',
        'total_amount',
    ];

    // Relasi ke user yang mencatat transaksi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke item-item dalam transaksi masuk ini
    public function items(): HasMany
    {
        return $this->hasMany(IncomingTransactionItem::class);
    }
}
