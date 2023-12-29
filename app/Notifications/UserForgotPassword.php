<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserForgotPassword extends Notification implements ShouldQueue
{
    use Queueable;

    protected $email;
    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = '/reset-password/' . $this->token;
        return (new MailMessage)
                    ->subject('Cấp lại mật khẩu Tender - Honghafeed')
                    ->line('Bạn vừa yêu cầu cấp lại mật khẩu cho ' . $this->email . '. Bạn hãy ấn nút dưới đây.')
                    ->action('Yêu cầu cấp mật khẩu', url($url))
                    ->line('Xin cảm ơn!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
