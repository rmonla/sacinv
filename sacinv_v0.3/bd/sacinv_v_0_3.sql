-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 17-07-2013 a las 00:23:10
-- Versión del servidor: 5.5.24-log
-- Versión de PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `sacinv_v_0_3`
--
CREATE DATABASE `sacinv_v_0_3` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sacinv_v_0_3`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos`
--

CREATE TABLE IF NOT EXISTS `asientos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_cta` int(10) NOT NULL,
  `debita` decimal(9,2) NOT NULL,
  `acredita` decimal(9,2) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE IF NOT EXISTS `cuentas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_may` int(10) NOT NULL,
  `descipcion` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE IF NOT EXISTS `inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `destino` varchar(40) NOT NULL,
  `cant` int(10) NOT NULL,
  `c_unitario` double NOT NULL,
  `metodo` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=102 ;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `fecha`, `destino`, `cant`, `c_unitario`, `metodo`) VALUES
(89, '2013-07-13', '1', 120, 22, 'peps'),
(90, '2013-07-13', '1', 100, 30, 'peps'),
(91, '2013-07-13', '2', 120, 22, 'peps'),
(92, '2013-07-13', '2', 100, 30, 'peps'),
(93, '2013-07-13', '1', 120, 20, 'peps'),
(94, '2013-07-13', '1', 120, 20, 'peps'),
(95, '2013-07-13', '1', 120, 22, 'peps'),
(96, '2013-07-13', '2', 10, 20, 'peps'),
(97, '2013-07-13', '1', 120, 30, 'peps'),
(98, '2013-07-13', '1', 100, 25, 'peps'),
(99, '2013-07-13', '2', 21, 20, 'peps'),
(100, '2013-07-13', '2', 209, 20, 'peps'),
(101, '2013-07-13', '2', 1, 22, 'peps');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saldos`
--

CREATE TABLE IF NOT EXISTS `saldos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mov` int(11) NOT NULL,
  `cant` int(11) NOT NULL,
  `costo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `mov` (`mov`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

--
-- Volcado de datos para la tabla `saldos`
--

INSERT INTO `saldos` (`id`, `mov`, `cant`, `costo`) VALUES
(124, 89, 120, 22),
(125, 90, 120, 22),
(126, 90, 100, 30),
(127, 91, 100, 30),
(128, 93, 120, 20),
(129, 94, 240, 20),
(130, 95, 240, 20),
(131, 95, 120, 22),
(132, 96, 230, 20),
(133, 96, 120, 22),
(134, 97, 230, 20),
(135, 97, 120, 22),
(136, 97, 120, 30),
(137, 98, 230, 20),
(138, 98, 120, 22),
(139, 98, 120, 30),
(140, 98, 100, 25),
(141, 99, 209, 20),
(142, 99, 120, 22),
(143, 99, 120, 30),
(144, 99, 100, 25),
(145, 100, 120, 22),
(146, 100, 120, 30),
(147, 100, 100, 25),
(148, 101, 119, 22),
(149, 101, 120, 30),
(150, 101, 100, 25);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `saldos`
--
ALTER TABLE `saldos`
  ADD CONSTRAINT `saldos_ibfk_1` FOREIGN KEY (`mov`) REFERENCES `inventario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
