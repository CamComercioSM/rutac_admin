<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
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
     * Esta notificación solo se usa como fallback si falla el envío personalizado.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Usar el host de la aplicación pública para el enlace de recuperación
        $publicHost = 'https://app.rutadecrecimiento.com';
        $resetUrl = $publicHost . '/password/reset?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        // Preparar nombre del usuario
        $userName = trim(($notifiable->name ?? '') . ' ' . ($notifiable->lastname ?? '')) ?: 'Usuario';
        
        return (new MailMessage)
            ->subject('Restablecer contraseña - Ruta C')
            ->greeting('Hola ' . $userName)
            ->line('Has solicitado restablecer tu contraseña.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line('Este enlace expirará en 24 horas.')
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.')
            ->salutation('Saludos, Equipo Ruta C');
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
