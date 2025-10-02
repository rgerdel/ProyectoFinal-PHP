/*Crea la base de datos e_commerce*/
CREATE DATABASE e_commerce_PF CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci; 

/*Crea la tabla usuarios.*/
CREATE TABLE usuarios (
	id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR (100) NOT NULL,
    email VARCHAR (150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('usuario','admin') NOT NULL DEFAULT 'usuario'
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/* Crea la tabla productos.*/
CREATE TABLE productos ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(200) NOT NULL, 
    descripcion TEXT NOT NULL, 
    precio DECIMAL(10,2) UNSIGNED NOT NULL CHECK (precio > 0), 
    stock INT NOT NULL DEFAULT 0 CHECK (stock>=0), 
    imagen_url VARCHAR(255) NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
); 

/* Crea la tabla password_resets.*/
CREATE TABLE password_resets ( 
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expira_en DATETIME NOT NULL,
    usado_en DATETIME NULL
)

