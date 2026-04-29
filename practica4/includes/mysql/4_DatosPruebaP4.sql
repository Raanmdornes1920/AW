-- =====================================================
-- Datos de prueba adicionales para la entrega final
-- Ejecutar después de 1_CrearDB.sql, 2_PoblarDB.sql y 3_MigracionOfertas.sql
-- =====================================================

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Café con leche', 'Café espresso con leche caliente.', 1.25, 10, 1, 1, 0
FROM `categorias` c
WHERE c.nombre = 'Bebidas'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Café con leche');

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Zumo de naranja', 'Zumo natural recién exprimido.', 2.20, 10, 1, 1, 0
FROM `categorias` c
WHERE c.nombre = 'Bebidas'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Zumo de naranja');

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Tostada de aceite y tomate', 'Pan tostado con aceite de oliva y tomate rallado.', 1.30, 10, 1, 1, 1
FROM `categorias` c
WHERE c.nombre = 'Hamburguesas'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Tostada de aceite y tomate');

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Hamburguesa Bistro', 'Hamburguesa de ternera con queso, lechuga, tomate y salsa de la casa.', 9.50, 10, 1, 1, 1
FROM `categorias` c
WHERE c.nombre = 'Hamburguesas'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Hamburguesa Bistro');

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Filete de pollo', 'Filete de pollo a la plancha con guarnición.', 8.90, 10, 1, 1, 1
FROM `categorias` c
WHERE c.nombre = 'Carnes'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Filete de pollo');

INSERT INTO `productos` (`id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`)
SELECT c.id, 'Ensalada mediterránea', 'Mezcla de hojas verdes, tomate, aceitunas, atún y vinagreta.', 7.90, 10, 1, 1, 1
FROM `categorias` c
WHERE c.nombre = 'Ensaladas'
  AND NOT EXISTS (SELECT 1 FROM `productos` p WHERE p.nombre = 'Ensalada mediterránea');

INSERT INTO `ofertas` (`nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `descuento_porcentaje`)
SELECT 'Desayuno Andaluz', 'Café con leche y tostada de aceite y tomate a precio especial.', DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 21.50
WHERE NOT EXISTS (SELECT 1 FROM `ofertas` o WHERE o.nombre = 'Desayuno Andaluz');

SET @oferta_desayuno = (SELECT id FROM `ofertas` WHERE nombre = 'Desayuno Andaluz' LIMIT 1);

INSERT INTO `oferta_productos` (`id_oferta`, `id_producto`, `cantidad`)
SELECT @oferta_desayuno, p.id, 1
FROM `productos` p
WHERE p.nombre = 'Café con leche'
  AND NOT EXISTS (SELECT 1 FROM `oferta_productos` op WHERE op.id_oferta = @oferta_desayuno AND op.id_producto = p.id);

INSERT INTO `oferta_productos` (`id_oferta`, `id_producto`, `cantidad`)
SELECT @oferta_desayuno, p.id, 1
FROM `productos` p
WHERE p.nombre = 'Tostada de aceite y tomate'
  AND NOT EXISTS (SELECT 1 FROM `oferta_productos` op WHERE op.id_oferta = @oferta_desayuno AND op.id_producto = p.id);

INSERT INTO `ofertas` (`nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `descuento_porcentaje`)
SELECT 'Menú Pasta', 'Pasta Carbonara con agua para una comida rápida.', DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 15.00
WHERE NOT EXISTS (SELECT 1 FROM `ofertas` o WHERE o.nombre = 'Menú Pasta');

SET @oferta_pasta = (SELECT id FROM `ofertas` WHERE nombre = 'Menú Pasta' LIMIT 1);

INSERT INTO `oferta_productos` (`id_oferta`, `id_producto`, `cantidad`)
SELECT @oferta_pasta, p.id, 1
FROM `productos` p
WHERE p.nombre = 'Pasta Carbonara'
  AND NOT EXISTS (SELECT 1 FROM `oferta_productos` op WHERE op.id_oferta = @oferta_pasta AND op.id_producto = p.id);

INSERT INTO `oferta_productos` (`id_oferta`, `id_producto`, `cantidad`)
SELECT @oferta_pasta, p.id, 1
FROM `productos` p
WHERE p.nombre = 'Agua'
  AND NOT EXISTS (SELECT 1 FROM `oferta_productos` op WHERE op.id_oferta = @oferta_pasta AND op.id_producto = p.id);
