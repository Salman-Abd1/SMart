<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom 'harga'
            $table->date('tanggal_kadaluarsa')->nullable()->after('harga');
            $table->integer('minimal_stok')->default(10)->after('stok');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('tanggal_kadaluarsa');
            $table->dropColumn('minimal_stok');
        });
    }
};
