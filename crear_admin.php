<?php
require_once 'conexion.php';

try {
    $nombre = 'Lisandro';
    $apellido = 'Admin';
    $dni = '50323594';
    $email = 'lisandro.jedi@gmail.com';
    $password = 'German123'; 
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO usuarios (nombre, apellido, dni, email, password_hash, rol) 
            VALUES (:nombre, :apellido, :dni, :email, :password_hash, "admin")';
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'dni' => $dni,
        'email' => $email,
        'password_hash' => $passwordHash
    ]);

    echo "<h1>¡Administrador creado con éxito!</h1>";
    echo "<p>Correo: <strong>lisandro.jedi@gmail.com</strong></p>";
    echo "<p>Contraseña: <strong>German123</strong></p>";
    echo "<br><p><a href='login.php'>Ir a iniciar sesión</a></p>";

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo "<p style='color: red;'>El correo o el DNI ya existen en la base de datos. Entrá a phpMyAdmin, borrá la tabla 'usuarios' o vaciala, y volvé a recargar esta página.</p>";
    } else {
        error_log($e->getMessage());
        echo "<p>Error al crear el administrador.</p>";
    }
}