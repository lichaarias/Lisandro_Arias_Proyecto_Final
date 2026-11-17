<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. No tenés permisos de administrador.');
}

$buscar = trim($_GET['buscar'] ?? '');
$usuarios = [];
$error = '';

try {
    $sql = 'SELECT id, nombre, apellido, dni, email, rol, creado_en FROM usuarios WHERE archivado_en IS NULL';
    $parametros = [];

    if ($buscar !== '') {
        $sql .= ' AND (nombre LIKE :nombre OR apellido LIKE :apellido OR dni LIKE :dni OR email LIKE :email)';
        $termino = '%' . $buscar . '%';
        $parametros = [
            'nombre' => $termino,
            'apellido' => $termino,
            'dni' => $termino,
            'email' => $termino
        ];
    }

    $sql .= ' ORDER BY id DESC';

    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $usuarios = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No se pudieron consultar los usuarios.';
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Tienda de Vinilos</title>
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
        <h1>Panel de Administración de Usuarios 🛠️</h1>
        <p>Bienvenido, <strong><?= escapar($_SESSION['usuario_nombre'] ?? 'Admin') ?></strong>. Desde aquí podés gestionar las cuentas de la tienda.</p>
        <br>

        <form method="GET" action="admin_usuarios.php" style="flex-direction: row; gap: 10px; margin-bottom: 20px;">
            <input type="search" name="buscar" value="<?= escapar($buscar) ?>" placeholder="Buscar por nombre, apellido, DNI o email..." maxlength="100" style="flex: 1;">
            <button type="submit" style="padding: 10px 20px;">Buscar</button>
            <?php if ($buscar !== ''): ?>
                <a href="admin_usuarios.php" style="background-color: #444; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; display: flex; align-items: center; font-weight: bold;">Limpiar</a>
            <?php endif; ?>
        </form>

        <?php if (!empty($error)): ?>
            <div style="background-color: #ff475722; border: 1px solid #ff4757; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #ff6b81;">
                <?= escapar($error) ?>
            </div>
        <?php endif; ?>

        <p><strong>Total de usuarios activos:</strong> <?= count($usuarios) ?></p>

        <?php if (empty($usuarios)): ?>
            <p style="margin-top: 20px; color: #aaa;">No se encontraron usuarios activos.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre y Apellido</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usu): ?>
                        <tr>
                            <td><?= escapar((string)$usu['id']) ?></td>
                            <td><?= escapar($usu['nombre'] . ' ' . $usu['apellido']) ?></td>
                            <td><?= escapar($usu['dni']) ?></td>
                            <td><?= escapar($usu['email']) ?></td>
                            <td><?= escapar($usu['rol']) ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $usu['id'] ?>" style="color: #ffa502; text-decoration: none; margin-right: 10px; font-weight: bold;">Editar</a>
                                <a href="archivar_usuario.php?id=<?= $usu['id'] ?>" onclick="return confirm('¿Estás seguro de archivar este usuario?');" style="color: #ff4757; text-decoration: none; font-weight: bold;">Archivar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <br>
        <div style="margin-top: 20px;">
            <a href="usuarios_archivados.php" style="color: #3742fa; font-weight: bold; text-decoration: none;">📦 Ver usuarios archivados</a>
        </div>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>