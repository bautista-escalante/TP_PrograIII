-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2024 a las 15:00:27
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
(1, 3, 'Zu7gS', 1, 10, 4, 9, 0, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:07:10'),
(2, 10, 'MlVQg', 1, 5, 4, 19, 0, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:08:11'),
(3, 27, 'fth7W', 1, 10, 4, NULL, 1, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:07:10'),
(4, 27, 'FOCcQ', 1, 10, 4, NULL, 1, 'cancelado', '2024-06-25 02:01:17', NULL),
(5, 21, '2Bfph', 1, 13, 4, 22, 0, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:19:02'),
(6, 3, 'JqUFa', 2, 10, 4, NULL, 1, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(7, 10, 'bvdUS', 2, 5, 4, 8, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:08:11'),
(8, 27, 'taYqI', 2, 10, 4, 21, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(9, 27, 'sxWUJ', 2, 10, 4, 14, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(10, 21, '6Qhs9', 2, 13, 4, 49, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:19:02'),
(11, 3, 'yA26o', 7, 10, 4, 40, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:51:53'),
(12, 3, 'RshDb', 7, 10, 4, 34, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:51:53'),
(13, 10, '0DzkZ', 7, 5, 4, 21, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:55:18'),
(14, 27, 'ZuPXJ', 7, 10, 4, 45, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:51:53'),
(15, 27, 'gm1nE', 7, 10, 4, 12, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:51:54'),
(16, 21, '8eFj2', 7, 13, 4, 13, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:57:40'),
(17, 21, 'kCT0G', 7, 13, 4, 56, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:57:40'),
(18, 21, 'Wp8RL', 7, 13, 4, 58, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:57:40'),
(19, 26, 'su3PM', 7, 10, 4, 54, 0, 'entregado', '2024-06-26 03:49:52', '2024-06-26 03:51:54'),
(20, 29, '6c8Ta', 4, 5, 4, 115, 0, 'entregado', '2024-06-30 15:46:21', '2024-07-02 03:58:46'),
(21, 29, 'ITq2a', 4, 5, 4, 64, 0, 'entregado', '2024-06-30 15:46:21', '2024-07-02 04:04:19'),
(22, 29, 'o7KfM', 4, 5, 4, 88, 0, 'entregado', '2024-06-30 15:46:21', '2024-07-02 04:04:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
