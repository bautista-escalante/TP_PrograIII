-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2024 a las 13:50:40
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
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `puntuacionMozo` int(11) NOT NULL,
  `comentarioMozo` text DEFAULT NULL,
  `puntuacionCocinero` int(11) NOT NULL,
  `comentarioCocinero` text DEFAULT NULL,
  `puntuacionMesa` int(11) NOT NULL,
  `comentarioMesa` text DEFAULT NULL,
  `puntuacionResto` int(11) NOT NULL,
  `comentarioResto` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `puntuacionMozo`, `comentarioMozo`, `puntuacionCocinero`, `comentarioCocinero`, `puntuacionMesa`, `comentarioMesa`, `puntuacionResto`, `comentarioResto`) VALUES
(1, 5, 'el mozo fue amable', 4, 'la comida esta muy rica', 5, 'la mesa  estaba en buen estado ', 5, 'el ambiente es agradable'),
(2, 5, 'el mozo fue amable', 4, 'la comida esta muy rica', 5, 'la mesa  estaba en buen estado ', 5, 'el ambiente es agradable'),
(3, 5, 'el mozo fue amable', 4, 'la comida esta muy rica', 5, 'la mesa  estaba en buen estado ', 5, 'el ambiente es agradable'),
(4, 5, 'el mozo fue amable', 4, 'la comida esta muy rica', 5, 'la mesa  estaba en buen estado ', 5, 'el ambiente es agradable'),
(5, 4, 'el mozo fue amable', 4, 'la comida esta muy rica', 5, 'la mesa  estaba en buen estado ', 5, 'el ambiente es agradable');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
