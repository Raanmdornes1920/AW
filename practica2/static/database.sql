-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-03-2026 a las 22:20:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
(1, 'Hamburguesas', 'Que quieres que te describa de una hambuerquesa mostro?', 'categoria_default.jpg', 1);

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
  `ofertado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'admin', 'admin@bistrofdi.es', '', '', '$2y$10$QOUxomzkP/RTr4EhHP8OVOx.9TiIbztfx1AOTKRma72u.dAvyLlvO', 'gerente', 'admin.png'),
(2, 'cliente', 'cliente@ucm.es', '', '', '$2y$10$2gsbg4/807iDyWG4b6il0uxVFIlP.eaKovNxfqnoTcLhlXvSE1tNy', 'cliente', 'default.png'),
(3, 'camarero', 'camarero@bistrofdi.es', '', '', '$2y$10$Bv5eepK6aWZn53kDdw8Zvu01W99eB4whkjAWWJ1xaeNmnb9wwklz2', 'camarero', 'camarero.png'),
(4, 'cocinero', 'cocinero@bistrofdi.es', '', '', '$2y$10$Bc6aW8U1mi62WxM3QYSU/umpiSiMikYpsaubnCYOlDYS8hJEI.SJS', 'cocinero', 'cocinero.png'),
(5, 'ramon', 'rsalaz01@ucm.es', 'Ramon', 'Salazar', '$2y$10$tpTHTrPSOaDG1P94s5.Uq.dfwmUWF6Efy3f7/c8JTxiTDRzLAojQ2', 'cliente', '1772146880_Logo.png'),
(6, 'a', 'a@a.a', 'a', 'a', '$2y$10$xGpK716UUF/ALKFFeh3bJOK2kRIwzktXkQIswAfUAmb/aYBZWBvaW', 'cliente', 'base/base1.png'),
(7, 'b', 'b@b.b', 'b', 'b', '$2y$10$3AKjIISbSlfqvJMrMt4vQeNJGZMGQ1tEPWhcf70/4ZcHs.2/y3Npe', 'cliente', 'base/base1.png'),
(8, 'c', 'c@c.c', 'c', 'c', '$2y$10$GeNudd3k9GTtkbTTh7fZFu03HNZDAG3jn607ygX6UoaMDFstv4/Am', 'cliente', 'base/base1.png'),
(9, 'd', 'd@d.d', 'd', 'd', '$2y$10$VLx4YCqVFS5Lk8CW5lub9.hpFyC664JM2qhxNA6TRWLPy3MBC5wRu', 'cliente', 'base/base4.png'),
(10, 'e', 'e@e.e', 'e', 'e', '$2y$10$yrICaqTnWzYKhfGhCP1SiO47202P4wwiDYZ6WQn8/LKsfLFwsLzIa', 'cliente', '1772485506_default.png');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

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
