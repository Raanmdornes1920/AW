-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: vm016.db.swarm.test
-- Tiempo de generación: 09-04-2026 a las 19:44:35
-- Versión del servidor: 10.4.28-MariaDB-1:10.4.28+maria~ubu2004
-- Versión de PHP: 8.2.27

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
  `imagen` varchar(255) DEFAULT 'default_cat.png',
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
(11, 'Bebidas', 'Bebidas', 'default_cat.png', 1);

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
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('recibido','en_preparacion','cocinando','listo_cocina','terminado','entregado','cancelado') DEFAULT 'recibido',
  `tipo` enum('local','llevar') NOT NULL,
  `total` decimal(10,2) NOT NULL
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
(11, 11, 'Agua', 'Agua manantial', 2.00, 21, 1, 1, 0);

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
(51, 10, 'prod_69b349ada5848_2.jpg', 3);

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
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_usuario` (`id_usuario`);

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
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

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
