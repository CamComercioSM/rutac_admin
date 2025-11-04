<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Traits\UserTrait;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword, UserTrait;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'identification',
        'name',
        'lastname',
        'position',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'rol_id',
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function unidades()
    {
        return $this->hasMany(UnidadProductiva::class, 'user_id');
    }


    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Usar el host de la aplicación pública para el enlace de recuperación
        $publicHost = 'https://app.rutadecrecimiento.com';
        $resetUrl = $publicHost . '/password/reset?token=' . $token . '&email=' . urlencode($this->email);

        // Preparar nombre del usuario
        $userName = trim(($this->name ?? '') . ' ' . ($this->lastname ?? '')) ?: 'Usuario';
        
        // Enviar el correo usando el template personalizado
        try {
            $mailService = app(\App\Services\MailService::class);
            $mailService->sendPasswordReset(
                $this->email,
                $userName,
                $resetUrl,
                'Ruta de Crecimiento'
            );
            
            \Illuminate\Support\Facades\Log::info('Correo de recuperación enviado con template personalizado', [
                'user_id' => $this->id,
                'email' => $this->email
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al enviar correo de recuperación con template personalizado', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            // Si falla el envío personalizado, usar la notificación estándar como fallback
            $this->notify(new \App\Notifications\ResetPasswordNotification($token));
        }
    }
}
