<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class BarangAkanKadaluarsaNotification extends Notification
{
    use Queueable;

    protected $barangs;

    public function __construct(Collection $barangs)
    {
        $this->barangs = $barangs;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // Kirim via email
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                    ->subject('Peringatan: Ada Barang Akan Kadaluarsa!')
                    ->greeting('Halo Admin,')
                    ->line('Sistem mendeteksi beberapa barang yang akan atau sudah melewati tanggal kadaluarsa. Mohon untuk segera ditindaklanjuti.');

        foreach ($this->barangs as $barang) {
            $mail->line('-> ' . $barang->nama_barang . ' (Kadaluarsa pada: ' . \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d F Y') . ')');
        }

        $mail->action('Lihat Daftar Barang', route('barangs.index'));

        return $mail;
    }
}
