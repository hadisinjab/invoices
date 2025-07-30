<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('تأكيد بريدك الإلكتروني - نظام إدارة الفواتير')
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('شكراً لك على التسجيل في نظام إدارة الفواتير.')
            ->line('يرجى النقر على الزر أدناه لتأكيد بريدك الإلكتروني:')
            ->action('تأكيد البريد الإلكتروني', $verificationUrl)
            ->line('إذا لم تقم بإنشاء حساب، فلا داعي لاتخاذ أي إجراء آخر.')
            ->line('هذا الرابط صالح لمدة 60 دقيقة فقط.')
            ->salutation('مع تحيات فريق نظام إدارة الفواتير');
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
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
