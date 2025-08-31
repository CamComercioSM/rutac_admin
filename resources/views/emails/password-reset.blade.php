<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - Ruta C</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            padding: 40px 20px 30px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(34, 197, 94, 0.1) 100%);
        }
        .logo-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 30px;
            margin-bottom: 25px;
            flex-wrap: nowrap;
        }
        .logo {
            width: 180px;
            height: 100px;
            background-color: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .brand-slogan {
            color: #ffffff;
            text-align: left;
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            opacity: 0.9;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .header-title {
            color: #ffffff;
            font-size: 32px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 25px;
        }
        .description {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #fcb716 0%, #f59e0b 100%);
            color: #000000;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(252, 183, 22, 0.4);
            transition: all 0.3s ease;
            border: 2px solid #000000;
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(252, 183, 22, 0.6);
        }
        .link-fallback {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            position: relative;
        }
        .link-fallback p {
            margin: 0 0 20px 0;
            color: #495057;
            font-size: 16px;
            font-weight: 600;
        }
        .link-fallback .url-container {
            background-color: #ffffff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .link-fallback .url-text {
            color: #1e3a8a;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        .warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .warning h4 {
            margin: 0 0 15px 0;
            color: #92400e;
            font-size: 18px;
            font-weight: 600;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 15px;
            line-height: 1.6;
        }
        .support {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .support h4 {
            margin: 0 0 15px 0;
            color: #0c4a6e;
            font-size: 18px;
        }
        .support p {
            margin: 0 0 10px 0;
            color: #0c4a6e;
            font-size: 14px;
        }
        .support-email {
            background-color: #ffffff;
            color: #0ea5e9;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .footer {
            background-color: #1e3a8a;
            color: #ffffff;
            text-align: center;
            padding: 25px 30px;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
            color: #cbd5e1;
        }
        .footer .copyright {
            color: #94a3b8;
            font-size: 12px;
        }
        .growth-arrow {
            position: absolute;
            top: 20px;
            right: 30px;
            width: 40px;
            height: 40px;
            opacity: 0.3;
        }
        .growth-arrow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, #fcb716, #f59e0b);
        }
        .growth-arrow::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 12px solid #fcb716;
        }
        @media (max-width: 600px) {
            .content {
                padding: 25px 20px;
            }
            .header {
                padding: 20px 15px;
            }
            .logo-container {
                flex-direction: column;
                gap: 20px;
            }
            .logo {
                width: 160px;
                height: 90px;
            }
            .brand-slogan {
                font-size: 18px;
                text-align: center;
            }
            .header-title {
                font-size: 24px;
            }
            .reset-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="growth-arrow"></div>
            <div class="logo-container">
                <div class="logo">
                    <img src="https://cdnsicam.net/img/rutac/rutac_blanco.png" alt="Ruta C Logo">
                </div>
                <p class="brand-slogan">Haz crecer tu negocio</p>
            </div>
            <h2 class="header-title">Recuperación de Contraseña</h2>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hola <strong>{{ $user_name }}</strong>,</p>
            </div>
            
            <div class="description">
                <p>Has solicitado restablecer tu contraseña en <strong>{{ $project_name }}</strong>.</p>
                
                <p>Para continuar con el proceso de recuperación, haz clic en el siguiente botón:</p>
            </div>
            
            <div class="button-container">
                <a href="{{ $reset_url }}" class="reset-button">Restablecer Contraseña</a>
            </div>
            
            <div class="link-fallback">
                <p><strong>¿El botón no funciona?</strong></p>
                <div class="url-container">
                    <span class="url-text">{{ $reset_url }}</span>
                </div>
            </div>
            
            <div class="warning">
                <h4>Importante</h4>
                <p>Este enlace expirará el <strong>{{ $expires_at }}</strong>. Si no has solicitado este cambio, puedes ignorar este correo de forma segura.</p>
            </div>
        </div>

        <div class="support">
            <h4>¿Necesitas ayuda?</h4>
            <p>Si tienes problemas para acceder a tu cuenta, contacta a nuestro equipo de soporte:</p>
            <a href="mailto:{{ $support_email }}" class="support-email">{{ $support_email }}</a>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p class="copyright">&copy; {{ $current_year }} {{ $project_name }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
