-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-11-2025 a las 23:15:40
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
-- Base de datos: `delivery`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

CREATE TABLE `calificacion` (
  `id_calificacion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_restaurante` int(11) DEFAULT NULL,
  `id_repartidor` int(11) DEFAULT NULL,
  `puntuacion` tinyint(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_comida`
--

CREATE TABLE `categoria_comida` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria_comida`
--

INSERT INTO `categoria_comida` (`id_categoria`, `nombre`) VALUES
(1, 'Comidas'),
(2, 'Bebidas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comida`
--

CREATE TABLE `comida` (
  `id_comida` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `precio` decimal(10,0) DEFAULT NULL,
  `id_restaurante` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comida`
--

INSERT INTO `comida` (`id_comida`, `nombre`, `precio`, `id_restaurante`, `id_categoria`) VALUES
(1, 'Pizza Margarita', 20000, 1, 1),
(2, 'Pizza Hawaiana', 22000, 1, 1),
(3, 'Pizza Cuatro Quesos', 24000, 1, 1),
(4, 'Pizza Peperoni', 25000, 1, 1),
(5, 'Pizza Veggie', 23000, 1, 1),
(6, 'Coca-Cola', 4000, 1, 2),
(7, 'Jugo de Naranja Natural', 5000, 1, 2),
(8, 'Limonada', 4500, 1, 2),
(9, 'Hamburguesa Bufalo', 15000, 2, 1),
(10, 'Hamburguesa BBQ Bacon', 17000, 2, 1),
(11, 'Hamburguesa Veggie', 16000, 2, 1),
(12, 'Hamburguesa Clásica', 14000, 2, 1),
(13, 'Hamburguesa Senta\'o Especial', 18000, 2, 1),
(14, 'Coca-Cola', 4000, 2, 2),
(15, 'Jugo de Uva', 5000, 2, 2),
(16, 'Té Helado', 4500, 2, 2),
(17, 'Hot Dog Clásico', 8000, 3, 1),
(18, 'Hot Dog Bacon & Queso', 10000, 3, 1),
(19, 'Hot Dog Vegetariano', 9500, 3, 1),
(20, 'Hot Dog Mexicano', 10500, 3, 1),
(21, 'Hot Dog Especial', 11000, 3, 1),
(22, 'Coca-Cola', 4000, 3, 2),
(23, 'Limonada', 4500, 3, 2),
(24, 'Jugo de Mora', 5000, 3, 2),
(25, 'Pollo Frito', 25000, 4, 1),
(26, 'Pollo a la Parrilla', 27000, 4, 1),
(27, 'Pollo con Papas', 28000, 4, 1),
(28, 'Pollo BBQ', 30000, 4, 1),
(29, 'Combo Frisby', 35000, 4, 1),
(30, 'Coca-Cola', 4000, 4, 2),
(31, 'Jugo de Naranja', 5000, 4, 2),
(32, 'Limonada', 4500, 4, 2),
(33, 'Arroz Frito', 15000, 5, 1),
(34, 'Pollo a la Naranja', 20000, 5, 1),
(35, 'Chop Suey de Verduras', 18000, 5, 1),
(36, 'Sopa de Fideos', 12000, 5, 1),
(37, 'Pato Pekín', 35000, 5, 1),
(38, 'Coca-Cola', 4000, 5, 2),
(39, 'Jugo de Naranja Natural', 5000, 5, 2),
(40, 'Limonada', 4500, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_comida` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `subtotal` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `metodo_pago` varchar(255) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_repartidor` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_entrega_estimada` datetime DEFAULT NULL,
  `fecha_entrega_real` datetime DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidor`
--

CREATE TABLE `repartidor` (
  `id_repartidor` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `id_restaurante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `repartidor`
--

INSERT INTO `repartidor` (`id_repartidor`, `nombre`, `correo`, `contraseña`, `id_restaurante`) VALUES
(1, 'Antony', 'antony@gmail.com', '123456789', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restaurantes`
--

CREATE TABLE `restaurantes` (
  `id_restaurante` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `restaurantes`
--

INSERT INTO `restaurantes` (`id_restaurante`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Zirus Pizza', 'Cra. 26 #46 - 17 Esquina, Barrancabermeja, Santander, Colombia', '+573154054861'),
(2, 'Bufalo Senta\'o Burger\'s', 'a 25a-31, Cl. 50 #25a1, Barrancabermeja, Santander, Colombia', '+576076222555'),
(3, 'Free Dog\'s', 'Cl. 60A #22-35, Barrancabermeja, Santander, Colombia', '+573219022040'),
(4, 'Frisby', 'Cl. 50 #N° 25-29, Barrancabermeja, Santander, Colombia', '+573015555555'),
(5, 'Restaurante Grand Shanghai 大上海', 'barrio colombia, Cl. 52 #27-60, Barrancabermeja, Santander, Colombia', '+573172197126');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `correo`, `contraseña`, `direccion`, `telefono`) VALUES
(1, 'Juan Rodriguez', 'mlppinterest1@gmail.com', '123456789', 'las granjas #45 call 57', '3133183665');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_restaurante` (`id_restaurante`),
  ADD KEY `id_repartidor` (`id_repartidor`);

--
-- Indices de la tabla `categoria_comida`
--
ALTER TABLE `categoria_comida`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `comida`
--
ALTER TABLE `comida`
  ADD PRIMARY KEY (`id_comida`),
  ADD KEY `id_restaurante` (`id_restaurante`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_comida` (`id_comida`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_repartidor` (`id_repartidor`);

--
-- Indices de la tabla `repartidor`
--
ALTER TABLE `repartidor`
  ADD PRIMARY KEY (`id_repartidor`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_repartidor_restaurante` (`id_restaurante`);

--
-- Indices de la tabla `restaurantes`
--
ALTER TABLE `restaurantes`
  ADD PRIMARY KEY (`id_restaurante`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  MODIFY `id_calificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria_comida`
--
ALTER TABLE `categoria_comida`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `comida`
--
ALTER TABLE `comida`
  MODIFY `id_comida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `repartidor`
--
ALTER TABLE `repartidor`
  MODIFY `id_repartidor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `restaurantes`
--
ALTER TABLE `restaurantes`
  MODIFY `id_restaurante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `calificacion_ibfk_2` FOREIGN KEY (`id_restaurante`) REFERENCES `restaurantes` (`id_restaurante`),
  ADD CONSTRAINT `calificacion_ibfk_3` FOREIGN KEY (`id_repartidor`) REFERENCES `repartidor` (`id_repartidor`);

--
-- Filtros para la tabla `comida`
--
ALTER TABLE `comida`
  ADD CONSTRAINT `comida_ibfk_1` FOREIGN KEY (`id_restaurante`) REFERENCES `restaurantes` (`id_restaurante`),
  ADD CONSTRAINT `comida_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_comida` (`id_categoria`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_comida`) REFERENCES `comida` (`id_comida`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_repartidor`) REFERENCES `repartidor` (`id_repartidor`);

--
-- Filtros para la tabla `repartidor`
--
ALTER TABLE `repartidor`
  ADD CONSTRAINT `fk_repartidor_restaurante` FOREIGN KEY (`id_restaurante`) REFERENCES `restaurantes` (`id_restaurante`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
