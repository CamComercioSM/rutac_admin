# API de Correos Electrónicos - Guía de Instalación

## 🚀 Descripción
Esta API permite a otros proyectos enviar correos electrónicos a través de nuestro sistema, incluyendo funcionalidades para recuperación de contraseñas y envío de correos personalizados.

## 📋 Requisitos Previos
- Laravel 8.x, 9.x o 10.x
- PHP 8.0 o superior
- Configuración de correo electrónico (SMTP, Mailgun, etc.)
- Composer

## 🔧 Instalación

### 1. Clonar o Descargar los Archivos
Asegúrate de tener todos los archivos necesarios en tu proyecto:

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── EmailController.php
│   └── Middleware/
│       └── ApiKeyMiddleware.php
├── Services/
│   └── MailService.php
resources/
└── views/
    └── emails/
        └── password-reset.blade.php
routes/
└── api.php
```

### 2. Configurar Variables de Entorno
Agrega estas variables en tu archivo `.env`:

```env
# Clave de API (opcional, pero recomendado)
API_KEY=tu_clave_secreta_aqui

# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tuempresa.com
MAIL_FROM_NAME="Tu Empresa"
```

### 3. Configurar la Aplicación
En `config/app.php`, agrega:

```php
'api_key' => env('API_KEY', null),
```

### 4. Registrar el Middleware (Opcional)
Si quieres usar autenticación con clave de API, registra el middleware en `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... otros middlewares
    'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
];
```

### 5. Aplicar el Middleware a las Rutas (Opcional)
En `routes/api.php`, puedes proteger las rutas:

```php
Route::middleware(['api.key'])->prefix('email')->group(function () {
    Route::post('/password-reset', [EmailController::class, 'sendPasswordReset']);
    Route::post('/custom', [EmailController::class, 'sendCustomEmail']);
    Route::get('/health', [EmailController::class, 'healthCheck']);
});
```

## 🧪 Pruebas

### 1. Verificar que la API Funciona
```bash
curl -X GET http://tu-dominio.com/api/test
```

### 2. Probar Health Check
```bash
curl -X GET http://tu-dominio.com/api/email/health
```

### 3. Probar Envío de Correo de Recuperación
```bash
curl -X POST http://tu-dominio.com/api/email/password-reset \
  -H "Content-Type: application/json" \
  -H "X-API-Key: tu_clave_api" \
  -d '{
    "email": "test@ejemplo.com",
    "reset_url": "https://miapp.com/reset?token=abc123",
    "user_name": "Usuario Test",
    "project_name": "Aplicación Test"
  }'
```

## 🔐 Seguridad

### Recomendaciones de Seguridad
1. **Usa HTTPS**: Siempre usa conexiones seguras en producción
2. **Clave de API**: Implementa autenticación con clave de API
3. **Rate Limiting**: Considera implementar límites de tasa de envío
4. **Validación**: Valida todos los datos de entrada
5. **Logs**: Mantén logs detallados para auditoría

### Implementar Rate Limiting
```php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/password-reset', [EmailController::class, 'sendPasswordReset']);
    Route::post('/custom', [EmailController::class, 'sendCustomEmail']);
});
```

## 📧 Configuración de Correo

### Gmail SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=tu-dominio.com
MAILGUN_SECRET=tu-clave-secreta
```

### Amazon SES
```env
MAIL_MAILER=ses
SES_KEY=tu-access-key
SES_SECRET=tu-secret-key
SES_REGION=us-east-1
```

## 🚨 Solución de Problemas

### Error: "Class 'App\Http\Controllers\Api\EmailController' not found"
**Solución**: Verifica que el namespace sea correcto y que el archivo esté en la ubicación correcta.

### Error: "Template not found"
**Solución**: Asegúrate de que el template `emails.password-reset` existe en `resources/views/emails/password-reset.blade.php`.

### Error: "Mail configuration not found"
**Solución**: Verifica que las variables de correo estén configuradas en `.env`.

### Error: "Connection refused"
**Solución**: Verifica la configuración SMTP y que el servidor de correo esté disponible.

## 📊 Monitoreo y Logs

### Verificar Logs
```bash
tail -f storage/logs/laravel.log
```

### Logs Importantes
- **Correos enviados exitosamente**: `Correo de recuperación enviado exitosamente`
- **Errores de envío**: `Error al enviar correo de recuperación`
- **Errores de validación**: `Datos de entrada inválidos`

## 🔄 Actualizaciones

### Mantener la API Actualizada
1. **Backup**: Siempre haz backup antes de actualizar
2. **Testing**: Prueba en un entorno de desarrollo primero
3. **Versionado**: Mantén un control de versiones de la API
4. **Documentación**: Actualiza la documentación con cada cambio

## 📞 Soporte

### Contacto
- **Email**: soporte@tuempresa.com
- **Documentación**: [URL de tu documentación]
- **Issues**: [URL de tu repositorio de issues]

### Recursos Adicionales
- [Documentación de Laravel Mail](https://laravel.com/docs/mail)
- [Plantillas Blade](https://laravel.com/docs/blade)
- [API Resources](https://laravel.com/docs/eloquent-resources)

## 📝 Changelog

### Versión 1.0.0 (Enero 2024)
- ✅ API básica de envío de correos
- ✅ Recuperación de contraseñas
- ✅ Correos personalizados
- ✅ Health check del servicio
- ✅ Autenticación con clave de API
- ✅ Logs detallados
- ✅ Documentación completa
- ✅ Ejemplos de uso

## 🤝 Contribuciones

Para contribuir al proyecto:
1. Fork el repositorio
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la licencia [TU_LICENCIA]. Ver el archivo `LICENSE` para más detalles.

---

**¡Gracias por usar nuestra API de Correos Electrónicos!** 🎉
