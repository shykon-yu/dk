<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InboundCreateNotify extends Notification
{
    use Queueable;
    protected $inbound;
    public function __construct($inbound)
    {
        $this->inbound = $inbound;
    }

    public function via($notifiable)
    {
        //return ['mail'];
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => '新入库单通知',
            'content' => "入库单号：{$this->inbound->inbound_code} 已成功录入",
            'id'      => $this->inbound->id,
            'type'    => 'inbound',
        ];
    }
}
