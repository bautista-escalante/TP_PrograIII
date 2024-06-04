-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2024 a las 20:05:25
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
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `estado` varchar(60) NOT NULL,
  `idPedido` varchar(5) NOT NULL,
  `tiempo` int(11) NOT NULL,
  `nombreCliente` varchar(30) NOT NULL,
  `NombreEmpleadoEncargado` varchar(30) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cancelado` tinyint(1) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `estado`, `idPedido`, `tiempo`, `nombreCliente`, `NombreEmpleadoEncargado`, `timestamp`, `cancelado`, `cantidad`) VALUES
(1, 'en preparacion', 'cGDlm', 11, 'cerveza ipa', 'pepe', '2024-06-04 07:56:55', 0, 4),
(2, 'en preparacion', 'nX3FN', 10, 'Margarita', 'pepe', '2024-06-04 07:56:55', 0, 1),
(3, 'en preparacion', 'EIX5D', 2, 'Empanadas', 'pepe', '2024-06-04 07:56:55', 0, 6),
(4, 'en preparacion', 'badBt', 10, 'Asado', 'pepe', '2024-06-04 07:56:55', 0, 1),
(5, 'en preparacion', 'jLWgw', 10, 'Milanesa', 'pepe', '2024-06-04 07:56:55', 0, 4),
(6, 'en preparacion', '3QrwY', 1, 'Helado', 'pepe', '2024-06-04 07:56:55', 0, 1),
(7, 'en preparacion', 'fYb5Z', 14, 'Milanesa', 'pepe', '2024-06-04 07:58:57', 0, 1),
(8, 'en preparacion', 'u69ZK', 12, 'Quilmes', 'pepe', '2024-06-04 07:56:55', 0, 1),
(9, 'en preparacion', 'at32A', 4, 'Patagonia', 'pepe', '2024-06-04 07:56:55', 0, 1),
(10, 'en preparacion', 'gMVrP', 13, 'Milanesa', 'pepe', '2024-06-04 07:56:55', 0, 1),
(11, 'en preparacion', '1rVNH', 13, 'Milanesa', 'pepe', '2024-06-04 07:56:55', 0, 1),
(12, 'en preparacion', '9Fjmy', 8, 'Asado', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(13, 'en preparacion', 'K6hP7', 9, 'Milanesa', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(14, 'en preparacion', 'naLYI', 1, 'Provoleta', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(15, 'en preparacion', 'KBjC9', 6, 'flan', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(16, 'en preparacion', 'mqa2w', 4, 'Helado', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(17, 'en preparacion', '0k3j4', 1, 'cerveza roja', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(18, 'en preparacion', 'XSi3q', 1, 'cerveza roja', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(19, 'en preparacion', 'syecM', 6, 'cerveza roja', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(20, 'en preparacion', 'nQYK4', 11, 'cerveza roja', 'eduardo', '2024-06-04 07:58:57', 0, 1),
(21, 'en preparacion', 'ISfOY', 10, 'cerveza roja', 'pepe', '2024-06-04 07:18:39', 0, 1),
(22, 'en preparacion', 'XcoDM', 2, 'cerveza roja', 'pepe', '2024-06-04 07:20:55', 0, 1),
(23, 'en preparacion', 'RVwTK', 15, 'cerveza roja', 'pepe', '2024-06-04 17:26:01', 0, 1),
(24, 'en preparacion', 'sdoEq', 14, 'cerveza roja', 'pepe', '2024-06-04 17:45:49', 0, 1),
(25, 'en preparacion', 'b65As', 6, 'cerveza ', 'nazareno', '2024-06-04 17:46:14', 0, 1),
(26, 'en preparacion', 'T1w6I', 7, 'cerveza roja', 'nazareno', '2024-06-04 17:58:44', 0, 1),
(27, 'en preparacion', 'e4kfs', 15, 'cerveza roja', 'nazareno', '2024-06-04 18:02:23', 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
