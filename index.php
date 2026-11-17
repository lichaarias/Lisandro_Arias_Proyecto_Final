<?php
session_start();
$destinoCompra = isset($_SESSION['usuario_id']) ? 'carrito_exito.php' : 'login.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda de Vinilos - Inicio</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo"> Vinilos & CDs</a>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li><a href="panel.php">Mi Panel (<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>)</a></li>
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                        <li><a href="admin_usuarios.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Bienvenido a nuestra Disquería </h1>
        <p>Encontrá los mejores clásicos en formato físico, CDs importados y vinilos de colección.</p>

        <div class="seccion-catalogo">
            <h2> Vinilos y CDs Destacados</h2>
            <div class="productos-grid">
                
                <div class="producto-card">
                    <img src="img/abbey_road.png" alt="Abbey Road" class="producto-img">
                    <h3>Abbey Road</h3>
                    <p class="artista">The Beatles (Vinyl LP)</p>
                    <div class="precio">$28.500</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

                <div class="producto-card">
                    <img src="img/fine_line.png" alt="Fine Line" class="producto-img">
                    <h3>Fine Line</h3>
                    <p class="artista">Harry Styles (Vinyl LP)</p>
                    <div class="precio">$32.000</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

                <div class="producto-card">
                    <img src="img/back_in_black.png" alt="Back in Black" class="producto-img">
                    <h3>Back in Black</h3>
                    <p class="artista">AC/DC (CD Importado)</p>
                    <div class="precio">$15.000</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

                <div class="producto-card">
                    <img src="img/alta_suciedad.png" alt="Alta Suciedad" class="producto-img">
                    <h3>Alta Suciedad</h3>
                    <p class="artista">Andres Calamaro (Vinyl Remaster)</p>
                    <div class="precio">$30.000</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

                <div class="producto-card">
                    <img src="img/lgdlc.png" alt="La Grasa de las Capitales" class="producto-img">
                    <h3>La Grasa de las Capitales</h3>
                    <p class="artista">Seru Giran (Vinyl Remaster)</p>
                    <div class="precio">$30.000</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

                <div class="producto-card">
                    <img src="img/nesrar.png" alt="No es solo Rock'n'Roll" class="producto-img">
                    <h3>No es solo Rock'n'Roll</h3>
                    <p class="artista">Intoxicados (Vinyl Remaster)</p>
                    <div class="precio">$30.000</div>
                    <a href="index.php" class="btn-comprar">Comprar</a>
                </div>

            </div>
        </div>
    </main>

    <footer>
        <p>Trabajo Práctico Final - Informática 4.º Año</p>
    </footer>

</body>
</html>