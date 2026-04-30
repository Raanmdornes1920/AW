-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 30-04-2026 a las 19:58:58
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `database`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT 'categoria_default.jpg',
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen`, `activa`) VALUES
(7, 'Hamburguesas', 'Jugosas hamburguesas elaboradas con ingredientes frescos y de calidad, acompañadas de pan artesanal y opciones de toppings para todos los gustos.', 'cat_69b2e63f080a0.png', 1),
(8, 'Pasta', 'Deliciosas pastas artesanales con salsas clásicas y creativas, preparadas al momento para una experiencia llena de sabor.', 'cat_69b2c1a38169c.avif', 1),
(9, 'Ensaladas', 'Frescas y coloridas, con ingredientes naturales y combinaciones deliciosas para una opción ligera y saludable.', 'cat_69b2c4c225362.jpg', 1),
(10, 'Carnes', 'Cortes jugosos y llenos de sabor, preparados a la parrilla o a la plancha para disfrutar de una experiencia intensa y deliciosa.', 'cat_69b30204e8ce1.jpg', 1),
(11, 'Bebidas', 'Bebidas', 'cat_69f389cc0c5f5.webp', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_pedido`
--

CREATE TABLE `lineas_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `preparado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

CREATE TABLE `ofertas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `descuento_porcentaje` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ofertas`
--

INSERT INTO `ofertas` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `descuento_porcentaje`) VALUES
(1, 'Menú healthy', 'Zumo a elegir + Ensalada César', '2026-04-30', '2026-05-30', 35.06);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oferta_productos`
--

CREATE TABLE `oferta_productos` (
  `id_oferta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oferta_productos`
--

INSERT INTO `oferta_productos` (`id_oferta`, `id_producto`, `cantidad`) VALUES
(1, 5, 1),
(1, 43, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('recibido','en_preparacion','cocinando','listo_cocina','terminado','entregado','cancelado') DEFAULT 'recibido',
  `tipo` enum('local','llevar') NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `total_sin_descuento` decimal(10,2) DEFAULT NULL,
  `descuento_aplicado` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_ofertas`
--

CREATE TABLE `pedido_ofertas` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_oferta` int(11) DEFAULT NULL,
  `nombre_oferta` varchar(150) NOT NULL,
  `veces_aplicada` int(11) NOT NULL DEFAULT 1,
  `descuento_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `iva` int(11) NOT NULL CHECK (`iva` in (4,10,21)),
  `disponible` tinyint(1) DEFAULT 1,
  `ofertado` tinyint(1) DEFAULT 1,
  `cocinable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`, `cocinable`) VALUES
(5, 9, 'Ensalada César', 'Clásica ensalada con lechuga crujiente, pollo, crutones y queso parmesano, acompañada de nuestra cremosa salsa César.', 11.00, 10, 1, 1, 1),
(9, 8, 'Pasta Carbonara', 'Pasta cremosa con bacon crujiente, queso parmesano y salsa tradicional, una receta clásica llena de sabor.', 13.00, 10, 1, 1, 1),
(10, 8, 'Pasta Boloñesa', 'Pasta acompañada de una rica salsa de carne de res cocinada a fuego lento con tomate y especias.', 10.00, 10, 1, 1, 1),
(17, 11, 'Agua', 'Botella de agua', 2.00, 10, 1, 1, 0),
(18, 11, 'Nestea', 'Lata de nestea', 1.00, 10, 1, 1, 0),
(19, 7, 'Hamburguesa BBQ', 'Hamburguesa BBQ, carne de vacuno, bacon y nuestra salsa barbacoa.', 13.00, 10, 1, 1, 1),
(20, 7, 'Hamburguesa vegana', 'Hamburguesa vegana, con salsa de remolacha.', 12.00, 10, 1, 1, 1),
(21, 10, 'Secreto ibérico', 'Con nuestra salsa de la casa.', 28.00, 10, 1, 1, 1),
(22, 11, 'Mojito', 'Nuestro mojito de la casa', 5.00, 10, 1, 1, 0),
(23, 9, 'Ensalada de pasta', 'Ensalada de pasta con tomates cherry, rúcula, queso feta y olivas.', 10.00, 10, 1, 1, 1),
(24, 10, 'Chuletillas de cordero', 'Chuletillas de cordero al horno', 25.00, 10, 1, 1, 1),
(25, 11, 'Piña colada', 'Piña colada', 5.00, 10, 1, 1, 0),
(26, 9, 'Ensalada de tomate', 'Ensalada de tomate, con aceite de oliva virgen extra y aceite de módena.', 11.00, 10, 1, 1, 1),
(27, 9, 'Ensalada de burrata', 'Ensalda de burrata.', 9.00, 10, 1, 1, 1),
(28, 9, 'Ensalada de garbanzos', 'Deliciosa ensalada de garbanzos con nuestro aderezo especial.', 9.00, 10, 1, 1, 1),
(29, 9, 'Ensalada de lentejas', 'Ensalada de lentejas', 8.00, 10, 1, 1, 1),
(30, 8, 'Pasta de pistacho', 'La pasta de su elección con nuestra salsa de pistacho', 12.00, 10, 1, 1, 1),
(31, 8, 'Rigatoni al forno', 'Rigatoni al forno', 0.00, 21, 1, 1, 1),
(32, 8, 'Gnocchi alla sorrentina', 'Gnocchi alla sorrentina', 10.00, 10, 1, 1, 1),
(33, 7, 'Hamburguesa de queso de cabra', 'Hamburguesa de queso de cabra y mermelada de bacon', 0.00, 21, 1, 1, 1),
(34, 10, 'Pluma Ibérica', 'Pluma ibérica a las finas hierbas', 30.00, 10, 1, 1, 1),
(35, 11, 'Coca Cola', 'En lata', 2.00, 10, 1, 1, 0),
(36, 10, 'Steak tartar', 'Sabroso steak tartar.', 10.00, 10, 1, 1, 1),
(37, 7, 'Chicken burger', 'Hamburguesa de pollo con mayonesa especiada', 9.00, 10, 1, 1, 1),
(38, 7, 'Cheese burger', 'Explosión de queso', 14.00, 10, 1, 1, 1),
(39, 11, 'Hamburguesa con huevo', 'Hamburguesa con huevo frito y salsa especial', 15.00, 10, 1, 1, 1),
(40, 7, 'Hamburguesa bacon crispy', 'Hamburguesa con bacon', 10.00, 10, 1, 1, 1),
(41, 8, 'Lasaña de carne', 'Lasaña de carne', 9.00, 10, 1, 1, 1),
(42, 8, 'Lasaña de berenjena', 'Lasaña de berenjena y carne picada', 10.00, 10, 1, 1, 1),
(43, 11, 'Zumo de fruta', 'Variedad de zumos', 3.00, 10, 1, 1, 0),
(44, 10, 'Pato laqueado', 'Pato laqueado pekinés', 20.00, 10, 1, 1, 1),
(45, 10, 'Mollejas', 'Mollejas', 15.00, 10, 1, 1, 1),
(46, 10, 'Rabo de toro', 'Rabo de toro con patatas panaderas.', 17.00, 21, 1, 1, 1),
(47, 10, 'Carrilleras al Pedro Ximénez', 'Carrilleras al Pedro Ximénez', 19.00, 10, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_imagenes`
--

CREATE TABLE `productos_imagenes` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_imagenes`
--

INSERT INTO `productos_imagenes` (`id`, `id_producto`, `ruta_imagen`, `orden`) VALUES
(45, 5, 'prod_69b341743d18d_0.avif', 1),
(46, 9, 'prod_69b3417d031f5_0.jpg', 1),
(47, 9, 'prod_69b3417d0373e_1.jpeg', 2),
(48, 9, 'prod_69b3417d03815_2.jpg', 3),
(49, 10, 'prod_69b349ada55c7_0.webp', 1),
(50, 10, 'prod_69b349ada5799_1.jpg', 2),
(51, 10, 'prod_69b349ada5848_2.jpg', 3),
(57, 17, 'prod_69d8c048d38f3_0.webp', 1),
(58, 17, 'prod_69d8c048d3b1a_1.webp', 2),
(60, 19, 'prod_69f387e73baac_0.jpg', 1),
(61, 19, 'prod_69f387e73bf97_1.webp', 2),
(62, 20, 'prod_69f38833b94dd_0.webp', 1),
(63, 20, 'prod_69f38833b9da7_1.avif', 2),
(64, 21, 'prod_69f388ceac62d_0.jpeg', 1),
(65, 21, 'prod_69f388ceac863_1.jpg', 2),
(66, 22, 'prod_69f38a4a66311_0.jpeg', 1),
(67, 22, 'prod_69f38a4a667dd_1.jpg', 2),
(68, 23, 'prod_69f38ad7434c3_0.jpg', 1),
(69, 23, 'prod_69f38ad743671_1.jpg', 2),
(70, 24, 'prod_69f38cf2099cc_0.jpg', 1),
(71, 24, 'prod_69f38cf20a1a6_1.jpg', 2),
(72, 25, 'prod_69f38d3d36c16_0.jpg', 1),
(73, 25, 'prod_69f38d3d3734a_1.webp', 2),
(74, 26, 'prod_69f38dc45a83f_0.jpg', 1),
(75, 26, 'prod_69f38dc45ac28_1.webp', 2),
(76, 27, 'prod_69f38e23a9a78_0.jpeg', 1),
(77, 27, 'prod_69f38e23a9d61_1.webp', 2),
(78, 27, 'prod_69f38e23a9ed5_2.jpg', 3),
(79, 28, 'prod_69f38e83e904b_0.jpeg', 1),
(80, 28, 'prod_69f38e83e942c_1.jpg', 2),
(81, 29, 'prod_69f38ed529ee9_0.jpg', 1),
(82, 30, 'prod_69f38f9f4945e_0.jpg', 1),
(83, 30, 'prod_69f38f9f49731_1.jpeg', 2),
(84, 31, 'prod_69f38fe73f20b_0.jpg', 1),
(85, 31, 'prod_69f38fe73f529_1.avif', 2),
(86, 32, 'prod_69f3904e4459d_0.jpg', 1),
(87, 32, 'prod_69f3904e4483d_1.jpg', 2),
(88, 33, 'prod_69f390a98ffd7_0.jpg', 1),
(89, 33, 'prod_69f390a99029e_1.avif', 2),
(90, 34, 'prod_69f39100a2e47_0.jpg', 1),
(91, 34, 'prod_69f39100a325e_1.jpg', 2),
(94, 36, 'prod_69f392922d0f1_0.jpg', 1),
(95, 36, 'prod_69f392922d495_1.jpg', 2),
(96, 37, 'prod_69f3931eb0349_0.webp', 1),
(97, 37, 'prod_69f3931eb0520_1.jpg', 2),
(98, 38, 'prod_69f3939114c14_0.avif', 1),
(99, 38, 'prod_69f39391150fe_1.jpg', 2),
(100, 39, 'prod_69f3940c83573_0.jpg', 1),
(101, 39, 'prod_69f3940c83997_1.webp', 2),
(102, 40, 'prod_69f394579a642_0.webp', 1),
(103, 40, 'prod_69f394579aa71_1.jpeg', 2),
(104, 41, 'prod_69f39496d6aaf_0.jpg', 1),
(105, 41, 'prod_69f39496d7058_1.jpg', 2),
(106, 42, 'prod_69f394c81c99d_0.jpg', 1),
(107, 42, 'prod_69f394c81cd34_1.jpg', 2),
(108, 43, 'prod_69f3953b89269_0.jpeg', 1),
(109, 43, 'prod_69f3953b897c3_1.webp', 2),
(110, 35, 'prod_69f391af7a6d7_0.jpg', 1),
(111, 35, 'prod_69f391af7aa75_1.jpg', 2),
(112, 18, 'prod_69f3958d1faf6_0.webp', 1),
(113, 18, 'prod_69f3958d200ba_1.webp', 2),
(114, 44, 'prod_69f3961fc4c09_0.webp', 1),
(115, 44, 'prod_69f3961fc510f_1.webp', 2),
(116, 45, 'prod_69f3965a8f1ab_0.jpg', 1),
(117, 45, 'prod_69f3965a8f573_1.avif', 2),
(118, 46, 'prod_69f3968ba02e2_0.jpg', 1),
(119, 46, 'prod_69f3968ba0b45_1.jpg', 2),
(120, 47, 'prod_69f396ef99f93_0.jpg', 1),
(121, 47, 'prod_69f396ef9a377_1.png', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','camarero','cocinero','gerente') DEFAULT 'cliente',
  `avatar` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `email`, `nombre`, `apellidos`, `password`, `rol`, `avatar`) VALUES
(1, 'admin', 'admin@bistrofdi.es', 'Admin', '', '$2y$10$QOUxomzkP/RTr4EhHP8OVOx.9TiIbztfx1AOTKRma72u.dAvyLlvO', 'gerente', 'admin.png'),
(2, 'cliente', 'cliente@ucm.es', 'Cliente', '', '$2y$10$2gsbg4/807iDyWG4b6il0uxVFIlP.eaKovNxfqnoTcLhlXvSE1tNy', 'cliente', 'base/base4.png'),
(3, 'camarero', 'camarero@bistrofdi.es', 'Camarero', '', '$2y$10$Bv5eepK6aWZn53kDdw8Zvu01W99eB4whkjAWWJ1xaeNmnb9wwklz2', 'camarero', 'camarero.png'),
(4, 'cocinero', 'cocinero@bistrofdi.es', 'Cocinero', '', '$2y$10$Bc6aW8U1mi62WxM3QYSU/umpiSiMikYpsaubnCYOlDYS8hJEI.SJS', 'cocinero', 'cocinero.png'),
(5, 'ramon', 'rsalaz01@ucm.es', 'Ramon', 'Salazar', '$2y$10$tpTHTrPSOaDG1P94s5.Uq.dfwmUWF6Efy3f7/c8JTxiTDRzLAojQ2', 'cliente', '1772146880_Logo.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_linea_pedido` (`id_pedido`),
  ADD KEY `fk_linea_producto` (`id_producto`);

--
-- Indices de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `oferta_productos`
--
ALTER TABLE `oferta_productos`
  ADD PRIMARY KEY (`id_oferta`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_usuario` (`id_usuario`);

--
-- Indices de la tabla `pedido_ofertas`
--
ALTER TABLE `pedido_ofertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_ofertas_pedido` (`id_pedido`),
  ADD KEY `fk_pedido_ofertas_oferta` (`id_oferta`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prod_cat` (`id_categoria`);

--
-- Indices de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_img_prod` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pedido_ofertas`
--
ALTER TABLE `pedido_ofertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD CONSTRAINT `fk_linea_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_linea_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `oferta_productos`
--
ALTER TABLE `oferta_productos`
  ADD CONSTRAINT `oferta_productos_ibfk_1` FOREIGN KEY (`id_oferta`) REFERENCES `ofertas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oferta_productos_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedido_ofertas`
--
ALTER TABLE `pedido_ofertas`
  ADD CONSTRAINT `fk_pedido_ofertas_oferta` FOREIGN KEY (`id_oferta`) REFERENCES `ofertas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedido_ofertas_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  ADD CONSTRAINT `fk_img_prod` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
