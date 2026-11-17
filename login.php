<?php
require_once 'conexion.php';
session_start();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Por favor, completá todos los campos.';
    } else {
        try {
            // Buscamos al usuario por su correo y que no esté archivado
            $sql = 'SELECT id, nombre, apellido, email, password_hash, rol FROM usuarios 
                    WHERE email = :email AND archivado_en IS NULL LIMIT 1';
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch();

            // Verificamos si el usuario existe y si la contraseña coincide con el hash
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                
                // Regeneramos el ID de sesión por seguridad (previene ataques de fijación de sesión)
                session_regenerate_id(true);

                // Guardamos los datos del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirigimos según el rol del usuario
                if ($usuario['rol'] === 'admin') {
                    header('Location: admin_usuarios.php');
                } else {
                    header('Location: panel.php');
                }
                exit;

            } else {
                $error = 'Correo o contraseña incorrectos.';
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Ocurrió un error al intentar iniciar sesión.';
        }
    }
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
    <title>Iniciar Sesión - Tienda de Vinilos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo">🎵 Vinilos & CDs</a>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.php">Iniciar Sesión</a></li>
                <li><a href="registro.php">Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Iniciar Sesión</h1>

        <?php if (!empty($error)): ?>
            <div style="background-color: #ff475722; border: 1px solid #ff4757; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #ff6b81;">
                <?= escapar($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" value="<?= escapar($email) ?>" maxlength="254" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>