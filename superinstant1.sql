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

-- Dumping structure for procedure superinstant1.add_devuelve
DELIMITER //
CREATE PROCEDURE `add_devuelve`(
	IN `suministro_id` INT,
	IN `sucursal_ruc` INT,
	IN `cantidad2` INT,
	IN `motivo2` VARCHAR(500)
)
BEGIN
	DECLARE precio2 DECIMAL(10,2);
	DECLARE centro_ruc INT;
	IF EXISTS (SELECT * FROM reabastece WHERE id_suministro = suministro_id) THEN
	SELECT precio FROM reabastece WHERE id_suministro = suministro_id 
	ORDER BY fecha DESC 
	LIMIT 1 INTO precio2;
	
	SELECT ruc_centro FROM sucursales WHERE ruc_sucursal = sucursal_ruc INTO centro_ruc;
	
	UPDATE contiene
	SET cantidad = cantidad - cantidad2
	WHERE id_suministro = suministro_id AND ruc_sucursal = sucursal_ruc;
	
	INSERT INTO devuelve(ruc_sucursal, ruc_centro, id_suministro, cantidad, precio, motivo)
	VALUES (sucursal_ruc, centro_ruc, suministro_id, cantidad2, precio2, motivo2);
	SELECT suministro_id AS producto, sucursal_ruc AS sucursal, centro_ruc AS centro, motivo2 AS motivo; 
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

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.centros
CREATE TABLE IF NOT EXISTS `centros` (
  `ruc_centro` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `direccion` varchar(25) NOT NULL,
  `telefono` int(11) NOT NULL,
  `correo` varchar(25) NOT NULL,
  PRIMARY KEY (`ruc_centro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `cédula` varchar(20) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `n_tarjeta` varchar(50) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cédula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Dumping structure for table superinstant1.devuelve
CREATE TABLE IF NOT EXISTS `devuelve` (
  `ruc_sucursal` int(11) NOT NULL,
  `ruc_centro` int(11) NOT NULL,
  `id_suministro` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `motivo` varchar(1000) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ruc_sucursal`,`ruc_centro`,`id_suministro`,`fecha`),
  KEY `FK_devuelve_centros` (`ruc_centro`),
  KEY `FK_devuelve_suministros` (`id_suministro`),
  CONSTRAINT `FK_devuelve_centros` FOREIGN KEY (`ruc_centro`) REFERENCES `centros` (`ruc_centro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_devuelve_sucursales` FOREIGN KEY (`ruc_sucursal`) REFERENCES `sucursales` (`ruc_sucursal`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_devuelve_suministros` FOREIGN KEY (`id_suministro`) REFERENCES `suministros` (`id_suministro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for procedure superinstant1.get_allSucursales
DELIMITER //
CREATE PROCEDURE `get_allSucursales`()
BEGIN
	SELECT 
	s.ruc_sucursal,
	s.ruc_centro,
	s.id_provincia,
	p.nombre AS provincia,
	s.nombre,
	s.correo,
	s.telefono,
	s.direccion
	FROM sucursales s JOIN provincias p ON s.id_provincia = p.id_provincia;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_devuelveByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_devuelveByCentroRUC`(
	IN `centro_ruc` INT
)
BEGIN
	SELECT 
	d.id_suministro,
	d.ruc_sucursal,
	su.id_provincia,
	p.descripcion AS producto,
	t.id_tamano AS id_tamano,
	t.descripcion AS tamano,
	d.precio AS precio,
	d.cantidad AS cantidad,
	d.motivo AS motivo,
	d.fecha AS fecha
	FROM devuelve d JOIN suministros s ON d.id_suministro = s.id_suministro
							JOIN productos p ON s.id_producto = p.id_producto
							JOIN tamanos t ON s.id_tamano = t.id_tamano
							JOIN sucursales su ON d.Ruc_sucursal = su.ruc_sucursal
							JOIN provincias pr ON su.id_provincia = pr.id_provincia 
	WHERE d.ruc_centro = centro_ruc
	ORDER BY d.id_suministro;
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
pr.nombre AS 'provincia',
s.nombre AS 'super',
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
						JOIN provincias pr ON s.id_provincia = pr.id_provincia
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
s.id_provincia,
pr.nombre AS provincia,
p.descripcion AS producto,
su.id_tamano AS id_tamano,
t.descripcion AS tamano,
c.cantidad AS cantidad
FROM contiene c JOIN sucursales s ON c.ruc_sucursal = s.ruc_sucursal
					JOIN suministros su ON c.id_suministro = su.id_suministro
					JOIN productos p ON su.id_producto = p.id_producto
					JOIN tamanos t ON su.id_tamano = t.id_tamano
					JOIN provincias pr ON s.id_provincia = pr.id_provincia 
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
p.id_producto AS id_producto,
p.descripcion AS producto,
cat.id_categoria AS id_categoria,
cat.descripcion AS categoria,
t.id_tamano AS id_tamano,
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

-- Dumping structure for procedure superinstant1.get_productosMasCompradosByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_productosMasCompradosByCentroRUC`(
	IN `centro_ruc` INT
)
BEGIN
SELECT 
p.descripcion AS 'producto',
t.descripcion AS 'tamano',
SUM(r.cantidad) AS 'cantidad'
FROM reabastece r JOIN suministros su ON r.id_suministro = su.id_suministro
						JOIN productos p ON su.id_producto = p.id_producto
						JOIN tamanos t ON su.id_tamano = t.id_tamano
WHERE r.ruc_centro = centro_ruc
GROUP BY p.descripcion, t.descripcion
ORDER BY cantidad desc;
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
	su.id_provincia,
	p.descripcion AS producto,
	t.id_tamano AS id_tamano,
	t.descripcion AS tamano,
	r.precio AS precio,
	r.cantidad
	FROM reabastece r JOIN suministros s ON r.id_suministro = s.id_suministro
							JOIN productos p ON s.id_producto = p.id_producto
							JOIN tamanos t ON s.id_tamano = t.id_tamano
							JOIN sucursales su ON r.Ruc_sucursal = su.ruc_sucursal
							JOIN provincias pr ON su.id_provincia = pr.id_provincia 
	WHERE r.ruc_centro = centro_ruc
	ORDER BY r.id_suministro;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_sucursalesByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_sucursalesByCentroRUC`(
	IN `ruc_centro2` INT
)
BEGIN
	SELECT 
	s.ruc_sucursal,
	s.ruc_centro,
	s.id_provincia,
	p.nombre AS provincia,
	s.nombre,
	s.correo,
	s.telefono,
	s.direccion
	FROM sucursales s JOIN provincias p ON s.id_provincia = p.id_provincia
	WHERE ruc_centro = ruc_centro2;
END//
DELIMITER ;

-- Dumping structure for procedure superinstant1.get_sucursalMasComprasByCentroRUC
DELIMITER //
CREATE PROCEDURE `get_sucursalMasComprasByCentroRUC`(
	IN `centro_ruc` INT
)
BEGIN
SELECT 
pr.nombre AS 'provincia',
s.nombre AS 'super',
s.direccion AS 'sucursal',
SUM(r.cantidad * r.precio) AS 'gastos'
FROM reabastece r JOIN sucursales s ON r.Ruc_sucursal = s.ruc_sucursal
						JOIN provincias pr ON s.id_provincia = pr.id_provincia
WHERE r.Ruc_centro = centro_ruc
GROUP BY pr.nombre, s.nombre, s.direccion
ORDER BY  gastos desc;
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

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.provincias
CREATE TABLE IF NOT EXISTS `provincias` (
  `id_provincia` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.sucursales
CREATE TABLE IF NOT EXISTS `sucursales` (
  `ruc_sucursal` int(11) NOT NULL,
  `ruc_centro` int(11) NOT NULL,
  `id_provincia` int(11) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(25) NOT NULL,
  `telefono` int(11) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  PRIMARY KEY (`ruc_sucursal`),
  KEY `FK_sucursales_centros` (`ruc_centro`),
  KEY `FK_sucursales_provincias` (`id_provincia`),
  CONSTRAINT `FK_sucursales_centros` FOREIGN KEY (`ruc_centro`) REFERENCES `centros` (`ruc_centro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sucursales_provincias` FOREIGN KEY (`id_provincia`) REFERENCES `provincias` (`id_provincia`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table superinstant1.tamanos
CREATE TABLE IF NOT EXISTS `tamanos` (
  `id_tamano` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tamano`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
