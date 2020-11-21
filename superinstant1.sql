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
	IN `tarjeta2` VARCHAR(50),
	IN `direccion2` VARCHAR(50)
)
BEGIN
	INSERT INTO clientes(cédula, nombre, telefono, n_tarjeta, direccion)
	VALUES(cedula2, nombre2, telefono2, tarjeta2, direccion2);
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.add_compra
DELIMITER //
CREATE PROCEDURE `add_compra`(
	IN `id_factura2` INT,
	IN `cedula` VARCHAR(50),
	IN `suministro` INT,
	IN `sucursal` INT,
	IN `pago` VARCHAR(50),
	IN `cantidad2` INT
)
BEGIN
DECLARE restante, demanda, reorden, centro INT;
SELECT cantidad 
FROM contiene 
WHERE id_suministro = suministro AND ruc_sucursal = sucursal INTO restante;

/* VERIFICAR SI HAY SUFICIENTES PRODUCTOS*/
IF (restante >= cantidad2) THEN
	/*INSERTANDO LA COMPRA*/
	INSERT INTO compra (id_factura, cédula, id_suministro, ruc_sucursal, forma_pago, cantidad)
	VALUES(id_factura2, cedula, suministro, sucursal, pago, cantidad2);
	
	/*ACTUALIZAR EL PRODUCTO COMPRADO*/
	UPDATE contiene
	SET cantidad = cantidad - cantidad2
	WHERE id_suministro = suministro AND ruc_sucursal = sucursal;
	
	/*SELECCIONAR LA CANTIDAD RESTANTE DE ESE PRODUCTO*/
	SELECT cantidad FROM contiene 
	WHERE id_suministro = suministro AND ruc_sucursal = sucursal INTO restante;
	
	/* SUMA LAS VENTAS EN LOS ULTIMOS 30 DIAS*/
	SELECT AVG(cantidad) FROM compra WHERE FECHA BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() AND id_suministro = suministro AND ruc_sucursal = sucursal INTO demanda; 
	SET reorden =  demanda * 5;
	
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
  `n_tarjeta` varchar(50) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cédula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.clientes: ~7 rows (approximately)
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` (`cédula`, `nombre`, `telefono`, `n_tarjeta`, `direccion`) VALUES
	('1111111', 'test', 6123456, '123456789', NULL),
	('12345678', 'Test Test', 12345678, '9c1185a5c5e9fc54612808977ee8f548b2258d31', 'Panama'),
	('1323', '131', 23, '9c1185a5c5e9fc54612808977ee8f548b2258d31', '3213'),
	('342325323', '23234234', 3424324, '9c1185a5c5e9fc54612808977ee8f548b2258d31', '234234'),
	('4134', '31413', 4134, '9c1185a5c5e9fc54612808977ee8f548b2258d31', '1341343'),
	('543534543', 'Angel', 1441, '9c1185a5c5e9fc54612808977ee8f548b2258d31', '1414'),
	('67567', '567567', 567567, '9c1185a5c5e9fc54612808977ee8f548b2258d31', '567567');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;

-- Dumping structure for table superinstant1.compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id_factura` int(11) NOT NULL,
  `cédula` varchar(20) NOT NULL,
  `id_suministro` int(11) NOT NULL,
  `ruc_sucursal` int(11) NOT NULL,
  `forma_pago` varchar(50) NOT NULL DEFAULT '',
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_factura`,`cédula`,`id_suministro`,`ruc_sucursal`),
  KEY `FK_compra_cédula` (`cédula`),
  KEY `FK_compra_id_suministro` (`id_suministro`),
  KEY `FK_compra_ruc_sucursales` (`ruc_sucursal`),
  CONSTRAINT `FK_compra_cédula` FOREIGN KEY (`cédula`) REFERENCES `clientes` (`cédula`),
  CONSTRAINT `FK_compra_id_suministro` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`),
  CONSTRAINT `FK_compra_ruc_sucursales` FOREIGN KEY (`ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.compra: ~16 rows (approximately)
/*!40000 ALTER TABLE `compra` DISABLE KEYS */;
INSERT INTO `compra` (`id_factura`, `cédula`, `id_suministro`, `ruc_sucursal`, `forma_pago`, `cantidad`, `fecha`) VALUES
	(2, '1111111', 1, 111, 'cr', 20, '2020-11-06 21:59:28'),
	(2, '1111111', 2, 111, 'cr', 60, '2020-11-18 19:01:20'),
	(3, '1111111', 1, 111, 'CR', 20, '2020-11-06 22:14:49'),
	(4, '1111111', 1, 111, 'cr', 20, '2020-11-06 22:19:03'),
	(5, '1111111', 1, 111, 'CR', 20, '2020-11-06 22:22:51'),
	(7, '1111111', 1, 111, 'CR', 250, '2020-11-06 22:49:24'),
	(8, '1111111', 1, 111, 'CR', 200, '2020-11-06 22:51:04'),
	(9, '1323', 3, 111, 'on', 1, '2020-11-21 13:58:28'),
	(9, '1323', 20, 111, 'on', 1, '2020-11-21 13:58:28'),
	(10, '12345678', 3, 111, 'Efectivo', 1, '2020-11-21 14:00:16'),
	(10, '12345678', 20, 111, 'Efectivo', 1, '2020-11-21 14:00:16'),
	(11, '342325323', 3, 111, 'Efectivo', 1, '2020-11-21 14:01:36'),
	(11, '342325323', 20, 111, 'Efectivo', 1, '2020-11-21 14:01:36'),
	(12, '543534543', 3, 111, 'Efectivo', 5, '2020-11-21 14:06:17'),
	(13, '67567', 3, 111, 'Efectivo', 2, '2020-11-21 14:08:23'),
	(14, '4134', 3, 111, 'Efectivo', 80, '2020-11-21 14:08:44');
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
	(1, 111, 224, 19.20),
	(1, 112, 73, 12.58),
	(2, 111, 250, 22.50),
	(3, 111, 10, 24.50),
	(4, 111, 150, 29.50),
	(5, 111, 62, 19.50),
	(5, 112, 100, 15.00),
	(9, 111, 25, 19.70),
	(10, 111, 65, 22.80),
	(11, 111, 89, 25.50),
	(12, 111, 140, 30.00),
	(13, 111, 165, 19.99),
	(17, 111, 230, 20.50),
	(18, 111, 140, 23.80),
	(19, 111, 125, 26.60),
	(20, 111, 53, 28.99),
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
	(44, 112, 20, 54.40),
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

-- Dumping structure for procedure superinstant1.get_allSucursales
DELIMITER //
CREATE PROCEDURE `get_allSucursales`()
BEGIN
	SELECT *
	FROM sucursales;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_factura
DELIMITER //
CREATE PROCEDURE `get_factura`()
BEGIN
	SELECT *
	FROM
	compra
	ORDER BY id_factura DESC
		LIMIT 1;
	

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
s.imagen AS imagen,
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table superinstant1.notifica: ~8 rows (approximately)
/*!40000 ALTER TABLE `notifica` DISABLE KEYS */;
INSERT INTO `notifica` (`id_notifica`, `id_suministro`, `ruc_sucursal`, `ruc_centro`, `cantidad`, `fecha`) VALUES
	(15, 3, 111, 123, 99, '2020-11-21 13:58:28'),
	(16, 20, 111, 123, 99, '2020-11-21 13:58:28'),
	(17, 3, 111, 123, 99, '2020-11-21 14:00:16'),
	(18, 20, 111, 123, 99, '2020-11-21 14:00:16'),
	(19, 3, 111, 123, 99, '2020-11-21 14:01:36'),
	(20, 20, 111, 123, 99, '2020-11-21 14:01:36'),
	(21, 3, 111, 123, 100, '2020-11-21 14:06:17'),
	(22, 3, 111, 123, 15, '2020-11-21 14:08:44');
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

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
	(17, 5, 112, 123, 10.00, 100, '2020-11-16 12:58:41'),
	(18, 1, 111, 123, 0.43, 144, '2020-11-17 22:16:04'),
	(19, 1, 111, 123, 20.00, 20, '2020-11-20 21:11:41'),
	(20, 44, 112, 123, 34.00, 20, '2020-11-20 21:12:10'),
	(21, 5, 111, 123, 12.00, 12, '2020-11-21 08:44:21'),
	(22, 1, 112, 123, 23.00, 23, '2020-11-21 08:50:10');
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

-- Dumping data for table superinstant1.sucursales: ~1 rows (approximately)
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
  `imagen` varchar(1000) DEFAULT NULL,
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
INSERT INTO `suministros` (`id_suministro`, `id_producto`, `id_categoria`, `id_tamano`, `imagen`, `fecha_agregado`) VALUES
	(1, 1, 1, 1, 'https://www.lafruteria.cl/wp-content/uploads/2020/06/7801610220016-570x570.jpg', '2020-11-06 15:36:49'),
	(2, 1, 1, 2, 'https://www.kcerito.com/wp-content/uploads/2020/06/COCA-COLA-450-ML.1.jpg', '2020-11-06 15:37:23'),
	(3, 1, 1, 3, 'https://supermercado.carrefour.com.ar/media/catalog/product/cache/1/image/1000x/040ec09b1e35df139433887a97daa66f/7/7/7790895001413_01.jpg', '2020-11-06 15:37:44'),
	(4, 1, 1, 4, 'https://d2j6dbq0eux0bg.cloudfront.net/images/31337833/1528367859.jpg', '2020-11-06 15:37:56'),
	(5, 2, 1, 1, 'https://micocacola.vteximg.com.br/arquivos/ids/170494-292-292/7801610220122_1.jpg?v=637159333477130000', '2020-11-06 15:39:30'),
	(9, 3, 1, 1, 'https://cdn.shopify.com/s/files/1/0289/4823/4288/products/KIST_FRESA_LATA_1X12_374a09b5-dccd-4abd-a51f-ef94b93f7fe9.jpg?v=1595566176', '2020-11-06 15:43:40'),
	(10, 3, 1, 2, 'https://res.cloudinary.com/walmart-labs/image/upload/w_960,dpr_auto,f_auto,q_auto:best/gr/images/product-images/img_large/00750105530523L.jpg', '2020-11-06 15:43:46'),
	(11, 3, 1, 3, 'https://titan.vtexassets.com/arquivos/ids/167285-800-auto?width=800&height=auto&aspect=true', '2020-11-06 15:44:03'),
	(12, 3, 1, 4, 'https://titan.vtexassets.com/arquivos/ids/167284-800-auto?width=800&height=auto&aspect=true', '2020-11-06 15:44:08'),
	(13, 4, 1, 1, 'https://cdn.grupoelcorteingles.es/SGFM/dctm/MEDIA03/201803/08/00113342801142____7__640x640.jpg', '2020-11-06 15:44:21'),
	(17, 5, 1, 1, 'https://cdn2.golosinasysnacks.com/12128-large_default/fanta-pina-12-latas-de-355ml.jpg', '2020-11-06 15:44:50'),
	(18, 5, 1, 2, 'https://www.superseis.com.py/images/thumbs/0214085.jpeg', '2020-11-06 15:44:54'),
	(19, 5, 1, 3, 'https://titan.vtexassets.com/arquivos/ids/167286-800-auto?width=800&height=auto&aspect=true', '2020-11-06 15:44:57'),
	(20, 5, 1, 4, 'https://images-na.ssl-images-amazon.com/images/I/71rbenFNzRL._SL1500_.jpg', '2020-11-06 15:45:01'),
	(21, 6, 1, 1, 'https://www.orientalmarket.es/shop/13791-large_default/fanta-uva-350ml.jpg', '2020-11-06 15:45:14'),
	(22, 6, 1, 2, 'https://www.chedraui.com.mx/medias/7501055327041-00-CH515Wx515H?context=bWFzdGVyfHJvb3R8NDA3OTd8aW1hZ2UvanBlZ3xoMjUvaGM2LzEwMTUwNjkwNzUwNDk0LmpwZ3xmYWIxMmYyMDYxMGI5ZjgxMjU1ZWUwMTIwYmMyMGZmOWRhOTcxOTczNzc4OTI5N2M1MzE3YjNiODczZTQwODMz', '2020-11-06 15:45:21'),
	(23, 6, 1, 3, 'https://com-coca-cola-latamclientes-content-hero-prod-images.s3.amazonaws.com/products/hidratacion/ec/powerade-uva-sin-calorias_1l_2018.png', '2020-11-06 15:45:24'),
	(24, 6, 1, 4, 'https://www.chedraui.com.mx/medias/7501055317462-00-CH515Wx515H?context=bWFzdGVyfHJvb3R8Mzk0MzB8aW1hZ2UvanBlZ3xoNGMvaDFlLzEwMjIzMjA0ODkyNzAyLmpwZ3wxN2ZkMmRlZDRjNzZlY2U1MDU0MjI2ZDExZmFkMDcyZjE3OWI3OTJiNDQ0MDJjNjgwNDQ2NjRiOWRhYjNiNjUz', '2020-11-06 15:45:30'),
	(25, 7, 1, 1, 'https://mercadoselroble.com/esp/pics/prodcutos/7501441603117.jpg', '2020-11-06 15:45:38'),
	(27, 7, 1, 3, 'https://www.casa-segal.com/wp-content/uploads/2020/03/fanta-naranja-15L-almacen-gaseosas-casa-segal-mendoza-600x600.jpg', '2020-11-06 15:45:44'),
	(29, 8, 1, 1, 'https://www.lasirena.es/37368-large_default/fanta-limon.jpg', '2020-11-06 15:46:39'),
	(31, 8, 1, 3, 'https://www.supertotus.com/assets/productos/fantalimon2l.jpg', '2020-11-06 15:46:49'),
	(32, 8, 1, 4, 'https://supermercado.carrefour.com.ar/media/catalog/product/cache/1/image/1000x/040ec09b1e35df139433887a97daa66f/7/7/7790895007385_02.jpg', '2020-11-06 15:46:53'),
	(33, 9, 2, 5, 'https://images-na.ssl-images-amazon.com/images/I/21fiZDcg5FL._QL70_ML2_.jpg', '2020-11-06 15:46:57'),
	(36, 9, 2, 6, 'https://shoperia.encuentra24.com/63764-large_default/aqua-cristalina-12-onz-24x1.jpg', '2020-11-06 15:47:10'),
	(37, 9, 2, 7, 'https://res.cloudinary.com/almacendo/image/upload/v1526570403/Agua/Agua-Dasani-_591ml_-Front.jpg', '2020-11-06 15:47:31'),
	(38, 9, 2, 8, 'https://www.superama.com.mx/Content/images/products/img_large/0750108680113L.jpg', '2020-11-06 15:47:37'),
	(39, 9, 2, 9, 'https://www.chedraui.com.mx/medias/7501055305681-00-CH1200Wx1200H?context=bWFzdGVyfHJvb3R8MjQyNzAyfGltYWdlL2pwZWd8aDVlL2g4Zi8xMDE1MDY1NjI0NTc5MC5qcGd8NmY0MDk0MmQ2OTYwZDA1ODkyMGY3YzI1NDQzMmMzYTEyMjFiODRhNTZlNmJkMGYxNzFkNmFlZGE0ZDMyNzc4MQ', '2020-11-06 15:47:42'),
	(40, 9, 2, 10, 'https://http2.mlstatic.com/dispensador-agua-garrafon-recargable-usb-electrico-bomba-D_Q_NP_840470-MLM41916631551_052020-F.webp', '2020-11-06 15:47:56'),
	(41, 10, 3, 5, 'https://cdn2.golosinasysnacks.com/12128-large_default/fanta-pina-12-latas-de-355ml.jpg', '2020-11-06 15:48:18'),
	(42, 10, 3, 11, 'https://www.superseis.com.py/images/thumbs/0214085.jpeg', '2020-11-06 15:48:29'),
	(43, 10, 3, 12, 'https://www.chedraui.com.mx/medias/7501055317455-00-CH1200Wx1200H?context=bWFzdGVyfHJvb3R8OTA3OTl8aW1hZ2UvanBlZ3xoMjIvaDQ0LzEwMjIzMzY5MjU2OTkwLmpwZ3w2OGZiNDE3OGY5MGViNzllMWY1NTdiZGJhOWZiODE1ZTE3YzY5Yzg4NDQxOTg2ZTJlOTI2NDk2MDZhNjFmYzVj', '2020-11-06 15:48:35'),
	(44, 10, 3, 13, 'https://images-na.ssl-images-amazon.com/images/I/71rbenFNzRL._SL1500_.jpg', '2020-11-06 15:48:44'),
	(45, 11, 3, 5, 'https://elmachetazo.com/pub/media/catalog/product/cache/89e492dca33e4630bae7e737f3495192/imp/ort/api-v1.1-file-public_files-pim-assets-cd-07-fd-5e-5efd07cd627ffe2c0f1db272-images-15-e7-91-5f-5f91e7152d6173d5d763c197-10026415.png', '2020-11-06 15:48:49'),
	(46, 11, 3, 11, 'https://com-coca-cola-latamclientes-content-hero-prod-images.s3.amazonaws.com/products/jugos/ec/dvfresh_naranja_1200ml_pet.png', '2020-11-06 15:48:53'),
	(47, 11, 3, 12, 'https://supercarnes.com/wp-content/uploads/2020/09/20200914_101815_clipped_rev_1.jpeg', '2020-11-06 15:48:58'),
	(48, 11, 3, 13, 'https://pickingupapp.com/wp-content/uploads/2020/06/d703ff612fe092e0ee3e1d0bd4651fc7ed69697a_223532_01.jpg', '2020-11-06 15:49:02'),
	(49, 12, 3, 5, 'https://cdnx.jumpseller.com/bepensa-dominicana/image/8535743/thumb/540/540?1588117262', '2020-11-06 15:49:16'),
	(50, 12, 3, 11, 'https://cdnx.jumpseller.com/bepensa-dominicana/image/10111813/thumb/260/260?1594221906', '2020-11-06 15:49:20'),
	(51, 12, 3, 12, 'https://jumbocolombiafood.vteximg.com.br/arquivos/ids/3430361-750-750/7702535015537.jpg?v=636958676598500000', '2020-11-06 15:49:24'),
	(52, 12, 3, 13, 'https://bblonatural.mx/wp-content/uploads/2020/07/mandarina-galon.jpg', '2020-11-06 15:49:30'),
	(53, 13, 3, 5, 'https://images-na.ssl-images-amazon.com/images/I/81vqKy582fL._SL1500_.jpg', '2020-11-06 15:49:40'),
	(54, 13, 3, 11, 'https://mountainmerchantvt.com/wp-content/uploads/2019/06/v8.jpg', '2020-11-06 15:49:44'),
	(55, 13, 3, 12, 'https://images.heb.com/is/image/HEBGrocery/000148544', '2020-11-06 15:49:48'),
	(56, 13, 3, 13, 'https://bblonatural.mx/wp-content/uploads/2020/07/verde-galon.jpg', '2020-11-06 15:49:53'),
	(57, 14, 3, 5, 'https://images-na.ssl-images-amazon.com/images/I/41hXT6y1tEL.jpg', '2020-11-06 15:49:58'),
	(58, 14, 3, 11, 'https://images-na.ssl-images-amazon.com/images/I/7111MsqSh0L._SL1500_.jpg', '2020-11-06 15:50:03'),
	(59, 14, 3, 12, 'https://supercarnes.com/wp-content/uploads/2020/07/20200713_083453.jpg', '2020-11-06 15:50:07'),
	(60, 14, 3, 13, 'https://merkadoo.com/pub/media/catalog/product/cache/54c8da64368501a23b32801443f04eac/1/_/1_7bq5wcidgdghwg4r.jpg', '2020-11-06 15:50:11'),
	(61, 15, 4, 14, 'https://www.farmacialeloir.com.ar/img/articulos/redoxitos_vitamina_c_gomitas_masticables_x_150_imagen1.jpg', '2020-11-06 15:50:24'),
	(62, 15, 4, 15, 'https://lh3.googleusercontent.com/proxy/VYpYF5mFdgWKMgg8pSk40O-tImH8XYi3zg9CWWzb43ya-NKpD-tcVlgWLyd4I-CD5RWWYKfMKfd1Cwwke3hVjyXJdxpv0nw', '2020-11-06 15:50:28'),
	(63, 15, 4, 16, 'https://images-na.ssl-images-amazon.com/images/I/81bmwCtXwkL._AC_SX425_.jpg', '2020-11-06 15:50:32'),
	(64, 15, 4, 17, 'https://simaro.global.ssl.fastly.net/media/catalog/product/cache/1/image/1800x/040ec09b1e35df139433887a97daa66f/S/p/Spring-Valley-masticables-de-vitamina-C-mltiple-de-fruta-sabores-dietticos-suplemento-200-ct_2.jpeg', '2020-11-06 15:50:41'),
	(65, 16, 4, 14, 'https://images-na.ssl-images-amazon.com/images/I/41WcgEC4mYL._AC_SX425_.jpg', '2020-11-06 15:51:43'),
	(66, 16, 4, 15, 'https://m.media-amazon.com/images/S/aplus-media/vc/94d577ca-560b-49c5-b1bc-7c9f7c4aa986.__CR125,0,750,1000_PT0_SX300_V1___.jpg', '2020-11-06 15:51:49'),
	(67, 16, 4, 16, 'https://www.vita33.com/images/productos/thumbnails/jamieson-vitamina-d3-1-000-iu-100-comprimidos-1-21065_thumb_434x520.jpg', '2020-11-06 15:51:53'),
	(68, 16, 4, 17, 'https://images-na.ssl-images-amazon.com/images/I/71h48-l928L._AC_SY679_.jpg', '2020-11-06 15:51:58'),
	(69, 17, 4, 14, 'https://quefarmacia.com/wp-content/uploads/2017/08/7502227421130_1.jpg', '2020-11-06 15:52:04'),
	(70, 17, 4, 15, 'https://farmaciauniversal.com/assets/sources/05058-vitamina-e-mason_1.jpg', '2020-11-06 15:52:10'),
	(71, 17, 4, 16, 'https://i.pinimg.com/originals/81/4b/ca/814bca1e8ad808b09a8a10db9e44abe0.jpg', '2020-11-06 15:52:16'),
	(72, 17, 4, 17, 'https://www.naturesbounty.es/-/media/naturesbountyspain/product-images/vitamin-e-200iu.jpg?h=360&w=270&la=es-ES&hash=2CFD4F7FB8A9EB90BE3428B2832A561D84DB5147', '2020-11-06 15:52:21'),
	(73, 18, 4, 14, 'https://images-na.ssl-images-amazon.com/images/I/51Fa%2B2DJxFL._AC_SX425_.jpg', '2020-11-06 15:52:27'),
	(74, 18, 4, 15, 'https://www.farmaciasknop.com/wp-content/uploads/2019/12/Vitamina-C-1000-Zinc-FDC.png', '2020-11-06 15:52:32'),
	(75, 18, 4, 16, 'https://tienda306.com/7776-large_default/quelato-de-zinc-natures-bounty-50-mg-100-pastillas.jpg', '2020-11-06 15:52:38'),
	(76, 18, 4, 17, 'https://http2.mlstatic.com/zinc-gluconato-50mg-250cap-acne-testosterona-vitamina-gnc-bc-D_NQ_NP_942623-MCO25620860894_052017-F.jpg', '2020-11-06 15:52:45'),
	(77, 19, 4, 14, 'https://d26lpennugtm8s.cloudfront.net/stores/796/648/products/magnesio-x-30-blister11-1a7ceb0a4efff0e60515690065174788-1024-1024.png', '2020-11-06 15:52:56'),
	(78, 19, 4, 15, 'https://images.jumpseller.com/store/mis-vitaminas/2087844/cloruro-de-magnesio-50-capsulas-natural-freshly.jpg?0', '2020-11-06 15:53:01'),
	(79, 19, 4, 16, 'https://i.pinimg.com/736x/22/4c/35/224c35e25bb7d12cfe3dc3a5d8d5594c.jpg', '2020-11-06 15:53:06'),
	(80, 19, 4, 17, 'https://images-na.ssl-images-amazon.com/images/I/61BLX3xa1hL._AC_SY741_.jpg', '2020-11-06 15:53:11'),
	(81, 20, 4, 14, 'https://resources.claroshop.com/imagenes-sanborns-ii/1200/7503008344730.jpg', '2020-11-06 15:53:17'),
	(82, 20, 4, 15, 'https://media.misohinutricion.com/media/catalog/product/cache/4/small_image/265x325/9df78eab33525d08d6e5fb8d27136e95/b/-/b-complex-50-solaray_1.jpg', '2020-11-06 15:53:22'),
	(83, 20, 4, 16, 'https://cdn1.evitamins.com/images/products/Sundown_Naturals/318965/500/318965_a.jpg', '2020-11-06 15:53:28'),
	(84, 20, 4, 17, 'https://images-na.ssl-images-amazon.com/images/I/71-rPFdOqcL._AC_SY550_.jpg', '2020-11-06 15:53:40');
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
