-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for superinstant1
CREATE DATABASE IF NOT EXISTS `superinstant1` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `superinstant1`;

-- Dumping structure for procedure superinstant1.add_clientes
DELIMITER //
CREATE PROCEDURE `add_clientes`(
	IN `cedula2` VARCHAR(50),
	IN `nombre2` VARCHAR(50),
	IN `telefono2` INT,
	IN `tarjeta2` INT,
	IN `calle2` VARCHAR(50),
	IN `lote2` INT,
	IN `barriada2` VARCHAR(50)
)
BEGIN
	INSERT INTO clientes(cedula, nombre, telefono, n_tarjeta, calle, lote, barriada)
	VALUES(cedula2, nombre2, telefono2, tarjeta2, calle2, lote2, barriada2);
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.add_compra
DELIMITER //
CREATE PROCEDURE `add_compra`(
	IN `cedula` VARCHAR(50),
	IN `suministro` INT,
	IN `sucursal` INT,
	IN `pago` VARCHAR(50),
	IN `cantidad2` INT,
	IN `total2` DECIMAL(10,2)
)
BEGIN
DECLARE restante, demanda, reorden, centro INT;
SELECT cantidad 
FROM contiene 
WHERE id_suministro = suministro AND ruc_sucursal = sucursal INTO restante;

/* VERIFICAR SI HAY SUFICIENTES PRODUCTOS*/
IF (restante >= cantidad2) THEN
	/*INSERTANDO LA COMPRA*/
	INSERT INTO compra (cédula, id_suministro, ruc_sucursal, forma_pago, cantidad, total)
	VALUES(cedula, suministro, sucursal, pago, cantidad2, total2);
	
	/*ACTUALIZAR EL PRODUCTO COMPRADO*/
	UPDATE contiene
	SET cantidad = cantidad - cantidad2
	WHERE id_suministro = suministro AND ruc_sucursal = sucursal;
	
	/*SELECCIONAR LA CANTIDAD RESTANTE DE ESE PRODUCTO*/
	SELECT cantidad FROM contiene 
	WHERE id_suministro = suministro AND ruc_sucursal = sucursal INTO restante;
	
	/* SUMA LAS VENTAS EN LOS ULTIMOS 30 DIAS*/
	SELECT SUM(cantidad) FROM compra WHERE FECHA BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() INTO demanda; 
	SET reorden =  demanda/30 * 5;
	
	/*VERIFICA SI ESTA POR DEBAJO DEL PUNTO DE REORDEN*/
	IF (reorden >= restante) THEN
		SELECT ruc_centro FROM sucursales WHERE ruc_sucursal = sucursal INTO centro;
		INSERT INTO notifica(id_suministro, ruc_sucursal, ruc_centro, cantidad)
		VALUES (suministro, sucursal, centro, reorden);
	END IF;
	
	SELECT 'Procesado Correctamente' AS inserted;
ELSE
	SELECT 'No hay suficientes productos' AS inserted;
END IF;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.add_reabastece
DELIMITER //
CREATE PROCEDURE `add_reabastece`(
	IN `suministro_id` INT,
	IN `sucursal_ruc` INT,
	IN `centro_ruc` INT,
	IN `precio2` DECIMAL(10,2),
	IN `cantidad2` INT
)
BEGIN
	DECLARE precio3 DECIMAL(10,2);
	INSERT INTO reabastece(id_suministro, ruc_sucursal, ruc_centro, precio, cantidad)
	VALUES(suministro_id, sucursal_ruc, centro_ruc, precio2, cantidad2);
	
	IF EXISTS (SELECT * FROM contiene WHERE id_suministro = suministro_id AND ruc_sucursal = sucursal_ruc) THEN
		UPDATE contiene
		SET cantidad = cantidad + cantidad2
		WHERE id_suministro = suministro_id AND ruc_sucursal = sucursal_ruc;
	ELSE
		SET precio3 = precio2 * ROUND((RAND()*100+30)/100 + 1, 1);
		INSERT INTO contiene
		VALUES(suministro_id, sucursal_ruc, cantidad2, precio3);
	END IF;

END//
DELIMITER ;

-- Dumping structure for table superinstant1.administradores
CREATE TABLE IF NOT EXISTS `administradores` (
  `cédula` varchar(20) NOT NULL,
  `ruc_centro` int(11) NOT NULL,
  `correo` varchar(25) NOT NULL,
  `contrasena` int(11) NOT NULL,
  `usuario` varchar(25) NOT NULL,
  PRIMARY KEY (`cédula`,`ruc_centro`),
  KEY `FK_administradores_ruc_centro` (`ruc_centro`),
  CONSTRAINT `FK_administradores_ruc_centro` FOREIGN KEY (`ruc_centro`) REFERENCES `centros` (`ruc_centro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.administradores: ~0 rows (approximately)
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
INSERT INTO `administradores` (`cédula`, `ruc_centro`, `correo`, `contrasena`, `usuario`) VALUES
	('123', 123, 'test@test.com', 123, 'Angel');
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;

-- Dumping structure for table superinstant1.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.categorias: ~4 rows (approximately)
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` (`id_categoria`, `descripcion`) VALUES
	(1, 'Sodas'),
	(2, 'Agua Embotellada'),
	(3, 'Jugos'),
	(4, 'Vitaminas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;

-- Dumping structure for table superinstant1.centros
CREATE TABLE IF NOT EXISTS `centros` (
  `ruc_centro` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `direccion` varchar(25) NOT NULL,
  `telefono` int(11) NOT NULL,
  `correo` varchar(25) NOT NULL,
  PRIMARY KEY (`ruc_centro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.centros: ~0 rows (approximately)
/*!40000 ALTER TABLE `centros` DISABLE KEYS */;
INSERT INTO `centros` (`ruc_centro`, `nombre`, `direccion`, `telefono`, `correo`) VALUES
	(123, 'testCentro', 'Tocumen', 12345678, 'centro@test.com'),
	(456, 'testCentro2', 'Veraguas', 501789, 'centro2@test.com');
/*!40000 ALTER TABLE `centros` ENABLE KEYS */;

-- Dumping structure for table superinstant1.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `cédula` varchar(20) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `n_tarjeta` int(11) DEFAULT NULL,
  `calle` varchar(50) DEFAULT NULL,
  `lote` int(11) DEFAULT NULL,
  `barriada` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cédula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.clientes: ~0 rows (approximately)
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` (`cédula`, `nombre`, `telefono`, `n_tarjeta`, `calle`, `lote`, `barriada`) VALUES
	('1111111', 'test', 6123456, 123456789, '13', 24, 'Llanos');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;

-- Dumping structure for table superinstant1.compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id_factura` int(11) NOT NULL AUTO_INCREMENT,
  `cédula` varchar(20) NOT NULL,
  `id_suministro` int(11) NOT NULL,
  `ruc_sucursal` int(11) NOT NULL,
  `forma_pago` varchar(50) NOT NULL DEFAULT '',
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_factura`,`cédula`,`id_suministro`,`ruc_sucursal`),
  KEY `FK_compra_cédula` (`cédula`),
  KEY `FK_compra_id_suministro` (`id_suministro`),
  KEY `FK_compra_ruc_sucursales` (`ruc_sucursal`),
  CONSTRAINT `FK_compra_cédula` FOREIGN KEY (`cédula`) REFERENCES `clientes` (`cédula`),
  CONSTRAINT `FK_compra_id_suministro` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`),
  CONSTRAINT `FK_compra_ruc_sucursales` FOREIGN KEY (`ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.compra: ~5 rows (approximately)
/*!40000 ALTER TABLE `compra` DISABLE KEYS */;
INSERT INTO `compra` (`id_factura`, `cédula`, `id_suministro`, `ruc_sucursal`, `forma_pago`, `cantidad`, `fecha`, `total`) VALUES
	(2, '1111111', 1, 111, 'cr', 20, '2020-11-06 21:59:28', NULL),
	(3, '1111111', 1, 111, 'CR', 20, '2020-11-06 22:14:49', NULL),
	(4, '1111111', 1, 111, 'cr', 20, '2020-11-06 22:19:03', NULL),
	(5, '1111111', 1, 111, 'CR', 20, '2020-11-06 22:22:51', NULL),
	(7, '1111111', 1, 111, 'CR', 250, '2020-11-06 22:49:24', NULL),
	(8, '1111111', 1, 111, 'CR', 200, '2020-11-06 22:51:04', NULL);
/*!40000 ALTER TABLE `compra` ENABLE KEYS */;

-- Dumping structure for table superinstant1.contiene
CREATE TABLE IF NOT EXISTS `contiene` (
  `id_suministro` int(11) NOT NULL,
  `ruc_sucursal` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_suministro`,`ruc_sucursal`),
  KEY `FK_contiene_ruc_sucursales` (`ruc_sucursal`),
  CONSTRAINT `FK_contiene_id_suministro` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_contiene_ruc_sucursales` FOREIGN KEY (`ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.contiene: ~81 rows (approximately)
/*!40000 ALTER TABLE `contiene` DISABLE KEYS */;
INSERT INTO `contiene` (`id_suministro`, `ruc_sucursal`, `cantidad`, `precio`) VALUES
	(1, 111, 60, 19.20),
	(1, 112, 50, 12.58),
	(2, 111, 250, 22.50),
	(3, 111, 100, 24.50),
	(4, 111, 150, 29.50),
	(5, 111, 50, 19.50),
	(5, 112, 100, 15.00),
	(9, 111, 25, 19.70),
	(10, 111, 65, 22.80),
	(11, 111, 89, 25.50),
	(12, 111, 140, 30.00),
	(13, 111, 165, 19.99),
	(17, 111, 230, 20.50),
	(18, 111, 140, 23.80),
	(19, 111, 125, 26.60),
	(20, 111, 56, 28.99),
	(21, 111, 79, 20.50),
	(22, 111, 140, 23.80),
	(23, 111, 235, 26.60),
	(24, 111, 199, 28.99),
	(25, 111, 40, 19.99),
	(27, 111, 99, 24.99),
	(29, 111, 57, 19.99),
	(31, 111, 111, 24.99),
	(32, 111, 177, 29.99),
	(33, 111, 145, 12.99),
	(36, 111, 150, 15.99),
	(37, 111, 200, 19.99),
	(38, 111, 400, 22.50),
	(39, 111, 240, 26.50),
	(39, 112, 40, 20.00),
	(40, 111, 120, 24.99),
	(41, 111, 230, 29.99),
	(42, 111, 250, 34.50),
	(43, 111, 140, 45.00),
	(44, 111, 370, 56.00),
	(45, 111, 99, 29.99),
	(46, 111, 170, 34.50),
	(46, 112, 100, 20.00),
	(47, 111, 207, 45.00),
	(48, 111, 270, 56.00),
	(49, 111, 54, 29.99),
	(50, 111, 79, 34.50),
	(51, 111, 165, 45.00),
	(52, 111, 168, 56.00),
	(53, 111, 214, 29.99),
	(54, 111, 257, 34.50),
	(55, 111, 179, 45.00),
	(56, 111, 199, 56.00),
	(57, 111, 246, 29.99),
	(57, 112, 100, 40.00),
	(58, 111, 211, 34.50),
	(59, 111, 189, 45.00),
	(60, 111, 145, 56.00),
	(61, 111, 400, 34.99),
	(61, 112, 100, 44.12),
	(62, 111, 210, 39.99),
	(63, 111, 260, 45.99),
	(64, 111, 140, 51.50),
	(65, 111, 125, 35.99),
	(65, 112, 100, 32.00),
	(66, 111, 187, 42.50),
	(67, 111, 350, 47.99),
	(68, 111, 320, 53.99),
	(69, 111, 317, 36.99),
	(69, 112, 100, 44.00),
	(70, 111, 146, 44.99),
	(71, 111, 210, 49.99),
	(72, 111, 260, 56.75),
	(73, 111, 100, 36.99),
	(74, 111, 450, 44.99),
	(75, 111, 201, 49.99),
	(76, 111, 179, 56.75),
	(77, 111, 199, 38.99),
	(78, 111, 240, 44.99),
	(79, 111, 289, 49.99),
	(80, 111, 390, 56.75),
	(81, 111, 140, 39.99),
	(82, 111, 180, 44.99),
	(83, 111, 240, 49.99),
	(84, 111, 178, 56.75);
/*!40000 ALTER TABLE `contiene` ENABLE KEYS */;

-- Dumping structure for procedure superinstant1.delete_notificaByID
DELIMITER //
CREATE PROCEDURE `delete_notificaByID`(
	IN `notifica_id` INT
)
BEGIN
	DELETE FROM notifica
	WHERE id_notifica = notifica_id;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_historial
DELIMITER //
CREATE PROCEDURE `get_historial`(
	IN `centro_ruc` INT
)
BEGIN
SELECT 
s.direccion AS 'sucursal',
p.descripcion AS 'producto',
c.descripcion AS 'categoria',
t.descripcion AS 'tamano',
r.cantidad AS 'cantidad',
r.precio AS 'precio',
r.fecha AS 'fecha'
FROM reabastece r JOIN suministros su ON r.id_suministro = su.id_suministro
						JOIN productos p ON su.id_producto = p.id_producto
						JOIN categorias c ON su.id_categoria = c.id_categoria
						JOIN tamanos t ON su.id_tamano = t.id_tamano
						JOIN sucursales s ON r.Ruc_sucursal = s.ruc_sucursal
WHERE r.ruc_centro = centro_ruc
ORDER BY fecha DESC;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_inventario
DELIMITER //
CREATE PROCEDURE `get_inventario`(
	IN `centro_ruc` INT
)
BEGIN
SELECT 
c.id_suministro,
c.ruc_sucursal,
p.descripcion AS producto,
su.id_tamano AS id_tamano,
t.descripcion AS tamano,
c.cantidad AS cantidad
FROM contiene c JOIN sucursales s ON c.ruc_sucursal = s.ruc_sucursal
					JOIN suministros su ON c.id_suministro = su.id_suministro
					JOIN productos p ON su.id_producto = p.id_producto
					JOIN tamanos t ON su.id_tamano = t.id_tamano
WHERE ruc_centro = centro_ruc;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_inventarioBySucursalRUC
DELIMITER //
CREATE PROCEDURE `get_inventarioBySucursalRUC`(
	IN `sucursal_ruc` INT
)
BEGIN

SELECT 
c.id_suministro,
c.ruc_sucursal,
su.nombre,
su.direccion,
p.descripcion AS producto,
cat.descripcion AS categoria,
t.descripcion AS tamaño,
c.cantidad,
c.precio
FROM contiene c JOIN suministros s ON c.id_suministro = s.id_suministro
 					JOIN sucursales su ON c.ruc_sucursal = su.ruc_sucursal
 					JOIN productos p ON s.id_producto = p.id_producto
 					JOIN tamanos t ON s.id_tamano = t.id_tamano
 					JOIN categorias cat ON s.id_categoria = cat.id_categoria
WHERE c.ruc_sucursal = sucursal_ruc;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_login
DELIMITER //
CREATE PROCEDURE `get_login`(
	IN `cedula2` INT,
	IN `contrasena2` INT
)
BEGIN
	SELECT *
	FROM administradores
	WHERE cédula = cedula2 AND contrasena = contrasena2;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_notificacionesByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_notificacionesByCentroRUC`(
	IN `centroRUC` INT
)
BEGIN
SELECT 
n.id_notifica AS id_notifica,
n.id_suministro AS id_suministro,
p.descripcion AS producto,
t.descripcion AS tamano,
c.descripcion AS categoria,
n.ruc_sucursal AS ruc_sucursal,
su.direccion AS direccion,
n.ruc_centro AS ruc_centro,
n.cantidad AS cantidad,
n.fecha AS fecha_pedido
FROM notifica n 	JOIN sucursales su ON n.ruc_sucursal = su.ruc_sucursal
						JOIN suministros s ON n.id_suministro = s.id_suministro
						JOIN productos p ON s.id_producto = p.id_producto
						JOIN tamanos t ON s.id_tamano = t.id_tamano
						JOIN categorias c ON s.id_categoria = c.id_categoria
WHERE n.ruc_centro = centroRUC;						
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_reabastece
DELIMITER //
CREATE PROCEDURE `get_reabastece`(
	IN `centro_ruc` INT
)
BEGIN
	SELECT 
	r.id_suministro,
	r.ruc_sucursal,
	p.descripcion AS producto,
	t.id_tamano AS id_tamano,
	t.descripcion AS tamano,
	r.precio AS precio,
	r.cantidad
	FROM reabastece r JOIN suministros s ON r.id_suministro = s.id_suministro
							JOIN productos p ON s.id_producto = p.id_producto
							JOIN tamanos t ON s.id_tamano = t.id_tamano
	WHERE ruc_centro = centro_ruc;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_sucursalesByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_sucursalesByCentroRUC`(
	IN `ruc_centro2` INT
)
BEGIN
SELECT *
FROM sucursales
WHERE ruc_centro = ruc_centro2;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_suministros
DELIMITER //
CREATE PROCEDURE `get_suministros`()
BEGIN
SELECT 
su.id_suministro,
su.id_producto,
p.descripcion AS 'producto',
su.id_categoria,
c.descripcion AS 'categoria',
su.id_tamano,
t.descripcion AS 'tamano'
FROM suministros su JOIN productos p ON su.id_producto = p.id_producto
							JOIN categorias c ON su.id_categoria = c.id_categoria
							JOIN tamanos t ON su.id_tamano = t.id_tamano;
END//
DELIMITER ;

-- Dumping structure for table superinstant1.notifica
CREATE TABLE IF NOT EXISTS `notifica` (
  `id_notifica` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_suministro` int(11) NOT NULL,
  `ruc_sucursal` int(11) NOT NULL,
  `ruc_centro` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notifica`),
  KEY `FK_notifica_ruc_centro` (`ruc_centro`),
  KEY `FK_notifica_ruc_sucursale` (`ruc_sucursal`),
  KEY `id_suministro` (`id_suministro`),
  CONSTRAINT `FK_notifica_id_suministro` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_notifica_ruc_centro` FOREIGN KEY (`ruc_centro`) REFERENCES `centros` (`ruc_centro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_notifica_ruc_sucursale` FOREIGN KEY (`ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.notifica: ~1 rows (approximately)
/*!40000 ALTER TABLE `notifica` DISABLE KEYS */;
INSERT INTO `notifica` (`id_notifica`, `id_suministro`, `ruc_sucursal`, `ruc_centro`, `cantidad`, `fecha`) VALUES
	(13, 1, 111, 123, 144, '2020-11-14 17:41:49');
/*!40000 ALTER TABLE `notifica` ENABLE KEYS */;

-- Dumping structure for table superinstant1.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.productos: ~18 rows (approximately)
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` (`id_producto`, `descripcion`) VALUES
	(1, 'Cola Original'),
	(2, 'Cola sin Azúcar'),
	(3, 'Cola de Fresa'),
	(4, 'Cola de Vainilla'),
	(5, 'Cola de Piña'),
	(6, 'Cola de Uva'),
	(7, 'Cola de Mandarina'),
	(8, 'Cola de Limón'),
	(9, 'Agua'),
	(10, 'Jugo de Piña'),
	(11, 'Jugo de Naranja'),
	(12, 'Jugo de Mandarina'),
	(13, 'Jugo de Verduras'),
	(14, 'Jugo de Ponche'),
	(15, 'Vitaminas C'),
	(16, 'Vitaminas D'),
	(17, 'Vitaminas E'),
	(18, 'Vitaminas Zinc'),
	(19, 'Vitaminas Magnesio'),
	(20, 'Vitaminas Complejo B');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;

-- Dumping structure for table superinstant1.reabastece
CREATE TABLE IF NOT EXISTS `reabastece` (
  `id_factura` int(11) NOT NULL AUTO_INCREMENT,
  `id_suministro` int(11) NOT NULL,
  `Ruc_sucursal` int(11) NOT NULL,
  `Ruc_centro` int(11) NOT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_factura`,`id_suministro`,`Ruc_sucursal`,`Ruc_centro`),
  KEY `FK_reabastece_id_suministro` (`id_suministro`),
  KEY `FK_reabastece_ruc_centro` (`Ruc_centro`),
  KEY `FK_reabastece_ruc_sucursal` (`Ruc_sucursal`),
  CONSTRAINT `FK_reabastece_id_suministro` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reabastece_ruc_centro` FOREIGN KEY (`Ruc_centro`) REFERENCES `centros` (`ruc_centro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reabastece_ruc_sucursal` FOREIGN KEY (`Ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.reabastece: ~17 rows (approximately)
/*!40000 ALTER TABLE `reabastece` DISABLE KEYS */;
INSERT INTO `reabastece` (`id_factura`, `id_suministro`, `Ruc_sucursal`, `Ruc_centro`, `precio`, `cantidad`, `fecha`) VALUES
	(1, 1, 111, 123, 2.00, 5, '2020-11-14 22:00:34'),
	(2, 1, 111, 123, 20.00, 20, '2020-11-15 10:46:42'),
	(3, 1, 111, 123, 24.00, 12, '2020-11-15 10:46:58'),
	(4, 5, 111, 123, 25.00, 2, '2020-11-15 10:47:13'),
	(5, 13, 111, 123, 25.00, 50, '2020-11-15 10:47:25'),
	(6, 33, 111, 123, 2.00, 5, '2020-11-15 17:21:19'),
	(7, 41, 111, 123, 5.20, 22, '2020-11-16 00:14:21'),
	(8, 1, 111, 123, 20.00, 50, '2020-11-16 10:36:26'),
	(9, 1, 112, 123, 10.00, 50, '2020-11-16 10:37:46'),
	(10, 39, 112, 123, 10.00, 40, '2020-11-16 10:45:19'),
	(11, 47, 111, 123, 20.00, 50, '2020-11-16 10:58:37'),
	(12, 57, 112, 123, 20.00, 100, '2020-11-16 10:59:03'),
	(13, 46, 112, 123, 20.00, 100, '2020-11-16 10:59:43'),
	(14, 61, 112, 123, 20.00, 100, '2020-11-16 11:01:16'),
	(15, 65, 112, 123, 20.00, 100, '2020-11-16 11:03:39'),
	(16, 69, 112, 123, 20.00, 100, '2020-11-16 11:04:08'),
	(17, 5, 112, 123, 10.00, 100, '2020-11-16 12:58:41');
/*!40000 ALTER TABLE `reabastece` ENABLE KEYS */;

-- Dumping structure for table superinstant1.sucursales
CREATE TABLE IF NOT EXISTS `sucursales` (
  `ruc_sucursal` int(11) NOT NULL,
  `ruc_centro` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(25) NOT NULL,
  `telefono` int(11) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  PRIMARY KEY (`ruc_sucursal`),
  KEY `FK_sucursales_centros` (`ruc_centro`),
  CONSTRAINT `FK_sucursales_centros` FOREIGN KEY (`ruc_centro`) REFERENCES `centros` (`ruc_centro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.sucursales: ~2 rows (approximately)
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` (`ruc_sucursal`, `ruc_centro`, `nombre`, `correo`, `telefono`, `direccion`) VALUES
	(111, 123, 'XTRA', 'sucursal@test.com', 12345678, 'Condado'),
	(112, 123, 'XTRA', 'sucursal2@test.com', 5647890, 'Tocumen');
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;

-- Dumping structure for table superinstant1.suministros
CREATE TABLE IF NOT EXISTS `suministros` (
  `id_suministro` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_suministro`),
  KEY `FK_suministros_id_categoria` (`id_categoria`),
  KEY `FK_suministros_id_tamano` (`id_tamano`),
  KEY `FK_suministros_id_producto` (`id_producto`),
  CONSTRAINT `FK_suministros_id_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `FK_suministros_id_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `FK_suministros_id_tamano` FOREIGN KEY (`id_tamano`) REFERENCES `tamanos` (`id_tamano`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.suministros: ~73 rows (approximately)
/*!40000 ALTER TABLE `suministros` DISABLE KEYS */;
INSERT INTO `suministros` (`id_suministro`, `id_producto`, `id_categoria`, `id_tamano`, `fecha_agregado`) VALUES
	(1, 1, 1, 1, '2020-11-06 15:36:49'),
	(2, 1, 1, 2, '2020-11-06 15:37:23'),
	(3, 1, 1, 3, '2020-11-06 15:37:44'),
	(4, 1, 1, 4, '2020-11-06 15:37:56'),
	(5, 2, 1, 1, '2020-11-06 15:39:30'),
	(9, 3, 1, 1, '2020-11-06 15:43:40'),
	(10, 3, 1, 2, '2020-11-06 15:43:46'),
	(11, 3, 1, 3, '2020-11-06 15:44:03'),
	(12, 3, 1, 4, '2020-11-06 15:44:08'),
	(13, 4, 1, 1, '2020-11-06 15:44:21'),
	(17, 5, 1, 1, '2020-11-06 15:44:50'),
	(18, 5, 1, 2, '2020-11-06 15:44:54'),
	(19, 5, 1, 3, '2020-11-06 15:44:57'),
	(20, 5, 1, 4, '2020-11-06 15:45:01'),
	(21, 6, 1, 1, '2020-11-06 15:45:14'),
	(22, 6, 1, 2, '2020-11-06 15:45:21'),
	(23, 6, 1, 3, '2020-11-06 15:45:24'),
	(24, 6, 1, 4, '2020-11-06 15:45:30'),
	(25, 7, 1, 1, '2020-11-06 15:45:38'),
	(27, 7, 1, 3, '2020-11-06 15:45:44'),
	(29, 8, 1, 1, '2020-11-06 15:46:39'),
	(31, 8, 1, 3, '2020-11-06 15:46:49'),
	(32, 8, 1, 4, '2020-11-06 15:46:53'),
	(33, 9, 2, 5, '2020-11-06 15:46:57'),
	(36, 9, 2, 6, '2020-11-06 15:47:10'),
	(37, 9, 2, 7, '2020-11-06 15:47:31'),
	(38, 9, 2, 8, '2020-11-06 15:47:37'),
	(39, 9, 2, 9, '2020-11-06 15:47:42'),
	(40, 9, 2, 10, '2020-11-06 15:47:56'),
	(41, 10, 3, 5, '2020-11-06 15:48:18'),
	(42, 10, 3, 11, '2020-11-06 15:48:29'),
	(43, 10, 3, 12, '2020-11-06 15:48:35'),
	(44, 10, 3, 13, '2020-11-06 15:48:44'),
	(45, 11, 3, 5, '2020-11-06 15:48:49'),
	(46, 11, 3, 11, '2020-11-06 15:48:53'),
	(47, 11, 3, 12, '2020-11-06 15:48:58'),
	(48, 11, 3, 13, '2020-11-06 15:49:02'),
	(49, 12, 3, 5, '2020-11-06 15:49:16'),
	(50, 12, 3, 11, '2020-11-06 15:49:20'),
	(51, 12, 3, 12, '2020-11-06 15:49:24'),
	(52, 12, 3, 13, '2020-11-06 15:49:30'),
	(53, 13, 3, 5, '2020-11-06 15:49:40'),
	(54, 13, 3, 11, '2020-11-06 15:49:44'),
	(55, 13, 3, 12, '2020-11-06 15:49:48'),
	(56, 13, 3, 13, '2020-11-06 15:49:53'),
	(57, 14, 3, 5, '2020-11-06 15:49:58'),
	(58, 14, 3, 11, '2020-11-06 15:50:03'),
	(59, 14, 3, 12, '2020-11-06 15:50:07'),
	(60, 14, 3, 13, '2020-11-06 15:50:11'),
	(61, 15, 4, 14, '2020-11-06 15:50:24'),
	(62, 15, 4, 15, '2020-11-06 15:50:28'),
	(63, 15, 4, 16, '2020-11-06 15:50:32'),
	(64, 15, 4, 17, '2020-11-06 15:50:41'),
	(65, 16, 4, 14, '2020-11-06 15:51:43'),
	(66, 16, 4, 15, '2020-11-06 15:51:49'),
	(67, 16, 4, 16, '2020-11-06 15:51:53'),
	(68, 16, 4, 17, '2020-11-06 15:51:58'),
	(69, 17, 4, 14, '2020-11-06 15:52:04'),
	(70, 17, 4, 15, '2020-11-06 15:52:10'),
	(71, 17, 4, 16, '2020-11-06 15:52:16'),
	(72, 17, 4, 17, '2020-11-06 15:52:21'),
	(73, 18, 4, 14, '2020-11-06 15:52:27'),
	(74, 18, 4, 15, '2020-11-06 15:52:32'),
	(75, 18, 4, 16, '2020-11-06 15:52:38'),
	(76, 18, 4, 17, '2020-11-06 15:52:45'),
	(77, 19, 4, 14, '2020-11-06 15:52:56'),
	(78, 19, 4, 15, '2020-11-06 15:53:01'),
	(79, 19, 4, 16, '2020-11-06 15:53:06'),
	(80, 19, 4, 17, '2020-11-06 15:53:11'),
	(81, 20, 4, 14, '2020-11-06 15:53:17'),
	(82, 20, 4, 15, '2020-11-06 15:53:22'),
	(83, 20, 4, 16, '2020-11-06 15:53:28'),
	(84, 20, 4, 17, '2020-11-06 15:53:40');
/*!40000 ALTER TABLE `suministros` ENABLE KEYS */;

-- Dumping structure for table superinstant1.tamanos
CREATE TABLE IF NOT EXISTS `tamanos` (
  `id_tamano` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tamano`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.tamanos: ~17 rows (approximately)
/*!40000 ALTER TABLE `tamanos` DISABLE KEYS */;
INSERT INTO `tamanos` (`id_tamano`, `descripcion`) VALUES
	(1, 'Lata'),
	(2, 'Botella'),
	(3, 'Litro'),
	(4, '2 Litros'),
	(5, '8 Onzas'),
	(6, '12 Onzas'),
	(7, '20 Onzas'),
	(8, '5 Litros'),
	(9, 'Con Garrafón'),
	(10, 'Del Garrafón'),
	(11, '16 Onzas'),
	(12, 'Medio Galón'),
	(13, '1 Galón'),
	(14, '25 Cápsulas'),
	(15, '50 Cápsulas'),
	(16, '100 Cápsulas'),
	(17, '200 Cápsulas');
/*!40000 ALTER TABLE `tamanos` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
