<?php
require_once 'conexion.php';
session_start();

// Control de acceso: solo administradores
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    try {
        // No permitimos que un admin se archive a sí mismo por accidente
        if ($id === $_SESSION['usuario_id']) {
            header('Location: admin_usuarios.php?error=propia_cuenta');
            exit;
        }

        // Actualizamos archivado_en con la fecha y hora actual
        $sql = 'UPDATE usuarios SET archivado_en = NOW() WHERE id = :id AND archivado_en IS NULL';
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['id' => $id]);

    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

header('Location: admin_usuarios.php');
exit;