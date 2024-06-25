-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2024 a las 19:48:24
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
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `puestoResponsable` varchar(20) NOT NULL,
  `precio` varchar(10) NOT NULL,
  `fechaBaja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `puestoResponsable`, `precio`, `fechaBaja`) VALUES
(1, 'Empanadas', 'cocinero', '500', '2024-06-06'),
(2, 'asado', 'cocinero', '2000', NULL),
(3, 'milanesa', 'cocinero', '1000', NULL),
(4, 'flan', 'cocinero', '400', NULL),
(5, 'helado', 'cocinero', '350', NULL),
(6, 'fernet con coca', 'bartender', '500', NULL),
(7, 'Caipirinha', 'bartender', '650', NULL),
(8, 'Malbec', 'bartender', '1200', NULL),
(9, 'cerveza ipa', 'cervecero', '600', NULL),
(10, 'cerveza roja', 'cervecero', '650', NULL),
(11, 'quilmes', 'cervecero', '400', NULL),
(14, 'Spaghetti ', 'cocinero', '1250', NULL),
(15, 'canelones', 'cocinero', '1475', NULL),
(16, 'Lasagna', 'cocinero', '1525', NULL),
(17, 'Ravioles ', 'cocinero', '1600', NULL),
(18, 'Margarita', 'bartender', '850', NULL),
(19, 'Piña Colada', 'bartender', '925', NULL),
(20, 'Mojito', 'bartender', '900', NULL),
(21, 'Daiquiri', 'bartender', '875', NULL),
(22, 'Tiramisú', 'cocinero', '650', NULL),
(23, 'Cheesecake de Frutos', 'cocinero', '725', NULL),
(24, 'Brownie con Helado', 'cocinero', '575', NULL),
(25, 'Tarta de Manzana', 'cocinero', '625', NULL),
(26, 'lemon pie', 'cocinero', '595', NULL),
(27, 'milanesa a caballo', 'cocinero', '1500', NULL),
(28, 'hamburguesas de garb', 'cocinero', '1200', NULL),
(29, 'corona', 'cervecero', '500', NULL),
(30, 'hamburguesa de garba', 'cocinero', '1200', NULL),
(31, 'corona', 'cervecero', '500', NULL),
(32, 'hamburguesa de garba', 'cocinero', '1200', NULL),
(33, 'corona', 'cervecero', '500', NULL),
(34, 'hamburguesa de garba', 'cocinero', '1200', NULL),
(35, 'corona', 'cervecero', '500', NULL),
(36, 'hamburguesa', 'cocinero', '1200', NULL),
(37, 'corona', 'cervecero', '500', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
