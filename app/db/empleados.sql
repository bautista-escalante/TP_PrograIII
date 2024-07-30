-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2024 a las 13:50:45
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
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `ocupado` tinyint(1) NOT NULL,
  `puntuacion` float DEFAULT NULL,
  `deleted_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `tipo`, `clave`, `ocupado`, `puntuacion`, `deleted_at`) VALUES
(1, 'pepe', 'mozo', '$2y$10$23yGbk5mVTgGHP.CXSkJ4OVNnuUiamwA9M2l92.sEvnAji9QVIil6', 1, NULL, '2024-06-04'),
(4, 'eduardo', 'mozo', '$2y$10$ckyPSKyjzsQbBOnZU6NqhuwADy2yPuC.BnGpzDQL.F5B0oNBybKzu', 0, 4, NULL),
(5, 'ricardo', 'cervecero', '$2y$10$/BwVpcq.Kg22nttvtNlbOuX33d.KPqCAYllPM7yEffSgzkZN1gdvm', 0, NULL, NULL),
(7, 'luis', 'socio', '$2y$10$X5fABN/OOCPX0gmfTmfsted2PcQ3VRtN2Onf6MVGXQzzQ7kkEIQ1m', 0, NULL, NULL),
(8, 'alejandro', 'socio', '$2y$10$opfqIWKSgVNDsttJazZDc.lqhJGvHWNee8k/DwBfCXJjf8h/m6Bfm', 0, NULL, NULL),
(9, 'isabel', 'socio', '$2y$10$2awV4yEkHTB1q52b6giKq.I9wDXxrecYvbzynGuXjuBtFVRpT7r8S', 0, NULL, NULL),
(10, 'pablo', 'cocinero', '$2y$10$b2OVUz8X5IVyJGb5UP64AuZ4DoDemztr15To4AcViH.59HVUxNmr.', 0, 4, NULL),
(11, 'tomas', 'cocinero', '$2y$10$yJGMxvkSbzwTWtXm7KRqj.GVxRZZ/uspcO6KXGCzuQPXJtmvUu3ge', 0, 4.5, NULL),
(13, 'ana', 'bartender', '$2y$10$mPaYRbnjotUkih/PWNC59uIvFBVQ.z/s4c/s5nBdaAZUjMKZhcu0O', 0, NULL, NULL),
(14, 'micaela', 'bartender', '$2y$10$jpgvjG4cT43xvH5S0UyPEuXFIs/lQdrANzu/7LEwrnerLSUk61rme', 0, NULL, NULL),
(16, 'matin', 'cocinero', '$2y$10$h2e/vwl2Hi.3KLvlyVzIuunllSAFUOCD1tfgyu428qwqb8/9FAvY.', 0, 5, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
