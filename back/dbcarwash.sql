DROP DATABASE IF EXISTS dbcarwash;

CREATE DATABASE dbcarwash;

USE dbcarwash;

CREATE TABLE personas (
  id_usuario BIGINT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  correo VARCHAR(100) UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol VARCHAR(10) NOT NULL 
);

CREATE TABLE marcas (
  id_marcas BIGINT AUTO_INCREMENT PRIMARY KEY,
  marca VARCHAR(25) NOT NULL,
  pais VARCHAR(25) NOT NULL
);

CREATE TABLE vehiculos (
  id_vehiculo BIGINT AUTO_INCREMENT PRIMARY KEY,
  modelo VARCHAR(50) NOT NULL,
  id_marca BIGINT NOT NULL,
  matricula VARCHAR(20) NOT NULL UNIQUE,
  tipo VARCHAR(50) NOT NULL,
  id_usuario BIGINT NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES personas(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_marca) REFERENCES marcas(id_marcas)  -- Relación con la tabla 'marcas'
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
  estado ENUM('En Espera', 'En Proceso', 'Listo', 'Cancelado') NOT NULL,
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

INSERT INTO marcas VALUES(1, "PEUGEOT", "FRANCIA");
INSERT INTO marcas VALUES(2, "BMW", "ALEMANIA");
INSERT INTO marcas VALUES(3, "VOLKSWAGEN", "ALEMANIA");
INSERT INTO marcas VALUES(4, "TOYOTA", "JAPON");
INSERT INTO marcas VALUES(5, "FORD", "USA");
INSERT INTO marcas VALUES(6, "FIAT", "ITALIA");
INSERT INTO marcas VALUES(7, "AUDI", "ALEMANIA");
INSERT INTO marcas VALUES(8, "RAM", "USA");
INSERT INTO marcas VALUES(9, "KAWASAKI", "JAPON");
INSERT INTO marcas VALUES(10, "HONDA", "JAPON");
INSERT INTO marcas VALUES(11, "HYUNDAI", "JAPON");
INSERT INTO marcas VALUES(12, "YAMAHA", "JAPON");
INSERT INTO marcas VALUES(13, "NISSAN", "JAPON");
INSERT INTO marcas VALUES(14, "CITROEN", "FRANCIA");
INSERT INTO marcas VALUES(15, "RENAULT", "FRANCIA");
INSERT INTO marcas VALUES(16, "MERCEDES-BENZ", "ALEMANIA");
INSERT INTO marcas VALUES(17, "HARLEY-DAVIDSON", "USA");
INSERT INTO marcas VALUES(18, "CHEVROLET", "USA");

INSERT INTO personas VALUES(1, "ADMIN", "ADMIN", 11111, "admin@admin.com", "contra123", "admin");
INSERT INTO personas VALUES(2, "Jairo", "Lopez", 2617145561, "jairolopezcabrera18@gmail.com", "123", "user");
INSERT INTO servicios VALUES (1, 'Limpieza Interior', 'Aspirado y limpieza profunda.', 50000);
INSERT INTO servicios VALUES (2, 'Lavado Exterior', 'Incluye lavado y encerado.', 60000);
INSERT INTO servicios VALUES (3, 'Lavado Completo y Detailing', 'Incluye limpieza interior y exterior.', 100000);
UPDATE personas
SET contrasena = MD5('contra123')
WHERE id_usuario = 1;

UPDATE personas
SET contrasena = MD5('123')
WHERE id_usuario = 2;

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
    v.modelo AS modelo_vehiculo,
    v.tipo AS tipo_vehiculo,
    m.marca AS marca_vehiculo,
    s.nombre AS servicio,
    t.fecha,
    t.estado,
    s.precio
FROM turnos t
JOIN personas p ON t.id_usuario = p.id_usuario
JOIN vehiculos v ON t.id_vehiculo = v.id_vehiculo
JOIN marcas m ON v.id_marca = m.id_marcas
JOIN servicios s ON t.id_servicio = s.id_servicio;

CREATE VIEW pago AS
SELECT 
    t.id_turno, 
    t.id_usuario, 
    t.fecha, 
    s.precio
FROM turnos t
JOIN servicios s ON t.id_servicio = s.id_servicio;