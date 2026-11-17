<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    try {
        // Volvemos archivado_en a NULL para que la cuenta vuelva a estar activa
        $sql = 'UPDATE usuarios SET archivado_en = NULL WHERE id = :id AND archivado_en IS NOT NULL';
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['id' => $id]);

    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

header('Location: usuarios_archivados.php');
exit;