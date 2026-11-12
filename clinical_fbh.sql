-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2026 a las 11:35:33
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
-- Base de datos: `clinical_fbh`
--
CREATE DATABASE IF NOT EXISTS `clinical_fbh` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `clinical_fbh`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `idCita` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `fecha_cita` date DEFAULT NULL,
  `motivo_cita` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `citas`:
--   `idUser`
--       `users_data` -> `idUser`
--

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`idCita`, `idUser`, `fecha_cita`, `motivo_cita`) VALUES
(1, 2, '2026-06-18', 'Radiografía'),
(2, 2, '2026-06-19', 'Cirugía'),
(3, 2, '2026-06-30', 'Traumatologia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `idNoticia` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `noticias`:
--   `idUser`
--       `users_data` -> `idUser`
--

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`idNoticia`, `titulo`, `imagen`, `texto`, `fecha`, `idUser`) VALUES
(1, 'Primera noticia de la web', 'noticia_6a2004fab54275.36774953.jpg', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software including versions of Lorem Ipsum.', '2026-06-03', 1),
(2, 'Segunda noticia de la web', 'noticia_6a20050e20dd79.43912807.jpg', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software including versions of Lorem Ipsum.', '2026-06-03', 1),
(3, 'Tercera noticia de la web', 'noticia_6a200517720896.42329515.jpg', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software including versions of Lorem Ipsum.', '2026-06-03', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_data`
--

CREATE TABLE `users_data` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `sexo` enum('Hombre','Mujer','Otro') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `users_data`:
--

--
-- Volcado de datos para la tabla `users_data`
--

INSERT INTO `users_data` (`idUser`, `nombre`, `apellidos`, `email`, `telefono`, `fecha_nacimiento`, `direccion`, `sexo`) VALUES
(1, 'Fernando', 'Barajas', 'admin@gmail.com', '666666666', '1991-06-06', 'C/Juan Carlos I', 'Hombre'),
(2, 'Patricia', 'Ferrer', 'usuario@gmail.com', '777777777', '1992-07-08', 'Calle San Claudio', 'Mujer');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_login`
--

CREATE TABLE `users_login` (
  `idLogin` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `users_login`:
--   `idUser`
--       `users_data` -> `idUser`
--

--
-- Volcado de datos para la tabla `users_login`
--

INSERT INTO `users_login` (`idLogin`, `idUser`, `usuario`, `password`, `rol`) VALUES
(1, 1, 'fernando123', '$2y$10$oVaMlabUGU7TXpSA8EzvOugV5X1uGXrZJJwINFbf3nYHXkXGaHblm', 'admin'),
(2, 2, 'patricia123', '$2y$10$/1VTXkShImzw9OZLcz4XE.XruA6c5sJZfZKbZ6UuMLke/FazFWKGC', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `idUser` (`idUser`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`idNoticia`),
  ADD KEY `idUser` (`idUser`);

--
-- Indices de la tabla `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`idUser`);

--
-- Indices de la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD PRIMARY KEY (`idLogin`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idUser` (`idUser`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `idNoticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users_data`
--
ALTER TABLE `users_data`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users_login`
--
ALTER TABLE `users_login`
  MODIFY `idLogin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD CONSTRAINT `users_login_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
