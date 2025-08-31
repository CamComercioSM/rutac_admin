# API de Correos Electrónicos - Documentación

## Descripción General
Esta API permite a otros proyectos enviar correos electrónicos a través de nuestro sistema, incluyendo funcionalidades para recuperación de contraseñas y envío de correos personalizados.

## Base URL
```
http://tu-dominio.com/api/email
```

## Endpoints

### 1. Recuperación de Contraseña
**POST** `/api/email/password-reset`

Envía un correo de recuperación de contraseña con un enlace personalizado.

#### Parámetros de Entrada
```json
{
    "email": "usuario@ejemplo.com",
    "reset_url": "https://tu-proyecto.com/reset-password?token=abc123",
    "user_name": "Juan Pérez",
    "project_name": "Mi Aplicación"
}
```

#### Parámetros Requeridos
- `email`: Correo electrónico del usuario
- `reset_url`: URL donde el usuario puede restablecer su contraseña

#### Parámetros Opcionales
- `user_name`: Nombre del usuario (por defecto: "Usuario")
- `project_name`: Nombre del proyecto (por defecto: "Sistema")

#### Respuesta Exitosa
```json
{
    "success": true,
    "message": "Correo de recuperación enviado exitosamente",
    "data": {
        "email": "usuario@ejemplo.com",
        "expires_at": "2024-01-15 14:30:00"
    }
}
```

#### Respuesta de Error
```json
{
    "success": false,
    "message": "Datos de entrada inválidos",
    "errors": {
        "email": ["El correo electrónico es requerido."]
    }
}
```

### 2. Correo Personalizado
**POST** `/api/email/custom`

Envía un correo personalizado usando un template específico.

#### Parámetros de Entrada
```json
{
    "to": "destinatario@ejemplo.com",
    "subject": "Asunto del correo",
    "template": "emails.welcome",
    "data": {
        "user_name": "Juan Pérez",
        "welcome_message": "¡Bienvenido a nuestra plataforma!"
    },
    "cc": ["copia@ejemplo.com"],
    "bcc": ["oculta@ejemplo.com"]
}
```

#### Parámetros Requeridos
- `to`: Correo electrónico del destinatario
- `subject`: Asunto del correo
- `template`: Nombre del template Blade a usar
- `data`: Datos para el template

#### Parámetros Opcionales
- `cc`: Array de correos en copia
- `bcc`: Array de correos en copia oculta

### 3. Verificación de Estado
**GET** `/api/email/health`

Verifica el estado del servicio de correo.

#### Respuesta
```json
{
    "success": true,
    "message": "Servicio de correo funcionando correctamente",
    "data": {
        "driver": "smtp",
        "from_address": "noreply@tuempresa.com",
        "from_name": "Tu Empresa",
        "timestamp": "2024-01-15 14:30:00"
    }
}
```

## Autenticación

### Opción 1: Clave de API (Recomendado)
Incluye la clave de API en el header de la petición:

```http
X-API-Key: tu_clave_api_aqui
```

### Opción 2: Header Authorization
```http
Authorization: tu_clave_api_aqui
```

## Configuración

### 1. Variables de Entorno
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

### 2. Configuración de la Aplicación
En `config/app.php`, agrega:

```php
'api_key' => env('API_KEY', null),
```

## Ejemplos de Uso

### Ejemplo 1: Recuperación de Contraseña
```bash
curl -X POST http://tu-dominio.com/api/email/password-reset \
  -H "Content-Type: application/json" \
  -H "X-API-Key: tu_clave_api" \
  -d '{
    "email": "usuario@ejemplo.com",
    "reset_url": "https://miapp.com/reset?token=abc123",
    "user_name": "Juan Pérez",
    "project_name": "Mi Aplicación"
  }'
```

### Ejemplo 2: Correo Personalizado
```bash
curl -X POST http://tu-dominio.com/api/email/custom \
  -H "Content-Type: application/json" \
  -H "X-API-Key: tu_clave_api" \
  -d '{
    "to": "cliente@ejemplo.com",
    "subject": "Bienvenido a nuestra plataforma",
    "template": "emails.welcome",
    "data": {
      "user_name": "María García",
      "company_name": "Empresa XYZ"
    }
  }'
```

### Ejemplo 3: Verificación de Estado
```bash
curl -X GET http://tu-dominio.com/api/email/health \
  -H "X-API-Key: tu_clave_api"
```

## Códigos de Estado HTTP

- `200 OK`: Petición exitosa
- `400 Bad Request`: Datos de entrada inválidos
- `401 Unauthorized`: Clave de API inválida o faltante
- `500 Internal Server Error`: Error interno del servidor

## Logs y Monitoreo

La API registra todas las operaciones en los logs de Laravel:

- **Logs de éxito**: Información sobre correos enviados exitosamente
- **Logs de error**: Detalles de errores con stack traces
- **Logs de validación**: Errores de validación de datos de entrada

## Seguridad

### Recomendaciones
1. **Usa HTTPS**: Siempre usa conexiones seguras
2. **Clave de API**: Implementa autenticación con clave de API
3. **Rate Limiting**: Considera implementar límites de tasa de envío
4. **Validación**: Valida todos los datos de entrada
5. **Logs**: Mantén logs detallados para auditoría

### Rate Limiting (Opcional)
Para implementar límites de tasa, puedes usar el middleware de Laravel:

```php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/password-reset', [EmailController::class, 'sendPasswordReset']);
    Route::post('/custom', [EmailController::class, 'sendCustomEmail']);
});
```

## Soporte

Para soporte técnico o preguntas sobre la API, contacta a:
- **Email**: soporte@tuempresa.com
- **Documentación**: [URL de tu documentación]

## Versión
- **Versión actual**: 1.0.0
- **Última actualización**: Enero 2024
- **Compatibilidad**: Laravel 8.x, 9.x, 10.x
