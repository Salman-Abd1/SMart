<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\User;
use App\Notifications\BarangAkanKadaluarsaNotification; // Kita akan buat ini
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class CekKadaluarsaHarian extends Command
{
    protected $signature = 'app:cek-kadaluarsa-harian';
    protected $description = 'Cek barang yang akan kadaluarsa dan kirim notifikasi.';

    public function handle()
    {
        $this->info('Mengecek barang yang akan kadaluarsa...');

        $tanggalPeringatan = Carbon::now()->addDays(30); // Peringatan untuk 30 hari ke depan

        $barangAkanKadaluarsa = Barang::whereNotNull('tanggal_kadaluarsa')
                                    ->where('tanggal_kadaluarsa', '<=', $tanggalPeringatan)
                                    ->get();

        if ($barangAkanKadaluarsa->isNotEmpty()) {
            $admin = User::where('role', 'admin')->first(); // Ambil admin pertama

            if ($admin) {
                Notification::send($admin, new BarangAkanKadaluarsaNotification($barangAkanKadaluarsa));
                $this->info('Notifikasi barang akan kadaluarsa berhasil dikirim.');
            }
        } else {
            $this->info('Tidak ada barang yang mendekati tanggal kadaluarsa.');
        }
    }
}
