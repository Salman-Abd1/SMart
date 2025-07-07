<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\User; // Asumsi Anda punya model User
use App\Notifications\StokHampirHabisNotification;
use Illuminate\Support\Facades\Notification;

class CekStokHarian extends Command
{
    protected $signature = 'app:cek-stok-harian';
    protected $description = 'Cek stok barang harian dan kirim notifikasi jika ada yang hampir habis';

    public function handle()
    {
        $this->info('Mengecek stok barang...');

        $barangHampirHabis = Barang::where('stok', '<=', 10)->get();

        if ($barangHampirHabis->isNotEmpty()) {
            // Ganti dengan cara Anda mendapatkan admin
            // Contoh: ambil user pertama atau user dengan role admin
            $admin = User::find(5); // Misalnya admin adalah user dengan ID 1

            if ($admin) {
                Notification::send($admin, new StokHampirHabisNotification($barangHampirHabis));
                $this->info('Notifikasi stok rendah berhasil dikirim ke ' . $admin->email);
            } else {
                $this->error('Admin tidak ditemukan. Notifikasi gagal dikirim.');
            }
        } else {
            $this->info('Tidak ada barang dengan stok rendah.');
        }

        return 0;
    }
}
