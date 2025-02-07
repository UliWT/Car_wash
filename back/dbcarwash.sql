DROP DATABASE IF EXISTS dbcarwash;

CREATE DATABASE dbcarwash;

USE dbcarwash;

CREATE TABLE personas (
  id_usuario BIGINT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  correo VARCHAR(100) UNIQUE,
  direccion TEXT,
  contrasena VARCHAR(255) NOT NULL,
  rol VARCHAR(10) NOT NULL 
);

CREATE TABLE vehiculos (
  id_vehiculo BIGINT AUTO_INCREMENT PRIMARY KEY,
  modelo VARCHAR(50) NOT NULL,
  marca VARCHAR(50) NOT NULL,
  matricula VARCHAR(20) NOT NULL UNIQUE,
  tipo VARCHAR(50) NOT NULL,
  id_usuario BIGINT NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES personas(id_usuario) ON DELETE CASCADE
);

CREATE TABLE servicios (
  id_servicio BIGINT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL
);

CREATE TABLE turnos (
  id_turno BIGINT AUTO_INCREMENT PRIMARY KEY,
  id_usuario BIGINT NOT NULL,
  id_vehiculo BIGINT NOT NULL,
  id_servicio BIGINT NOT NULL,
  fecha DATE NOT NULL,
  estado ENUM('Nuevo', 'En Proceso', 'Enviado', 'Entregado') NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES personas(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo) ON DELETE CASCADE,
  FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio) ON DELETE CASCADE
);

CREATE TABLE pagos (
  id_pago BIGINT AUTO_INCREMENT PRIMARY KEY,
  id_usuario BIGINT NOT NULL,
  id_turno BIGINT NOT NULL,
  monto_total DECIMAL(10,2) NOT NULL,
  fecha DATETIME NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES personas(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_turno) REFERENCES turnos(id_turno) ON DELETE CASCADE
);

CREATE TABLE auditoria_turnos (
    id_auditoria BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_turno BIGINT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(100) NOT NULL,
    descripcion TEXT
);

INSERT INTO servicios VALUES (1, 'Limpieza Interior', 'Aspirado y limpieza profunda.', 50000);
INSERT INTO servicios VALUES (2, 'Lavado Exterior', 'Incluye lavado y encerado.', 60000);
INSERT INTO servicios VALUES (3, 'Lavado Completo y Detailing', 'Incluye limpieza interior y exterior.', 100000);
UPDATE personas
SET contrasena = MD5('contra123')
WHERE id_usuario = 1;

DELIMITER $$

CREATE TRIGGER before_turno_delete
BEFORE DELETE ON turnos
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_turnos (id_turno, accion, usuario, descripcion)
    VALUES (OLD.id_turno, 'DELETE', 'admin', 
            CONCAT('Se eliminó el turno del usuario ', OLD.id_usuario, 
                   ' con vehículo ', OLD.id_vehiculo, ' y servicio ', OLD.id_servicio));
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER after_turno_update
AFTER UPDATE ON turnos
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_turnos (id_turno, accion, usuario, descripcion)
    VALUES (OLD.id_turno, 'UPDATE', 'admin', 
            CONCAT('Turno actualizado: Estado de ', OLD.estado, ' a ', NEW.estado, 
                   ', fecha de ', OLD.fecha, ' a ', NEW.fecha));
END$$

DELIMITER ;

CREATE OR REPLACE VIEW vista_turnos AS
SELECT 
    t.id_turno,
    p.nombre AS nombre_usuario,
    p.apellido AS apellido_usuario,
    v.matricula AS vehiculo_matricula,
    s.nombre AS servicio,
    t.fecha,
    t.estado
FROM turnos t
JOIN personas p ON t.id_usuario = p.id_usuario
JOIN vehiculos v ON t.id_vehiculo = v.id_vehiculo
JOIN servicios s ON t.id_servicio = s.id_servicio;

