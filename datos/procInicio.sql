USE taller;  -- Selecciona la base de datos 'taller' para las operaciones siguientes

DELIMITER $$  -- Cambia el delimitador de MySQL a $$ para permitir el uso de ; en el procedimiento

DROP PROCEDURE IF EXISTS IniciarSesion$$  -- Elimina el procedimiento 'IniciarSesion' si ya existe

CREATE PROCEDURE IniciarSesion (_id int, _passw varchar(255))  -- Crea el procedimiento 'IniciarSesion' con los parámetros _id y _passw
BEGIN
    SELECT idUsuario, rol FROM usuario WHERE id = _id AND passw = _passw;  -- Selecciona los campos 'idUsuario' y 'rol' de la tabla 'usuario' donde 'id' y 'passw' coincidan con los parámetros proporcionados
END$$  -- Fin del procedimiento

DELIMITER ;  -- Cambia el delimitador de vuelta a ;
