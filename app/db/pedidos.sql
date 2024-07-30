-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2024 a las 13:50:55
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
-- Base de datos: `tp_prograiii`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `idProducto` int(11) DEFAULT NULL,
  `codigoAlfa` varchar(5) DEFAULT NULL,
  `idMesa` int(11) DEFAULT NULL,
  `idCocinero` int(11) DEFAULT NULL,
  `idMozo` int(11) DEFAULT NULL,
  `tiempo` int(11) DEFAULT NULL,
  `cancelado` tinyint(1) NOT NULL,
  `estado` varchar(25) NOT NULL,
  `fechaInicio` datetime DEFAULT NULL,
  `fechaEntrega` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `idProducto`, `codigoAlfa`, `idMesa`, `idCocinero`, `idMozo`, `tiempo`, `cancelado`, `estado`, `fechaInicio`, `fechaEntrega`) VALUES
(1, 3, 'x4XpO', 8, 10, 4, 15, 0, 'entregado', '2024-07-26 03:02:36', '2024-07-26 03:20:09'),
(2, 2, 'ul18T', 2, 10, 4, 15, 0, 'entregado', '2024-07-26 03:18:54', '2024-07-26 03:20:18'),
(3, 2, '2oHL5', 6, 10, 4, 15, 0, 'entregado', '2024-07-26 03:27:46', '2024-07-26 03:29:21'),
(4, 2, 'J1xsz', 4, 10, 4, 30, 0, 'entregado', '2024-07-26 16:19:59', '2024-07-28 23:24:00'),
(5, 2, 'FYukU', 5, 10, 4, 24, 0, 'entregado', '2024-07-28 22:52:42', '2024-07-28 23:24:04'),
(6, 2, 'VLHgf', 1, 10, 4, 30, 0, 'entregado', '2024-07-29 03:24:17', '2024-07-29 03:30:51'),
(7, 10, 'T7OSm', 1, 5, 4, 15, 0, 'entregado', '2024-07-29 03:24:17', '2024-07-29 23:51:53'),
(8, 15, 'd43G7', 1, 10, 4, 30, 0, 'entregado', '2024-07-29 03:24:17', '2024-07-29 23:51:53'),
(9, 21, 'ng3ev', 1, 14, 4, 30, 0, 'entregado', '2024-07-29 03:24:17', '2024-07-29 03:30:51'),
(10, 2, 'MYsDb', 8, 10, 4, 60, 0, 'entregado', '2024-07-29 23:33:47', '2024-07-29 23:52:21'),
(11, 10, 'kSl5B', 8, 5, 4, 15, 0, 'entregado', '2024-07-29 23:33:47', '2024-07-29 23:52:21'),
(12, 15, 'SgnwD', 8, 10, 4, 60, 0, 'entregado', '2024-07-29 23:33:47', '2024-07-29 23:52:21'),
(13, 21, 'rlR2G', 8, 14, 4, 30, 0, 'entregado', '2024-07-29 23:33:47', '2024-07-29 23:52:21'),
(14, 2, 'BwUAV', 1, 10, 4, 60, 0, 'entregado', '2024-07-29 23:35:07', '2024-07-29 23:51:53'),
(15, 10, 'y7OXi', 1, 5, 4, 15, 0, 'entregado', '2024-07-29 23:35:07', '2024-07-29 23:51:53'),
(16, 15, 'c1pH6', 1, 10, 4, 60, 0, 'entregado', '2024-07-29 23:35:07', '2024-07-29 23:51:53'),
(17, 21, '0POgf', 1, 14, 4, 30, 0, 'entregado', '2024-07-29 23:35:07', '2024-07-29 23:51:53'),
(18, 2, '5dxCZ', 2, 5, 4, 15, 0, 'entregado', '2024-07-29 23:35:24', '2024-07-29 23:52:00'),
(19, 10, '5dxCZ', 2, 5, 4, 15, 0, 'entregado', '2024-07-29 23:35:24', '2024-07-29 23:52:00'),
(20, 15, '5dxCZ', 2, 5, 4, 15, 0, 'entregado', '2024-07-29 23:35:24', '2024-07-29 23:52:00'),
(21, 21, '5dxCZ', 2, 5, 4, 15, 0, 'entregado', '2024-07-29 23:35:24', '2024-07-29 23:52:00'),
(22, 2, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(23, 10, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(24, 15, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(25, 21, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(26, 21, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(27, 21, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16'),
(28, 21, 'rtx0e', 6, 5, 4, 15, 0, 'entregado', '2024-07-29 23:45:00', '2024-07-29 23:52:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProducto` (`idProducto`),
  ADD KEY `idMesa` (`idMesa`),
  ADD KEY `idCocinero` (`idCocinero`),
  ADD KEY `idMozo` (`idMozo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`idMesa`) REFERENCES `mesas` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`idCocinero`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`idMozo`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
