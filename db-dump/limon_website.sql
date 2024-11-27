-- Configuración inicial de la base de datos
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Creación de la base de datos con conjunto de caracteres y collation consistentes
CREATE DATABASE IF NOT EXISTS `limon_website` 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_0900_ai_ci;
USE `limon_website`;

-- Tabla Roles
CREATE TABLE `Roles` (
    `id_rol` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_rol` VARCHAR(50) NOT NULL,
    `descripcion` TEXT
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Tabla Usuarios

CREATE TABLE `usuario` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `alias` VARCHAR(100) NOT NULL,
    `nombre` VARCHAR(30) NOT NULL,
    `apellido1` VARCHAR(15) NOT NULL,
    `apellido2` VARCHAR(15) NOT NULL,
    `telefono` VARCHAR(9) NOT NULL,
    `celular` VARCHAR(9) DEFAULT NULL,
    `correo` VARCHAR(100) NOT NULL,
    `rol` INT NOT NULL,
    `passw` VARCHAR(255) NOT NULL,
    `ultimoAcceso` DATETIME DEFAULT NULL,
    `tkR` VARCHAR(255) NULL,
    `fechaIngreso` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_alias` (`alias`),
    UNIQUE KEY `unique_correo` (`correo`),
    FOREIGN KEY (`rol`) REFERENCES `Roles`(`id_rol`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Tabla Categorías
CREATE TABLE `Categorias` (
    `id_categoria` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre_categoria` VARCHAR(50) NOT NULL,
    `descripcion` TEXT
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Tabla Comentarios
CREATE TABLE `Comentarios` (
    `id_comentario` INT PRIMARY KEY AUTO_INCREMENT,
    `contenido` TEXT NOT NULL,
    `usuario_id` INT,
    `id_publicacion` INT,
    `tipo_publicacion` ENUM('comida', 'planta', 'restaurante'), -- Especifica el tipo de publicación
    `fecha_comentario` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Relación entre Categorías y Comidas
CREATE TABLE `Comida_Categoria` (
    `id_comida` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_comida`, `id_categoria`),
    FOREIGN KEY (`id_comida`) REFERENCES `Comidas`(`id_comida`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Relación entre Categorías y Plantas
CREATE TABLE `Planta_Categoria` (
    `id_planta` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_planta`, `id_categoria`),
    FOREIGN KEY (`id_planta`) REFERENCES `Plantas`(`id_planta`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

-- Relación entre Categorías y Restaurantes
CREATE TABLE `Restaurante_Categoria` (
    `id_restaurante` INT,
    `id_categoria` INT,
    PRIMARY KEY (`id_restaurante`, `id_categoria`),
    FOREIGN KEY (`id_restaurante`) REFERENCES `Restaurantes`(`id_restaurante`),
    FOREIGN KEY (`id_categoria`) REFERENCES `Categorias`(`id_categoria`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci;

COMMIT;

-- Procedimientos almacenados para Comidas

-- Obtener comidas
DELIMITER $$

CREATE PROCEDURE obtener_comidas(IN pid_comida INT)
BEGIN
    IF pid_comida IS NULL THEN
        SELECT * FROM Comidas;
    ELSE
        IF EXISTS (SELECT 1 FROM Comidas WHERE id_comida = pid_comida) THEN
            SELECT * FROM Comidas WHERE id_comida = pid_comida;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en comidas';
        END IF;
    END IF;
END$$

DELIMITER ;

-- Eliminar comida
DELIMITER $$

CREATE PROCEDURE eliminar_comida(IN pid_comida INT)
BEGIN
    IF (SELECT COUNT(*) FROM Comidas WHERE id_comida = pid_comida) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con el id_comida especificado no existe.';
    ELSE
        DELETE FROM Comidas WHERE id_comida = pid_comida;
    END IF;
END$$

DELIMITER ;

-- Crear comida
DELIMITER $$

CREATE PROCEDURE crear_comida(
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_usuario_id INT,
    IN p_fecha_creacion DATETIME,
    IN p_fecha_actualizacion DATETIME,
    IN p_elaboracion TEXT
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;

    INSERT INTO Comidas (
        nombre_comida,
        descripcion_comida,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion,
        elaboracion
    ) VALUES (
        p_nombre_comida,
        p_descripcion_comida,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion,
        p_elaboracion
    );
END$$

DELIMITER ;

-- Actualizar comida
DELIMITER $$ 

CREATE PROCEDURE actualizar_comida(
    IN p_id_comida INT,
    IN p_nombre_comida VARCHAR(100),
    IN p_descripcion_comida TEXT,
    IN p_imagen VARCHAR(255),
    IN p_elaboracion TEXT
)
BEGIN
    UPDATE Comidas
    SET 
        nombre_comida = COALESCE(p_nombre_comida, nombre_comida),
        descripcion_comida = COALESCE(p_descripcion_comida, descripcion_comida),
        imagen = COALESCE(p_imagen, imagen),
        fecha_actualizacion = NOW(),
        elaboracion = COALESCE(p_elaboracion, elaboracion)
    WHERE id_comida = p_id_comida;
END$$

DELIMITER ;

-- Filtrar comidas
DELIMITER $$

CREATE PROCEDURE filtrar_comidas(IN pnombre_comidas VARCHAR(100))
BEGIN
    SELECT * 
    FROM Comidas
    WHERE nombre_comida COLLATE utf8mb4_0900_ai_ci LIKE CONCAT('%', pnombre_comidas, '%');
END$$

DELIMITER ;

-- Procedimientos almacenados para Restaurantes

-- Obtener restaurantes
DELIMITER $$

CREATE PROCEDURE obtener_restaurantes(IN pid_restaurantes INT)
BEGIN
    IF pid_restaurantes IS NULL THEN
        SELECT * FROM Restaurantes;
    ELSE
        IF EXISTS (SELECT 1 FROM Restaurantes WHERE id_restaurante = pid_restaurantes) THEN
            SELECT * FROM Restaurantes WHERE id_restaurante = pid_restaurantes;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en restaurantes';
        END IF;
    END IF;
END$$

DELIMITER ;

-- Eliminar restaurante
DELIMITER $$

CREATE PROCEDURE eliminar_Restaurante (IN pid_restaurante INT)
BEGIN
    IF (SELECT COUNT(*) FROM Restaurantes WHERE id_restaurante = pid_restaurante) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con el id_restaurante especificado no existe.';
    ELSE
        DELETE FROM Restaurantes WHERE id_restaurante = pid_restaurante;
    END IF;
END$$

DELIMITER ;

-- Filtrar restaurantes
DELIMITER $$

CREATE PROCEDURE filtrar_restaurantes(IN pnombre_restaurantes VARCHAR(100))
BEGIN
    SELECT * 
    FROM Restaurantes
    WHERE nombre_restaurante COLLATE utf8mb4_0900_ai_ci  LIKE CONCAT('%', pnombre_restaurantes, '%');
END$$

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
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;

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
END$$

DELIMITER ;

-- Actualizar restaurante
DELIMITER $$

CREATE PROCEDURE actualizar_restaurante (
    IN p_id_restaurante INT,
    IN p_nombre_restaurante VARCHAR(100),
    IN p_descripcion_restaurante TEXT,
    IN p_direccion VARCHAR(255),
    IN p_imagen VARCHAR(255)
)
BEGIN
    UPDATE Restaurantes
    SET 
        nombre_restaurante = COALESCE(p_nombre_restaurante, nombre_restaurante),
        descripcion_restaurante = COALESCE(p_descripcion_restaurante, descripcion_restaurante),
        direccion = COALESCE(p_direccion, direccion),
        imagen = COALESCE(p_imagen, imagen),
        fecha_actualizacion = NOW()
    WHERE id_restaurante = p_id_restaurante;
END$$

DELIMITER ;

-- Procedimientos almacenados para Plantas

-- Obtener plantas
DELIMITER $$

CREATE PROCEDURE obtener_plantas(IN pid_planta INT)
BEGIN
    IF pid_planta IS NULL THEN
        SELECT * FROM Plantas;
    ELSE
        IF EXISTS (SELECT 1 FROM Plantas WHERE id_planta = pid_planta) THEN
            SELECT * FROM Plantas WHERE id_planta = pid_planta;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe este id en plantas';
        END IF;
    END IF;
END$$

DELIMITER ;

-- Eliminar planta
DELIMITER $$

CREATE PROCEDURE eliminar_planta (IN pid_planta INT)
BEGIN
    IF (SELECT COUNT(*) FROM Plantas WHERE id_planta = pid_planta) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro con el id_planta especificado no existe.';
    ELSE
        DELETE FROM Plantas WHERE id_planta = pid_planta;
    END IF;
END$$

DELIMITER ;

-- Filtrar plantas
DELIMITER $$

CREATE PROCEDURE filtrar_plantas(IN pnombre_plantas VARCHAR(100))
BEGIN
    SELECT * 
    FROM Plantas
    WHERE nombre_planta COLLATE utf8mb4_0900_ai_ci  LIKE CONCAT('%', pnombre_plantas, '%');
END$$

DELIMITER ;

-- Crear planta
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
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE id = p_usuario_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no existe';
    END IF;

    INSERT INTO Plantas (
        nombre_planta,
        caracteristicas,
        imagen,
        usuario_id,
        fecha_creacion,
        fecha_actualizacion,
        elaboracion
    ) VALUES (
        p_nombre_planta,
        p_caracteristicas,
        p_imagen,
        p_usuario_id,
        p_fecha_creacion,
        p_fecha_actualizacion,
        p_elaboracion 
    );
END$$

DELIMITER ;

-- Actualizar planta
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
END$$

DELIMITER ;

-- Procedimiento para obtener usuario
DELIMITER $$ 

CREATE PROCEDURE getUser(IN userParam VARCHAR(255))
BEGIN
    SELECT id, alias, nombre, apellido1, apellido2, celular, correo, rol
    FROM usuario
    WHERE alias = userParam OR correo = userParam
    LIMIT 1;
END$$ 

DELIMITER ;

-- Función para modificar el token
DELIMITER $$

CREATE FUNCTION modificarToken (aliasORcorreo VARCHAR(100), _tkR VARCHAR(255)) RETURNS INT 
READS SQL DATA DETERMINISTIC
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(alias) INTO _cant FROM usuario WHERE alias = aliasORcorreo OR correo = aliasORcorreo;
    IF _cant > 0 THEN
        UPDATE usuario SET
            tkR = _tkR
        WHERE alias = aliasORcorreo OR correo = aliasORcorreo;
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END$$

DELIMITER ;

-- Procedimiento para verificar el token
DELIMITER $$

CREATE PROCEDURE verificarTokenR (_alias VARCHAR(15), _tkR VARCHAR(255)) 
BEGIN
    SELECT rol 
    FROM usuario 
    WHERE alias = _alias AND tkR = _tkR;
END$$

DELIMITER ;

-- Procedimiento para iniciar sesión
DELIMITER $$

CREATE PROCEDURE IniciarSesion(_id INT, _passw VARCHAR(255)) 
BEGIN
    SELECT alias, rol FROM usuario WHERE id = _id AND passw = _passw;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE cambiarRolUsuario (
    IN p_aliasORcorreo VARCHAR(100),
    IN p_nuevoRol INT
)
BEGIN
    DECLARE v_usuarioId INT;
    
    -- Verificar que el rol exista
    IF (SELECT COUNT(*) FROM Roles WHERE id_rol = p_nuevoRol) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Rol inválido';
    END IF;
    
    -- Obtener el ID del usuario
    SELECT id INTO v_usuarioId FROM usuario WHERE alias = p_aliasORcorreo OR correo = p_aliasORcorreo LIMIT 1;
    
    IF v_usuarioId IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    ELSE
        -- Actualizar el rol del usuario
        UPDATE usuario SET rol = p_nuevoRol WHERE id = v_usuarioId;
    END IF;
END$$


DELIMITER ;

INSERT INTO Roles (id_rol, nombre_rol, descripcion) VALUES
(1, 'Administrador', 'Rol de administrador'),
(2, 'Supervisor', 'Rol de supervisor'),
(3, 'Cliente', 'Rol de cliente');

DELIMITER $$