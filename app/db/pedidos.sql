-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2024 a las 19:48:19
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
(3, 27, 'fth7W', 1, 10, 4, 48, 0, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:07:10'),
(4, 27, 'FOCcQ', 1, 10, 4, NULL, 1, 'cancelado', '2024-06-25 02:01:17', NULL),
(5, 21, '2Bfph', 1, 13, 4, 22, 0, 'entregado', '2024-06-25 02:01:17', '2024-06-25 19:19:02'),
(6, 3, 'JqUFa', 2, 10, 4, 49, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(7, 10, 'bvdUS', 2, 5, 4, 8, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:08:11'),
(8, 27, 'taYqI', 2, 10, 4, 21, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(9, 27, 'sxWUJ', 2, 10, 4, 14, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:07:10'),
(10, 21, '6Qhs9', 2, 13, 4, 49, 0, 'entregado', '2024-06-25 19:06:38', '2024-06-25 19:19:02');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
