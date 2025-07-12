<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_transaction_id')->constrained('incoming_transactions')->onDelete('cascade'); // Foreign key ke transaksi masuk
            $table->foreignId('barang_id')->constrained()->onDelete('cascade'); // Barang yang diterima
            $table->integer('quantity'); // Jumlah barang yang diterima
            $table->decimal('unit_cost', 10, 2)->nullable(); // Harga beli per unit (opsional)
            $table->decimal('sub_total', 10, 2)->default(0); // Total harga untuk item ini (quantity * unit_cost)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_transaction_items');
    }
};
