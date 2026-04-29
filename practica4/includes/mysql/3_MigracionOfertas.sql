-- =====================================================
-- Migración: Funcionalidad 4 - Gestión de Ofertas
-- Ejecutar en phpMyAdmin sobre la base de datos 'database'
-- =====================================================

-- Añadir campos a la tabla pedidos para registrar descuentos de ofertas
-- total_sin_descuento: precio original del pedido antes de aplicar ofertas
-- descuento_aplicado: cantidad total descontada por las ofertas
ALTER TABLE `pedidos` 
    ADD COLUMN `total_sin_descuento` DECIMAL(10,2) DEFAULT NULL AFTER `total`,
    ADD COLUMN `descuento_aplicado` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_sin_descuento`;
