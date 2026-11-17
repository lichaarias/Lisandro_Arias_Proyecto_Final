-- Creamos la base de datos para la tienda de vinilos
CREATE DATABASE tienda_vinilos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleccionamos la base de datos que acabamos de crear
USE tienda_vinilos;

-- Creamos la tabla 'usuarios' con todos los campos obligatorios del trabajo
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'usuario',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    archivado_en TIMESTAMP NULL DEFAULT NULL
);