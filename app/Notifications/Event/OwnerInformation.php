<?php
namespace App\Notifications\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OwnerInformation extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
      return (new MailMessage)
        ->from(env('MAIL_FROM_ADDRESS'))
        ->replyTo(env('MAIL_REPLY_TO_ADDRESS'))
        ->subject('Neue Anmeldung: ' . $this->data['title'])
        ->markdown('notifications.event.owner-information', ['data' => $this->data]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}