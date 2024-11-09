-- Configuración inicial de la base de datos
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS `limon_website` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `limon_website`;

-- Tabla Roles
CREATE TABLE `Roles` (
    `id_rol` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_rol` VARCHAR(50) NOT NULL,
    `descripcion` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
CREATE TABLE `cliente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido1` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido2` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `celular` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fechaIngreso` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;


CREATE TABLE `supervisor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido1` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido2` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `celular` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fechaIngreso` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

CREATE TABLE `administrador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido1` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `apellido2` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `telefono` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `celular` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fechaIngreso` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;


-- Tabla Usuarios
CREATE TABLE usuario (
    `id` INT NOT NULL AUTO_INCREMENT,
    `idUsuario` VARCHAR(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
    `nombreusuario` VARCHAR(100) NOT NULL,
    `correo` VARCHAR(100) NOT NULL,
    `rol` INT NOT NULL,
    `passw` VARCHAR(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
    `ultimoAcceso` DATETIME DEFAULT NULL,
    `tkR` varchar(255) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY idx_Usuario (idUsuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- Tabla Categorías
CREATE TABLE `Categorias` (
    `id_categoria` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_categoria` VARCHAR(50) NOT NULL,
    `descripcion` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla Comidas
CREATE TABLE `Comidas` (
    `id_comida` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_comida` VARCHAR(100) NOT NULL,
    `descripcion_comida` TEXT,
    `imagen` VARCHAR(255),
    `usuario_id` INT,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;


-- Tabla Plantas
CREATE TABLE `Plantas` (
    `id_planta` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_planta` VARCHAR(100) NOT NULL,
    `caracteristicas` TEXT,
    `imagen` VARCHAR(255),
    `usuario_id` INT,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla Restaurantes
CREATE TABLE `Restaurantes` (
    `id_restaurante` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_restaurante` VARCHAR(100) NOT NULL,
    `descripcion_restaurante` TEXT,
    `direccion` VARCHAR(255),
    `imagen` VARCHAR(255),
    `usuario_id` INT,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla Comentarios
CREATE TABLE `Comentarios` (
    `id_comentario` INT PRIMARY KEY AUTO_INCREMENT,
    `contenido` TEXT NOT NULL,
    `usuario_id` INT,
    `id_publicacion` INT,
    `tipo_publicacion` ENUM('comida', 'planta', 'restaurante'), -- Especifica el tipo de publicación
    `fecha_comentario` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`)REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Relación entre Categorías y Comidas
CREATE TABLE `Comida_Categoria` (
    `id_comida` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_comida`, `id_categoria`),
    FOREIGN KEY (`id_comida`) REFERENCES `Comidas`(`id_comida`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Relación entre Categorías y Plantas
CREATE TABLE `Planta_Categoria` (
    `id_planta` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_planta`, `id_categoria`),
    FOREIGN KEY (`id_planta`) REFERENCES `Plantas`(`id_planta`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Relación entre Categorías y Restaurantes
CREATE TABLE `Restaurante_Categoria` (
    `id_restaurante` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_restaurante`, `id_categoria`),
    FOREIGN KEY (`id_restaurante`) REFERENCES `Restaurantes`(`id_restaurante`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

COMMIT;
COMMIT;

DELIMITER $$

CREATE FUNCTION modificarToken (_idUsuario VARCHAR(100), _tkR varchar(255)) RETURNS INT(1) 
READS SQL DATA DETERMINISTIC
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(idUsuario) INTO _cant FROM usuario WHERE idUsuario = _idUsuario OR correo = _idUsuario;
    IF _cant > 0 THEN
        UPDATE usuario SET
            tkR = _tkR
        WHERE idUsuario = _idUsuario OR correo = _idUsuario;
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE verificarTokenR (_idUsuario VARCHAR(15), _tkR VARCHAR(255)) 
BEGIN
    SELECT rol 
    FROM usuario 
    WHERE idUsuario = _idUsuario AND tkR = _tkR;
END $$

DELIMITER ;


DELIMITER ;

DELIMITER $$
CREATE PROCEDURE IniciarSesion(_id int, _passw varchar(255)) -- > SP iniciar sesion
BEGIN
    select idUsuario, rol from usuario where id = _id and passw = _passw;
END$$
DELIMITER;

DELIMITER $$
CREATE PROCEDURE eliminarCliente (_id INT) -- > SP eliminar cliente
begin
    declare _cant int;
    declare _resp int;
    set _resp = 0;
    select count(id) into _cant from cliente where id = _id;
    if _cant > 0 then
        set _resp = 1;
        select count(id) into _cant from artefacto where idCliente = _id;
        if _cant = 0 then
            delete from cliente where id = _id;
        else 
            -- select 2 into _resp;
            set _resp = 2;
        end if;
    end if;
    select _resp as resp;
end$$
DELIMITER ;


DELIMITER $$
CREATE TRIGGER eliminar_cliente AFTER DELETE ON cliente FOR EACH ROW -- > Trigger eliminar usuario
BEGIN

 DELETE FROM usuario WHERE usuario.idUsuario = OLD.id;

END$$
DELIMITER ;

--Leer

DELIMITER $$ CREATE PROCEDURE obtener_comidas(IN pid_comida INT) BEGIN IF pid_comida IS NULL THEN SELECT * FROM Comidas; ELSE SELECT * FROM Comidas WHERE id_comida = pid_comida; END IF; END$$
DELIMITER ;


--eliminar

DELIMITER $$

CREATE PROCEDURE eliminar_comida(IN pid_comida INT)
BEGIN
    -- Verifica si el id_comida existe en la tabla
    IF (SELECT COUNT(*) FROM Comidas WHERE id_comida = pid_comida) = 0 THEN
        -- Lanza una excepción si el id_comida no existe
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con el id_comida especificado no existe.';
    ELSE
        -- Si el id_comida existe, elimina el registro
        DELETE FROM Comidas WHERE id_comida = pid_comida;
    END IF;
END $$

DELIMITER ;

--CREAR

DELIMITER $$

CREATE PROCEDURE insertar_comida(
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME
)
BEGIN
    INSERT INTO Comidas (
        nombre_comida,
        descripcion_comida,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion
    ) VALUES (
        p_nombre_comida,
        p_descripcion_comida,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion
    );
END $$

DELIMITER ;

--Actualizer

DELIMITER $$

CREATE PROCEDURE actualizar_comida(
    IN p_id_comida INT,
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME
)
BEGIN
    UPDATE Comidas
    SET 
        nombre_comida = COALESCE(p_nombre_comida, nombre_comida),
        descripcion_comida = COALESCE(p_descripcion_comida, descripcion_comida),
        imagen = COALESCE(p_imagen, imagen),
        usuario_id = COALESCE(p_usuario_id, usuario_id),
        fecha_creacion = COALESCE(p_fecha_creacion, fecha_creacion),
        fecha_actualizacion = COALESCE(p_fecha_actualizacion, fecha_actualizacion)
    WHERE id_comida = p_id_comida;
END $$

DELIMITER ;


