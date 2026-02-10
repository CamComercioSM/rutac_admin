-- SQL para crear la tabla de log de mensajes WhatsApp desde unidades productivas
-- Ejecutar manualmente si prefieres no usar migraciones de Laravel

CREATE TABLE unidad_productiva_whatsapp_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unidadproductiva_id INT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    phone VARCHAR(20) NOT NULL,
    phone_type VARCHAR(30) NULL COMMENT 'mobile, telephone, contact_phone',
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, sent, failed',
    provider_response JSON NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_unidadproductiva_id (unidadproductiva_id),
    INDEX idx_user_id (user_id)
);
