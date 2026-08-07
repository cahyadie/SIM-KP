<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MagangNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $jenis,
        public string $pesan,
        public string $url,
        public string $icon = 'bi-bell',
        public ?int $magangId = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'jenis' => $this->jenis,
            'pesan' => $this->pesan,
            'url' => $this->url,
            'icon' => $this->icon,
            'magang_id' => $this->magangId,
        ];
    }
}
