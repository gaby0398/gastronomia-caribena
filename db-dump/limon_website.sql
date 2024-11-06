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

-- Tabla Usuarios
CREATE TABLE `Usuarios` (
    `id_usuario` INT PRIMARY KEY AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `correo` VARCHAR(100) NOT NULL UNIQUE,
    `contraseña` VARCHAR(255) NOT NULL, -- Recuerda encriptar las contraseñas a nivel de aplicación
    `rol_id` INT,
    FOREIGN KEY (`rol_id`) REFERENCES `Roles`(`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

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
    FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`id_usuario`)
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
    FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`id_usuario`)
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
    FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla Comentarios
CREATE TABLE `Comentarios` (
    `id_comentario` INT PRIMARY KEY AUTO_INCREMENT,
    `contenido` TEXT NOT NULL,
    `usuario_id` INT,
    `id_publicacion` INT,
    `tipo_publicacion` ENUM('comida', 'planta', 'restaurante'), -- Especifica el tipo de publicación
    `fecha_comentario` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`id_usuario`)
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
