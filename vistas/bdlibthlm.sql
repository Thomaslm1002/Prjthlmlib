-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2026 a las 19:26:14
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
-- Base de datos: `bdlibthlm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autores`
--

CREATE TABLE `autores` (
  `id_autor` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `apellido` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autores`
--

INSERT INTO `autores` (`id_autor`, `nombre`, `apellido`) VALUES
(10, 'Marc', 'Cerasini'),
(11, 'Julio', 'Verne'),
(12, 'Mary', 'Wollstonecraft Shelley'),
(13, 'Ben', 'Mezrich'),
(14, 'Bram', 'Stoker'),
(15, 'Bruno', 'Nievas'),
(16, 'César', 'García Muñoz'),
(17, 'Armando', 'Rodera'),
(18, 'Jane', 'Austen'),
(19, 'Emily', 'Bronte'),
(20, 'Alejandro', 'Dumas'),
(21, 'Gabriel', 'García Márquez'),
(22, 'Nikos', 'Kazantzakis'),
(23, 'Raymond', 'Carver'),
(24, 'Umberto', 'Eco'),
(222, 'Nikos', 'Kazantzakis');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor_por_libro`
--

CREATE TABLE `autor_por_libro` (
  `id_autor` int(11) NOT NULL,
  `isbn` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autor_por_libro`
--

INSERT INTO `autor_por_libro` (`id_autor`, `isbn`) VALUES
(21, 72950);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `categoria`) VALUES
(11, 'Acción y Aventura'),
(5, 'Ciencia'),
(10, 'Clásicos'),
(4, 'Literatura'),
(3, 'Novela Histórica'),
(12, 'Romance'),
(2, 'Suspenso'),
(9, 'Tecnología'),
(1, 'Terror');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `identificacion` varchar(12) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `apellidos` varchar(64) NOT NULL,
  `celular` varchar(15) NOT NULL,
  `direccion` varchar(128) NOT NULL,
  `correo` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `identificacion`, `nombre`, `apellidos`, `celular`, `direccion`, `correo`) VALUES
(1, '1038870865', 'Thomas', 'Lopera Muñoz', '301 5293358', 'Cra 50c #90-08', 'thomaslm1002@gmail.com'),
(2, '1129012401', 'Weimar', 'Orejuela', '3013242342', 'xx zz yy vv', 'weimarorejuela@gmail.com'),
(3, '42424242442', 'Harol', 'Gomez', '4242424141', 'xx zz yy nn', 'Haroldgomez@gmail.com'),
(5, '1020308990', 'Samuel', 'Munoz Guituierrez', '3004015446', 'Cra 90e #30-04', 'samuelmunoz@gmail.com'),
(6, '1021806743', 'Paula Andrea', 'Cali Martinez', '3116080978', 'Calle 94 #55-77 apartamento 02', 'andreacali749@gmail.com'),
(7, '1025656562', 'Sofia', 'Carmona Castaño', '3044498336', 'CR 51 #94-53', 'sosopretttygirll@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `isbn` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `categoria` int(11) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `precio` int(11) NOT NULL CHECK (`precio` >= 0),
  `portada` varchar(128) DEFAULT 'no_imagen.png',
  `cantidad_stock` int(11) NOT NULL CHECK (`cantidad_stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`isbn`, `titulo`, `categoria`, `fecha_publicacion`, `precio`, `portada`, `cantidad_stock`) VALUES
(1213, 'Harry Potter', 11, '1985-04-13', 50000, 'no_imagen.png', 100),
(12851, 'Zorba, el griego', 12, '2010-11-25', 38500, 'no_imagen.png', 10),
(12852, 'Cathedral', 12, '2004-08-15', 25700, 'no_imagen.png', 10),
(12855, 'Cristo de Nuevo Crucificado', 12, '2014-06-11', 46000, 'no_imagen.png', 10),
(12856, '¿Quieres hacer el favor de callarte, por favor?', 12, '2005-04-25', 34000, 'no_imagen.png', 10),
(24377, 'Crónica de una Muerte Anunciada', 12, '2016-07-15', 48000, 'no_imagen.png', 10),
(32811, 'Miguel Strogoff', 2, '2001-12-10', 25000, 'no_imagen.png', 10),
(32813, 'Cinco Semanas en Globo', 2, '2004-05-15', 33000, 'no_imagen.png', 10),
(37210, 'Operation Hell Gate', 2, '2005-09-27', 48000, 'no_imagen.png', 10),
(37212, 'Shadow Protocol', 2, '2008-09-12', 51000, 'no_imagen.png', 10),
(38521, 'Sexo en la Luna', 5, '2011-06-01', 29500, 'no_imagen.png', 10),
(38523, 'Breaking Twitter', 9, '2021-09-07', 61000, 'no_imagen.png', 10),
(41522, 'Drácula', 1, '1999-04-10', 50000, 'no_imagen.png', 8),
(41524, 'El Invitado de Drácula', 1, '2002-06-19', 44000, 'no_imagen.png', 10),
(42830, 'Realidad aumentada', 9, '2001-03-13', 35200, 'no_imagen.png', 10),
(42832, 'La Guerra de los Cielos', 9, '2013-03-09', 49000, 'no_imagen.png', 10),
(44231, 'Juicio Final, Sangre en el cielo', 9, '2009-05-30', 40000, 'no_imagen.png', 10),
(44233, 'La Sombra del Ayer', 9, '2012-07-21', 43000, 'no_imagen.png', 10),
(46632, 'El Enigma de los Vencidos', 9, '2000-11-25', 38500, 'no_imagen.png', 10),
(46634, 'El Aroma del Miedo', 1, '2007-01-16', 40000, 'no_imagen.png', 10),
(46812, 'El gato negro', 1, '1997-10-12', 44000, 'no_imagen.png', 10),
(46814, 'La Caída de la Casa Usher', 1, '1996-02-18', 41000, 'no_imagen.png', 10),
(49820, 'Frankenstein', 1, '1990-03-01', 55500, 'no_imagen.png', 10),
(49822, 'El Último Hombre', 2, '1994-11-30', 47000, 'no_imagen.png', 10),
(57840, 'Orgullo y Prejuicio', 10, '2003-09-25', 36100, 'no_imagen.png', 10),
(57842, 'Persuasión', 10, '2006-08-27', 37000, 'no_imagen.png', 10),
(60341, 'Cumbres Borrascosas', 10, '1998-11-25', 60800, 'no_imagen.png', 10),
(60343, 'Villette', 10, '2001-10-10', 39000, 'no_imagen.png', 10),
(61842, 'La Dama de las Camelias', 10, '1995-07-28', 57600, 'no_imagen.png', 10),
(61844, 'El Conde de Montecristo', 4, '1998-12-05', 75000, 'no_imagen.png', 10),
(72950, '100 años de soledad', 12, '1990-04-27', 39500, 'no_imagen.png', 10),
(72951, 'El amor en los tiempos del cólera', 12, '1998-05-18', 43000, 'no_imagen.png', 10),
(88073, 'El Nombre de la Rosa', 4, '2011-05-24', 68000, 'no_imagen.png', 10),
(88075, 'Baudolino', 4, '2014-09-14', 69000, 'no_imagen.png', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_cliente`
--

CREATE TABLE `pedidos_cliente` (
  `id_pedido` int(11) NOT NULL,
  `nro_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `isbn` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos_cliente`
--

INSERT INTO `pedidos_cliente` (`id_pedido`, `nro_pedido`, `id_cliente`, `isbn`, `fecha_pedido`, `cantidad`, `subtotal`) VALUES
(1, 101, 3, 1213, '2026-10-20 21:50:00', 1, 50000),
(2, 102, 1, 41522, '2026-10-28 13:01:00', 2, 100000);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id_autor`);

--
-- Indices de la tabla `autor_por_libro`
--
ALTER TABLE `autor_por_libro`
  ADD PRIMARY KEY (`id_autor`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `categoria` (`categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `pedidos_cliente`
--
ALTER TABLE `pedidos_cliente`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `isbn` (`isbn`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autores`
--
ALTER TABLE `autores`
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedidos_cliente`
--
ALTER TABLE `pedidos_cliente`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `autor_por_libro`
--
ALTER TABLE `autor_por_libro`
  ADD CONSTRAINT `autor_por_libro_ibfk_1` FOREIGN KEY (`id_autor`) REFERENCES `autores` (`id_autor`),
  ADD CONSTRAINT `autor_por_libro_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `libros` (`isbn`);

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `pedidos_cliente`
--
ALTER TABLE `pedidos_cliente`
  ADD CONSTRAINT `pedidos_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `pedidos_cliente_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `libros` (`isbn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
