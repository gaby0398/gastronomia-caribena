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
  `alias` VARCHAR(100) NOT NULL,
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
  `alias` VARCHAR(100) NOT NULL,
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
  `alias` VARCHAR(100) NOT NULL,
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
    `alias` VARCHAR(100) NOT NULL,
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
    `elaboracion` TEXT, -- NUEVO
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
    `elaboracion` TEXT, -- NUEVO
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

-- COMIDA PROCESOS ALMACENADOS

--Leer comida

DELIMITER $$

CREATE PROCEDURE obtener_comidas(IN pid_comida INT)
BEGIN
    -- select general
    IF pid_comida IS NULL THEN
        SELECT * FROM Comidas;
    ELSE
        -- verica existencia de id_comida antes de select especifico
        IF EXISTS (SELECT 1 FROM Comidas WHERE id_comida = pid_comida) THEN
            SELECT * FROM Comidas WHERE id_comida = pid_comida;
        ELSE
            -- no existe ese id
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en comidas';
        END IF;
    END IF;
END $$

DELIMITER ;

--eliminar comida

DELIMITER $$

CREATE PROCEDURE eliminar_comida(IN pid_comida INT)
BEGIN
    -- Verifica si el id_comida existe en la tabla
    IF (SELECT COUNT(*) FROM Comidas WHERE id_comida = pid_comida) = 0 THEN
        -- Lanza una excepción si el id_comida no existe
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con el id_comida especificado no existe.';
    ELSE
        -- Si  existe id, elimina el registro
        DELETE FROM Comidas WHERE id_comida = pid_comida;
    END IF;
END $$

DELIMITER ;


-- CREAR COMIDA

DELIMITER $$

CREATE PROCEDURE crear_comida(
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME,
    IN p_elaboracion TEXT  -- Nuevo parámetro
)
BEGIN
    -- Verificar que el usuario existe
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;

    -- Insertar la comida
    INSERT INTO Comidas (
        nombre_comida,
        descripcion_comida,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion,
        elaboracion  -- Insertar el nuevo campo
    ) VALUES (
        p_nombre_comida,
        p_descripcion_comida,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion,
        p_elaboracion  -- Incluir el parámetro en la inserción
    );
END $$

DELIMITER ;

-- actualizar comida

DELIMITER $$ 

CREATE PROCEDURE actualizar_comida(
    IN p_id_comida INT,
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_elaboracion TEXT -- Parámetro para la nueva columna "elaboracion"
)
BEGIN
    UPDATE Comidas
    SET 
        nombre_comida = COALESCE(p_nombre_comida, nombre_comida),
        descripcion_comida = COALESCE(p_descripcion_comida, descripcion_comida),
        imagen = COALESCE(p_imagen, imagen),
        fecha_actualizacion = now(),
        elaboracion = COALESCE(p_elaboracion, elaboracion) -- Actualización de "elaboracion"
    WHERE id_comida = p_id_comida;
END $$

DELIMITER ;

-- filtro comida

DELIMITER $$

CREATE PROCEDURE filtrar_comidas(IN pnombre_comidas VARCHAR(100))
BEGIN
    SELECT * 
    FROM Comidas
    WHERE nombre_comida LIKE CONCAT('%', pnombre_comidas, '%');
END $$

DELIMITER ;


-- Cambiar el delimitador para que MySQL no interprete el ; dentro del procedimiento como el fin de la declaración
DELIMITER $$ 

-- Creación del procedimiento almacenado getUser
CREATE PROCEDURE getUser(IN userParam VARCHAR(255)) -- Procedimiento que recibe un parámetro de entrada llamado userParam
BEGIN
    -- Selecciona las columnas idUsuario, alias, correo y rol de la tabla Usuario
    SELECT idUsuario, alias, correo, rol
    FROM Usuario
    -- Filtra la búsqueda por el valor proporcionado en userParam (idUsuario, alias o correo)
    WHERE idUsuario = userParam OR alias = userParam OR correo = userParam
    LIMIT 1; -- Limita el resultado a una única fila (en caso de múltiples coincidencias)
END$$ 

-- Restaurar el delimitador predeterminado
DELIMITER ;

--**REstaurantes PS

--Leer RESTAURANTES

DELIMITER $$

CREATE PROCEDURE obtener_restaurantes(IN pid_restaurantes INT)
BEGIN
    -- select general
    IF pid_restaurantes IS NULL THEN
        SELECT * FROM Restaurantes;
    ELSE
        -- verica existencia de id_restaurantes antes de select especifico
        IF EXISTS (SELECT 1 FROM Restaurantes WHERE id_restaurante = pid_restaurantes) THEN
            SELECT * FROM Restaurantes WHERE id_restaurante = pid_restaurantes;
        ELSE
            -- no existe ese id
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en restaurantes';
        END IF;
    END IF;
END $$

DELIMITER ;

--eliminar restaurante

DELIMITER $$

CREATE PROCEDURE eliminar_Restaurante (IN pid_restaurante INT)
BEGIN
    -- Verifica si el id_restaurantes existe en la tabla
    IF (SELECT COUNT(*) FROM Restaurantes WHERE id_restaurante = pid_restaurante) = 0 THEN
        -- Lanza una excepción si el id_restaurante no existe
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con especificado no existe.';
    ELSE
        -- Si existe id, elimina el registro
        DELETE FROM Restaurantes WHERE id_restaurante = pid_restaurante;
    END IF;
END $$

DELIMITER ;

--filtro restaurante

DELIMITER $$

CREATE PROCEDURE filtrar_restaurantes(IN pnombre_restaurantes VARCHAR(100))
BEGIN
    SELECT * 
    FROM Restaurantes
    WHERE nombre_restaurante COLLATE utf8mb4_0900_ai_ci LIKE CONCAT('%', pnombre_restaurantes, '%');
END $$

DELIMITER ;

-- Crear restaurante

DELIMITER $$

CREATE PROCEDURE crear_restaurante(
    IN p_nombre_restaurante VARCHAR(100),
    IN p_descripcion_restaurante TEXT,
    IN p_direccion VARCHAR(255),
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME
)
BEGIN
    -- Verificar que el usuario existe
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;

    -- Insertar el restaurante
    INSERT INTO Restaurantes (
        nombre_restaurante,
        descripcion_restaurante,
        direccion,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion
    ) VALUES (
        p_nombre_restaurante,
        p_descripcion_restaurante,
        p_direccion,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion
    );
END $$

DELIMITER ;

-- actualizar restaurante


DELIMITER $$

CREATE PROCEDURE actualizar_comida(
    IN p_id_comida INT,
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255)
)
BEGIN
    UPDATE Comidas
    SET 
        nombre_comida = COALESCE(p_nombre_comida, nombre_comida),
        descripcion_comida = COALESCE(p_descripcion_comida, descripcion_comida),
        imagen = COALESCE(p_imagen, imagen),
        fecha_actualizacion = NOW()
    WHERE id_comida = p_id_comida;
END $$

DELIMITER ;

--Plantas PS
--LEER PLANTAS

DELIMITER $$

CREATE PROCEDURE obtener_plantas(IN pid_planta INT)
BEGIN
    -- select general
    IF pid_planta IS NULL THEN
        SELECT * FROM Plantas;
    ELSE
        -- verica existencia de id_planta antes de select especifico
        IF EXISTS (SELECT 1 FROM Plantas WHERE id_planta = pid_planta) THEN
            SELECT * FROM Plantas WHERE id_planta = pid_planta;
        ELSE
            -- no existe ese id
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en plantas';
        END IF;
    END IF;
END $$

DELIMITER ;

--ELIMINAR PLANTA

DELIMITER $$

CREATE PROCEDURE eliminar_planta (IN pid_planta INT)
BEGIN
    -- Verifica si el id_planta existe en la tabla
    IF (SELECT COUNT(*) FROM Plantas WHERE id_planta = pid_planta) = 0 THEN
        -- Lanza una excepción si el id_planta no existe
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con especificado no existe.';
    ELSE
        -- Si  existe id, elimina el registro
        DELETE FROM Plantas WHERE id_planta = pid_planta;
    END IF;
END $$

DELIMITER ;

--filtro plantas

DELIMITER $$

CREATE PROCEDURE filtrar_plantas(IN pnombre_plantas VARCHAR(100))
BEGIN
    SELECT * 
    FROM Plantas
    WHERE nombre_planta COLLATE utf8mb4_0900_ai_ci LIKE CONCAT('%', pnombre_plantas, '%');
END $$

DELIMITER ;

-- crear planta


DELIMITER $$

CREATE PROCEDURE crear_planta(
    IN p_nombre_planta VARCHAR(100),
    IN p_caracteristicas TEXT,
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME,
    IN p_elaboracion TEXT 
)
BEGIN
    -- Verificar que el usuario existe
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'el usuario no existe';
    END IF;

    -- Insertar la planta
    INSERT INTO Plantas (
        nombre_planta,
        caracteristicas,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion,
        elaboracion -- Nuevo campo "elaboracion" después de "fecha_actualizacion"
    ) VALUES (
        p_nombre_planta,
        p_caracteristicas,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion,
        p_elaboracion 
    );
END $$

DELIMITER ;

-- actualizar planta


DELIMITER $$

CREATE PROCEDURE actualizar_planta(
    IN p_id_planta INT,
    IN p_nombre_planta VARCHAR(100),
    IN p_caracteristicas TEXT,
    IN p_imagen VARCHAR(255),
    IN p_elaboracion TEXT
)
BEGIN
    UPDATE Plantas
    SET 
        nombre_planta = COALESCE(p_nombre_planta, nombre_planta),
        caracteristicas = COALESCE(p_caracteristicas, caracteristicas),
        imagen = COALESCE(p_imagen, imagen),
        elaboracion = COALESCE(p_elaboracion, elaboracion),
        fecha_actualizacion = NOW()
    WHERE id_planta = p_id_planta;
END $$

DELIMITER ;
