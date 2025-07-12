<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name'); // Nama supplier
            $table->string('reference_number')->nullable(); // Nomor referensi/invoice dari supplier
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang mencatat transaksi masuk
            $table->decimal('total_amount', 10, 2)->default(0); // Total nilai transaksi masuk (opsional, bisa dihitung dari item)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_transactions');
    }
};
