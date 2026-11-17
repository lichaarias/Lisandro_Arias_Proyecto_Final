<?php
require_once 'conexion.php';
session_start();

// Control de acceso: si no hay sesión activa, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$usuarioDatos = null;

try {
    // Consultamos los datos actualizados del usuario logueado
    $sql = 'SELECT id, nombre, apellido, dni, email, rol, creado_en FROM usuarios WHERE id = :id LIMIT 1';
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id' => $_SESSION['usuario_id']]);
    $usuarioDatos = $stmt->fetch();

    if (!$usuarioDatos) {
        // Si el usuario ya no existe en la base de datos, cerramos sesión
        header('Location: cerrar_sesion.php');
        exit;
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No se pudieron cargar tus datos personales.';
}

// Función para escapar datos en HTML
function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Tienda de Vinilos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo">🎵 Vinilos & CDs</a>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="panel.php">Mi Panel</a></li>
                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                    <li><a href="admin_usuarios.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Panel Personal de Cliente 🎧</h1>
        <p>Bienvenido/a, <strong><?= escapar($usuarioDatos['nombre'] ?? '') ?></strong>. Aquí podés ver los datos de tu cuenta.</p>
        <br>

        <?php if (!empty($error)): ?>
            <div style="background-color: #ff475722; border: 1px solid #ff4757; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #ff6b81;">
                <?= escapar($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($usuarioDatos): ?>
            <div style="background-color: #252525; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <p style="margin-bottom: 10px;"><strong>Nombre:</strong> <?= escapar($usuarioDatos['nombre']) ?></p>
                <p style="margin-bottom: 10px;"><strong>Apellido:</strong> <?= escapar($usuarioDatos['apellido']) ?></p>
                <p style="margin-bottom: 10px;"><strong>DNI:</strong> <?= escapar($usuarioDatos['dni']) ?></p>
                <p style="margin-bottom: 10px;"><strong>Correo electrónico:</strong> <?= escapar($usuarioDatos['email']) ?></p>
                <p style="margin-bottom: 10px;"><strong>Rol:</strong> <?= escapar($usuarioDatos['rol']) ?></p>
                <p><strong>Fecha de registro:</strong> <?= escapar($usuarioDatos['creado_en']) ?></p>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>