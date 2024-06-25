-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2024 a las 19:48:33
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
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `clave` varchar(225) NOT NULL,
  `fechaBaja` date DEFAULT NULL,
  `puesto` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `fechaBaja`, `puesto`) VALUES
(1, 'pepe', '$2y$10$23yGbk5mVTgGHP.CXSkJ4OVNnuUiamwA9M2l92.sEvnAji9QVIil6', '2024-06-21', 'mozo'),
(4, 'eduardo', '$2y$10$ckyPSKyjzsQbBOnZU6NqhuwADy2yPuC.BnGpzDQL.F5B0oNBybKzu', NULL, 'mozo'),
(5, 'ricardo', '$2y$10$/BwVpcq.Kg22nttvtNlbOuX33d.KPqCAYllPM7yEffSgzkZN1gdvm', NULL, 'cervecero'),
(7, 'luis', '$2y$10$X5fABN/OOCPX0gmfTmfsted2PcQ3VRtN2Onf6MVGXQzzQ7kkEIQ1m', NULL, 'socio'),
(8, 'alejandro', '$2y$10$opfqIWKSgVNDsttJazZDc.lqhJGvHWNee8k/DwBfCXJjf8h/m6Bfm', NULL, 'socio'),
(9, 'isabel', '$2y$10$2awV4yEkHTB1q52b6giKq.I9wDXxrecYvbzynGuXjuBtFVRpT7r8S', NULL, 'socio'),
(10, 'pablo', '$2y$10$b2OVUz8X5IVyJGb5UP64AuZ4DoDemztr15To4AcViH.59HVUxNmr.', NULL, 'cocinero'),
(11, 'tomas', '$2y$10$yJGMxvkSbzwTWtXm7KRqj.GVxRZZ/uspcO6KXGCzuQPXJtmvUu3ge', NULL, 'cocinero'),
(12, 'ana', '$2y$10$mPaYRbnjotUkih/PWNC59uIvFBVQ.z/s4c/s5nBdaAZUjMKZhcu0O', NULL, 'bartender'),
(13, 'micaela', '$2y$10$jpgvjG4cT43xvH5S0UyPEuXFIs/lQdrANzu/7LEwrnerLSUk61rme', NULL, 'bartender'),
(14, 'martin', '$2y$10$h2e/vwl2Hi.3KLvlyVzIuunllSAFUOCD1tfgyu428qwqb8/9FAvY.', NULL, 'cocinero'),
(16, 'matin', '$2y$10$x1YQRGyYJ6lIe3ycUHFSOe0qK6vWj.7sdUQqUtGX8KjCQ7RQaUedi', NULL, 'cocinero');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
