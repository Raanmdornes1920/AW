-- =====================================================
-- Migración: Funcionalidad 4 - Gestión de Ofertas
-- Ejecutar en phpMyAdmin sobre la base de datos 'database'
-- =====================================================

CREATE TABLE IF NOT EXISTS `ofertas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE NOT NULL,
    `descuento_porcentaje` DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `oferta_productos` (
    `id_oferta` INT(11) NOT NULL,
    `id_producto` INT(11) NOT NULL,
    `cantidad` INT(11) NOT NULL,
    PRIMARY KEY (`id_oferta`, `id_producto`),
    KEY `id_producto` (`id_producto`),
    CONSTRAINT `fk_oferta_productos_oferta` FOREIGN KEY (`id_oferta`) REFERENCES `ofertas` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_oferta_productos_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Añadir campos a la tabla pedidos para registrar descuentos de ofertas
-- total_sin_descuento: precio original del pedido antes de aplicar ofertas
-- descuento_aplicado: cantidad total descontada por las ofertas
ALTER TABLE `pedidos`
    ADD COLUMN IF NOT EXISTS `total_sin_descuento` DECIMAL(10,2) DEFAULT NULL AFTER `total`,
    ADD COLUMN IF NOT EXISTS `descuento_aplicado` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_sin_descuento`;

CREATE TABLE IF NOT EXISTS `pedido_ofertas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_pedido` INT(11) NOT NULL,
    `id_oferta` INT(11) DEFAULT NULL,
    `nombre_oferta` VARCHAR(150) NOT NULL,
    `veces_aplicada` INT(11) NOT NULL DEFAULT 1,
    `descuento_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_pedido_ofertas_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pedido_ofertas_oferta` FOREIGN KEY (`id_oferta`) REFERENCES `ofertas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
