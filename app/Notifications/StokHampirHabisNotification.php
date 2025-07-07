<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection; // Import Collection

class StokHampirHabisNotification extends Notification
{
    use Queueable;

    protected $barangHampirHabis;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $barangHampirHabis)
    {
        $this->barangHampirHabis = $barangHampirHabis;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Kita mulai dengan email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
                    ->subject('Peringatan: Stok Barang Hampir Habis')
                    ->greeting('Halo Admin,')
                    ->line('Sistem mendeteksi beberapa barang dengan stok yang perlu segera diisi ulang.');

        foreach ($this->barangHampirHabis as $barang) {
            $mailMessage->line('• ' . $barang->nama_barang . ' - Sisa stok: ' . $barang->stok . ' unit.');
        }

        $mailMessage->action('Lihat Dashboard', url('/dashboard'))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');

        return $mailMessage;
    }
}
