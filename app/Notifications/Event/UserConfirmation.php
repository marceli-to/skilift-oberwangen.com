<?php
namespace App\Notifications\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserConfirmation extends Notification
{
  use Queueable;

  protected $data;
  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $message = (new MailMessage)
      ->from(env('MAIL_FROM_ADDRESS'))
      ->replyTo(env('MAIL_REPLY_TO_ADDRESS'))
      ->subject('Anmeldung ' . $this->data['title'])
      ->markdown('notifications.event.user-confirmation', ['data' => $this->data]);

    if (!is_null($this->data['invoice'])) {
      $message->attach(public_path($this->data['invoice']));
    }

    return $message;
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
