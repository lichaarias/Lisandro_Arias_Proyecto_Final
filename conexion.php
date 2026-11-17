<?php
$host = 'localhost';
$dbname = 'tienda_vinilos';
$usuario = 'root';
$password = '';

try {
    // Creamos la conexión con PDO indicando el servidor, la base de datos y la codificación utf8mb4
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $usuario,
        $password
    );

    // Configuramos PDO para que lance excepciones si hay un error en las consultas
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configuramos el modo de recuperación por defecto como un array asociativo (nombre de columna => valor)
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla la conexión, registramos el error internamente y mostramos un mensaje genérico por seguridad
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos.');
}