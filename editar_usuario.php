<?php
require_once 'conexion.php';
session_start();

// Control de acceso: solo administradores
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. No tenés permisos de administrador.');
}

// Validamos el ID que llega por la URL (GET) o por el formulario (POST)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador de usuario inválido.');
}

$errores = [];
$exito = '';

// Si se envió el formulario de edición (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'usuario';

    // Validaciones
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if ($apellido === '') {
        $errores[] = 'El apellido es obligatorio.';
    }
    if ($dni === '' || !preg_match('/^[0-9]{7,8}$/', $dni)) {
        $errores[] = 'El DNI debe tener 7 u 8 números.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo electrónico válido.';
    }
    if ($rol !== 'usuario' && $rol !== 'admin') {
        $errores[] = 'Rol seleccionado inválido.';
    }

    if (empty($errores)) {
        try {
            $sql = 'UPDATE usuarios 
                    SET nombre = :nombre, apellido = :apellido, dni = :dni, email = :email, rol = :rol 
                    WHERE id = :id AND archivado_en IS NULL';
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'rol' => $rol,
                'id' => $id
            ]);

            $exito = '¡Datos del usuario actualizados correctamente!';

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'El DNI o el correo electrónico ya pertenecen a otro usuario.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Ocurrió un error al actualizar los datos.';
            }
        }
    }
}

// Consultamos los datos actuales del usuario para rellenar el formulario
try {
    $stmt = $conexion->prepare('SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE id = :id AND archivado_en IS NULL LIMIT 1');
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        http_response_code(404);
        exit('Usuario no encontrado o se encuentra archivado.');
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Error al cargar los datos del usuario.');
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Tienda de Vinilos</title>
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
        <h1>Editar Usuario #<?= escapar((string)$usuario['id']) ?> ✏️</h1>

        <?php if (!empty($exito)): ?>
            <p style="color: #2ed573; font-weight: bold; margin-bottom: 15px;"><?= escapar($exito) ?></p>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div style="background-color: #ff475722; border: 1px solid #ff4757; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <ul style="margin-left: 20px; color: #ff6b81;">
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="editar_usuario.php?id=<?= $usuario['id'] ?>" method="POST">
            <input type="hidden" name="id" value="<?= escapar((string)$usuario['id']) ?>">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= escapar($usuario['nombre']) ?>" maxlength="80" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?= escapar($usuario['apellido']) ?>" maxlength="80" required>

            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" value="<?= escapar($usuario['dni']) ?>" inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" value="<?= escapar($usuario['email']) ?>" maxlength="254" required>

            <label for="rol">Rol del usuario:</label>
            <select id="rol" name="rol" style="padding: 10px; border-radius: 5px; border: 1px solid #444; background-color: #2b2b2b; color: #fff; font-size: 1rem;">
                <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario común</option>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" style="flex: 1;">Guardar Cambios</button>
                <a href="admin_usuarios.php" style="background-color: #444; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; text-align: center;">Volver</a>
            </div>
        </form>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>