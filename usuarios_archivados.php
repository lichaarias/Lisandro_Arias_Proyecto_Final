<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$usuariosArchivados = [];
$error = '';

try {
    // Consultamos solo los usuarios que tienen archivado_en con fecha (no NULL)
    $sql = 'SELECT id, nombre, apellido, dni, email, rol, archivado_en FROM usuarios WHERE archivado_en IS NOT NULL ORDER BY id DESC';
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $usuariosArchivados = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No se pudieron cargar los usuarios archivados.';
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Archivados - Tienda de Vinilos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo">🎵 Vinilos & CDs [Admin]</a>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="admin_usuarios.php">Usuarios Activos</a></li>
                <li><a href="usuarios_archivados.php">Archivados</a></li>
                <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Usuarios Archivados 📦</h1>
        <p>Listado de cuentas dadas de baja en el sistema.</p>
        <br>

        <?php if (!empty($error)): ?>
            <div style="background-color: #ff475722; border: 1px solid #ff4757; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #ff6b81;">
                <?= escapar($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($usuariosArchivados)): ?>
            <p style="margin-top: 20px; color: #aaa;">No hay usuarios archivados en este momento.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre y Apellido</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Fecha de Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuariosArchivados as $usu): ?>
                        <tr>
                            <td><?= escapar((string)$usu['id']) ?></td>
                            <td><?= escapar($usu['nombre'] . ' ' . $usu['apellido']) ?></td>
                            <td><?= escapar($usu['dni']) ?></td>
                            <td><?= escapar($usu['email']) ?></td>
                            <td><?= escapar($usu['archivado_en']) ?></td>
                            <td>
                                <a href="reactivar_usuario.php?id=<?= $usu['id'] ?>" onclick="return confirm('¿Estás seguro de reactivar este usuario?');" style="color: #2ed573; text-decoration: none; font-weight: bold;">Reactivar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <br>
        <div style="margin-top: 20px;">
            <a href="admin_usuarios.php" style="color: #ffa502; font-weight: bold; text-decoration: none;">← Volver a usuarios activos</a>
        </div>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>