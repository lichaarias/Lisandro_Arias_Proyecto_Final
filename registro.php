<?php
require_once 'conexion.php';

$errores = [];
$exito = '';

$nombre = '';
$apellido = '';
$dni = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos y limpiamos los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones obligatorias
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
    if (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    // Si no hay errores de validación, procedemos a guardar en la base de datos
    if (empty($errores)) {
        try {
            // Encriptamos la contraseña con password_hash por seguridad
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO usuarios (nombre, apellido, dni, email, password_hash, rol) 
                    VALUES (:nombre, :apellido, :dni, :email, :password_hash, "usuario")';
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password_hash' => $passwordHash
            ]);

            $exito = '¡Cuenta creada con éxito! Ya podés iniciar sesión.';
            
            // Limpiamos los campos
            $nombre = '';
            $apellido = '';
            $dni = '';
            $email = '';

        } catch (PDOException $e) {
            // El código SQLSTATE '23000' indica duplicidad en campos UNIQUE (DNI o email)
            if ($e->getCode() === '23000') {
                $errores[] = 'El DNI o el correo electrónico ya se encuentran registrados.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Ocurrió un error al procesar el registro.';
            }
        }
    }
}

// Función para escapar datos al mostrarlos en HTML (Previene ataques XSS)
function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Tienda de Vinilos</title>
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
        <h1>Crear una cuenta</h1>

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

        <form action="registro.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= escapar($nombre) ?>" maxlength="80" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?= escapar($apellido) ?>" maxlength="80" required>

            <label for="dni">DNI (7 u 8 dígitos):</label>
            <input type="text" id="dni" name="dni" value="<?= escapar($dni) ?>" inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" value="<?= escapar($email) ?>" maxlength="254" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registrarse</button>
        </form>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>