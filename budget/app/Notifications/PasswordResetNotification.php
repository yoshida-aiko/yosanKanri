<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
//use Illuminate\Notifications\Notification;
/*use Illuminate\Notifications\ResetPassword;*/
use Illuminate\Auth\Notifications\ResetPassword;

class PasswordResetNotification extends ResetPassword
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
                    /*->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');*/


                    ->subject('【'.config('app.name').'】パスワード再設定')
                    ->line('下のボタンをクリックしてパスワードを再設定してください。')
                    ->action('パスワード再設定',url(config('app.url').route('password.reset', $this->token, false)))
                    ->line('もし心当たりがない場合は、本メッセージは破棄してください。');

                    /*->view('emails.password_reset')
                    ->action('パスワード再設定', url(config('app.url').route('password.reset', $this->token, false)))
                    */

                    /*->line('下のボタンをクリックしてパスワードを再設定してください。')
                    ->action('パスワード再設定', url(config('app.url').route('password.reset', $this->token, false)))
                    ->line('もし心当たりがない場合は、本メッセージは破棄してください。');*/
                    /*->view('emails.password_reset', [
                        'reset_url' => url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false))
                    ]);*/
    }

}
