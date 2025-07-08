<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // app/Models/Barang.php
protected $fillable = [
    'nama_barang',
    'kode_barang',
    'stok',
    'minimal_stok', // Tambahkan ini
    'harga',
    'tanggal_kadaluarsa', // Tambahkan ini
];
}
