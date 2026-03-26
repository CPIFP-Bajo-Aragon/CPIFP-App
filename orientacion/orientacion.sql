-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-03-2023 a las 18:42:22
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `orientacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asesoria`
--

CREATE TABLE `asesoria` (
  `id_asesoria` int(11) NOT NULL,
  `nombre_as` varchar(100) DEFAULT NULL,
  `dni_as` varchar(25) DEFAULT NULL,
  `titulo_as` varchar(255) DEFAULT NULL,
  `telefono_as` varchar(25) DEFAULT NULL,
  `email_as` varchar(255) DEFAULT NULL,
  `descripcion_as` text DEFAULT NULL,
  `domicilio_as` varchar(255) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `asesoria`
--

INSERT INTO `asesoria` (`id_asesoria`, `nombre_as`, `dni_as`, `titulo_as`, `telefono_as`, `email_as`, `descripcion_as`, `domicilio_as`, `fecha_inicio`, `fecha_fin`, `id_estado`) VALUES
(14, 'Jonatan Seg', '544343', 'Duda ... 34343', '43 432424', 'fefeifef@dfwfwww', 'Tengo problemas para conectarme a Internettttt4444444', 'Av. Aragón', '2022-12-16 13:48:17', NULL, 2),
(15, 'Luis', '453534g4eee', 'Otra duda', '43 4324242 4242', '', 'Otrarrrr duca', 'C/ pepito ...', '2022-12-16 13:56:58', '2023-01-19 13:09:30', 3),
(16, 'Juanitorrrrr', '5443432423s', 'Duda ...', '', '', 'Massss dudas', '', '2022-12-16 13:57:26', '2023-01-18 09:34:09', 3),
(17, 'Luis', '453534g4', 'Duda ... 34343', '43 4324242 4242', 'fefeifef@dfwfwww', 'rrrrrrrrrrrr', 'C/ pepito ...', '2023-02-13 13:29:34', '2023-02-13 13:30:19', 3),
(18, 'Juanito', '453534g4  ', '', '', '', 'Duda peac', '', '2023-02-21 11:40:30', '2023-02-21 11:41:18', 3),
(19, 'Luis', '453534444', 'No se como ... tal tal tal', '', 'fefeifef@dfwfwww', 'Describimosssddd', '', '2023-03-01 12:20:59', NULL, 2),
(20, 'Jonatan', '5443432423s', '', '', '', '', '', '2023-03-01 12:43:56', NULL, 2),
(29, 'Luis', '5443432423s', '', '', 'ffff@ffff', 'fffeeeeeeeeeeb trtrttt', '', '2023-03-07 19:05:42', NULL, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `departamento` varchar(45) NOT NULL,
  `departamento_corto` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`id_departamento`, `departamento`, `departamento_corto`) VALUES
(1, 'Conserjeria', 'CONS'),
(2, 'PEAC', 'PEAC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `estado` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `estado`) VALUES
(1, 'Nueva'),
(2, 'Procesando'),
(3, 'Cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `login` varchar(45) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesor`, `login`, `password`, `nombre_completo`) VALUES
(1, 'javier', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Javier Arenzana'),
(2, 'josemanuel', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'José Manuel'),
(3, 'conchi', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Conchi');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores_departamento`
--

CREATE TABLE `profesores_departamento` (
  `id_profesores_departamento` int(11) NOT NULL,
  `id_profesor` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `profesores_departamento`
--

INSERT INTO `profesores_departamento` (`id_profesores_departamento`, `id_profesor`, `id_rol`, `id_departamento`) VALUES
(1, 1, 10, 2),
(2, 2, 20, 2),
(3, 3, 30, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_acciones`
--

CREATE TABLE `reg_acciones` (
  `id_reg_acciones` int(11) NOT NULL,
  `fecha_reg` datetime NOT NULL,
  `accion` varchar(255) NOT NULL,
  `automatica` tinyint(4) NOT NULL DEFAULT 0,
  `id_asesoria` int(11) NOT NULL,
  `id_profesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `reg_acciones`
--

INSERT INTO `reg_acciones` (`id_reg_acciones`, `fecha_reg`, `accion`, `automatica`, `id_asesoria`, `id_profesor`) VALUES
(2, '2022-12-16 13:48:17', 'Inicia', 1, 14, 1),
(3, '2022-12-16 13:56:58', 'Inicia', 1, 15, 1),
(4, '2022-12-16 13:57:26', 'Inicia', 1, 16, 1),
(5, '2022-12-16 14:34:51', 'Revisamos la documentación y damos de alta Revisamos la documentación y damos de alta Revisamos la documentación y damos de alta Revisamos la documentación y damos de alta Revisamos la documentación y damos de alta Revisamos la documentación y damos de al', 0, 16, 2),
(6, '2023-01-12 13:35:00', 'Cualquier cosa', 0, 16, 3),
(7, '2023-01-13 13:20:14', 'Nueva acción ...', 0, 16, 1),
(8, '2023-01-13 13:20:38', 'Nueva accion otra vez ... y otra ...', 0, 16, 1),
(9, '2023-01-13 13:32:00', 'Otraaaaaa', 0, 16, 1),
(10, '2023-01-16 14:11:41', 'he realizado ....', 0, 16, 1),
(11, '2023-01-16 14:12:50', 'ffsfsafs fffff', 0, 15, 1),
(12, '2023-01-18 09:34:09', 'Cierra', 1, 16, 1),
(13, '2023-01-19 13:09:30', 'Cierra', 1, 15, 1),
(14, '2023-01-19 13:33:12', 'primera accióntgt', 0, 14, 1),
(15, '2023-01-19 13:43:39', 'Otra acciónfffffffffffffffffff.................dddñññññññ', 0, 14, 1),
(16, '2023-02-13 13:29:34', 'Inicia', 1, 17, 1),
(17, '2023-02-13 13:29:48', 'Cualquier acción', 0, 17, 1),
(18, '2023-02-13 13:29:54', 'Otraaaa acción...', 0, 17, 1),
(19, '2023-02-13 13:30:05', 'dgdgdgdd........', 0, 17, 1),
(20, '2023-02-13 13:30:19', 'Cierra', 1, 17, 1),
(21, '2023-02-14 13:37:51', 'Otra cosa...', 0, 14, 1),
(22, '2023-02-21 11:40:30', 'Inicia', 1, 18, 1),
(23, '2023-02-21 11:41:04', 'revidado documentación', 0, 18, 1),
(24, '2023-02-21 11:41:18', 'Cierra', 1, 18, 1),
(25, '2023-03-01 12:20:59', 'Inicia', 1, 19, 1),
(26, '2023-03-01 12:21:38', 'Revisando...v', 0, 19, 1),
(27, '2023-03-01 12:43:56', 'Inicia', 1, 20, 1),
(28, '2023-03-01 12:44:28', 'regerg grgrgg fvdvvvv', 0, 20, 1),
(29, '2023-03-01 12:45:24', 'Cierra', 1, 20, 1),
(42, '2023-03-07 19:05:42', 'Inicia', 1, 29, 3),
(43, '2023-03-08 12:44:08', 'ggggggggg', 0, 19, 3),
(44, '2023-03-08 13:25:34', 'gggg', 0, 29, 3),
(45, '2023-03-08 13:30:54', 'bbb', 0, 19, 3),
(48, '2023-03-08 17:50:40', 'Abre', 1, 20, 1),
(49, '2023-03-08 17:51:01', 'fsdfsfsf', 0, 20, 1),
(50, '2023-03-08 17:51:15', 'Cierra', 1, 20, 1),
(51, '2023-03-08 17:52:02', 'Abre', 1, 20, 1),
(52, '2023-03-08 17:59:23', 'Cierra', 1, 20, 1),
(53, '2023-03-08 17:59:37', 'Abre', 1, 20, 1),
(54, '2023-03-08 18:00:52', 'Otra pruebaaa', 0, 20, 2),
(55, '2023-03-08 18:03:49', 'Ha llamado de nuevo', 0, 19, 3),
(56, '2023-03-08 18:35:56', 'vvvvvvvwfvv..........', 0, 14, 3),
(57, '2023-03-08 18:37:04', 'frfrfrfrfr nnnnnnnnnn', 0, 14, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`) VALUES
(10, 'root'),
(20, 'Orientación'),
(30, 'Conserje');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asesoria`
--
ALTER TABLE `asesoria`
  ADD PRIMARY KEY (`id_asesoria`),
  ADD KEY `fk_asesoria_estados_idx` (`id_estado`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesor`);

--
-- Indices de la tabla `profesores_departamento`
--
ALTER TABLE `profesores_departamento`
  ADD PRIMARY KEY (`id_profesores_departamento`),
  ADD KEY `fk_profesores_departamento_profesores1_idx` (`id_profesor`),
  ADD KEY `fk_profesores_departamento_rol1_idx` (`id_rol`),
  ADD KEY `fk_profesores_departamento_departamento1_idx` (`id_departamento`);

--
-- Indices de la tabla `reg_acciones`
--
ALTER TABLE `reg_acciones`
  ADD PRIMARY KEY (`id_reg_acciones`),
  ADD KEY `fk_reg_acciones_asesoria1_idx` (`id_asesoria`),
  ADD KEY `fk_reg_acciones_profesores1_idx` (`id_profesor`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asesoria`
--
ALTER TABLE `asesoria`
  MODIFY `id_asesoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `profesores_departamento`
--
ALTER TABLE `profesores_departamento`
  MODIFY `id_profesores_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reg_acciones`
--
ALTER TABLE `reg_acciones`
  MODIFY `id_reg_acciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asesoria`
--
ALTER TABLE `asesoria`
  ADD CONSTRAINT `fk_asesoria_estados` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `profesores_departamento`
--
ALTER TABLE `profesores_departamento`
  ADD CONSTRAINT `fk_profesores_departamento_departamento1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_profesores_departamento_profesores1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_profesores_departamento_rol1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reg_acciones`
--
ALTER TABLE `reg_acciones`
  ADD CONSTRAINT `fk_reg_acciones_asesoria1` FOREIGN KEY (`id_asesoria`) REFERENCES `asesoria` (`id_asesoria`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reg_acciones_profesores1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
