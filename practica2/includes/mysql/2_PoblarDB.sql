/*
  Se debe deshabilitar la opción "Enable foreign key checks" para evitar problemas a la hora de importar el script.
*/
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar datos antiguos para evitar duplicados si se relanza
TRUNCATE TABLE `productos_imagenes`;
TRUNCATE TABLE `lineas_pedido`;
TRUNCATE TABLE `pedidos`;
TRUNCATE TABLE `productos`;
TRUNCATE TABLE `categorias`;
TRUNCATE TABLE `usuarios`;

-- Inserción de Usuarios
INSERT INTO `usuarios` (`id`, `nombre_usuario`, `email`, `nombre`, `apellidos`, `password`, `rol`, `avatar`) VALUES
(1, 'admin', 'admin@bistrofdi.es', 'Admin', '', '$2y$10$QOUxomzkP/RTr4EhHP8OVOx.9TiIbztfx1AOTKRma72u.dAvyLlvO', 'gerente', 'admin.png'),
(2, 'cliente', 'cliente@ucm.es', 'Cliente', '', '$2y$10$2gsbg4/807iDyWG4b6il0uxVFIlP.eaKovNxfqnoTcLhlXvSE1tNy', 'cliente', 'base/base4.png'),
(3, 'camarero', 'camarero@bistrofdi.es', 'Camarero', '', '$2y$10$Bv5eepK6aWZn53kDdw8Zvu01W99eB4whkjAWWJ1xaeNmnb9wwklz2', 'camarero', 'camarero.png'),
(4, 'cocinero', 'cocinero@bistrofdi.es', 'Cocinero', '', '$2y$10$Bc6aW8U1mi62WxM3QYSU/umpiSiMikYpsaubnCYOlDYS8hJEI.SJS', 'cocinero', 'cocinero.png'),
(5, 'ramon', 'rsalaz01@ucm.es', 'Ramon', 'Salazar', '$2y$10$tpTHTrPSOaDG1P94s5.Uq.dfwmUWF6Efy3f7/c8JTxiTDRzLAojQ2', 'cliente', '1772146880_Logo.png');

-- Inserción de Categorías
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen`, `activa`) VALUES
(7, 'Hamburguesas', 'Jugosas hamburguesas elaboradas con ingredientes frescos...', 'cat_69b2e63f080a0.png', 1),
(8, 'Pasta', 'Deliciosas pastas artesanales...', 'cat_69b2c1a38169c.avif', 1),
(9, 'Ensaladas', 'Frescas y coloridas...', 'cat_69b2c4c225362.jpg', 1),
(10, 'Carnes', 'Cortes jugosos y llenos de sabor...', 'cat_69b30204e8ce1.jpg', 1);

-- Inserción de Productos
INSERT INTO `productos` (`id`, `id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`) VALUES
(5, 9, 'Ensalada César', 'Clásica ensalada con lechuga...', 11.00, 10, 1, 1),
(9, 8, 'Pasta Carbonara', 'Pasta cremosa con bacon...', 13.00, 10, 1, 1),
(10, 8, 'Pasta Boloñesa', 'Pasta acompañada de una rica salsa...', 10.00, 10, 1, 1);

-- Inserción de Imágenes de Productos
INSERT INTO `productos_imagenes` (`id`, `id_producto`, `ruta_imagen`, `orden`) VALUES
(45, 5, 'prod_69b341743d18d_0.avif', 1),
(46, 9, 'prod_69b3417d031f5_0.jpg', 1),
(47, 9, 'prod_69b3417d0373e_1.jpeg', 2),
(48, 9, 'prod_69b3417d03815_2.jpg', 3),
(49, 10, 'prod_69b349ada55c7_0.webp', 1),
(50, 10, 'prod_69b349ada5799_1.jpg', 2),
(51, 10, 'prod_69b349ada5848_2.jpg', 3);

SET FOREIGN_KEY_CHECKS = 1;