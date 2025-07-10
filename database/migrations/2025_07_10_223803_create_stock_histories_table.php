<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained()->onDelete('cascade'); // Barang yang stoknya berubah
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Siapa yang mengubah (bisa null jika otomatis)
            $table->integer('old_stock'); // Stok sebelum perubahan
            $table->integer('new_stock'); // Stok setelah perubahan
            $table->integer('change_quantity'); // Jumlah perubahan (+/-)
            $table->string('reason'); // Alasan perubahan (misalnya: penjualan, penyesuaian manual, penerimaan)
            $table->string('reference_type')->nullable(); // Opsional: model terkait (misal: Transaksi, StockAdjustment)
            $table->unsignedBigInteger('reference_id')->nullable(); // Opsional: ID model terkait
            $table->timestamps(); // Kapan perubahan terjadi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
