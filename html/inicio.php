<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

// Incluir el archivo de conexión a la base de datos
include '../php/db.php';

// Obtener la categoría de la URL
$categoria = $_GET['categoria'];

// Consulta SQL para obtener los anuncios de la categoría seleccionada
$sql = "SELECT * FROM publicacions WHERE categoria = '$categoria'";

// Si hay una sesión iniciada, filtrar los anuncios del usuario actual
if ($sesionIniciada) {
    $usuario_id = $_SESSION['user_id'];
    $sql .= " AND usuario_id != $usuario_id";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardCapture - Anuncios de <?php echo $categoria; ?></title>
    <link rel="stylesheet" href="../css/css.css">
    <link rel="stylesheet" href="../css/PAGINIcssHeaderFooter.css">
    <style>
        .anuncios {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        .anuncio {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            
        }

        .anuncio:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .anuncio img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .anuncio h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #333;
        }

        .anuncio .precio {
            font-weight: bold;
            color: #DE9929;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <header id="header">
        <canvas id="headerCanvas"></canvas>
        <div class="header-content">
            <div class="logo"><h1>CardCapture</h1></div>
            <input type="text" class="search-bar" placeholder="Busca una carta en específico">
            <div class="user-menu">
                <div class="icon-user" id="userIcon">
                    <span class="user-icon">👤</span>
                    <span class="arrow">▼</span>
                </div>
                <ul class="dropdown" id="dropdownMenu">
                    <?php if (!$sesionIniciada): ?>
                        <li><a href="InicioSesion.html">Iniciar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="./perfil.php">Perfil</a></li>
                        <li><a href="../php/logout.php">Compra</a></li>
                        <li><a href="../php/logout.php">Venda</a></li>
                        <li><a href="./chat.php">Bústia</a></li>
                        <li><a href="../php/logout.php">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <script src="../js/scriptHeader.js"></script>
            <nav class="main-nav">
                <ul class="menu menu-games">
                    <li><a href="#">MTG</a></li>
                    <li><a href="#">Pokémon</a></li>
                    <li><a href="#">One Piece</a></li>
                    <li><a href="#">Yu-Gi-Oh!</a></li>
                    <li><a href="#">MLP</a></li>
                    <li><a href="#">Invizimals</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="paTras">
        <a href="../index.php" class="tornar">&#8592; Volver al Inicio</a>
    </div>
    <main>
        <section class="anuncios">
            <?php
            if ($result->num_rows > 0) {
                // Mostrar los anuncios
                while ($row = $result->fetch_assoc()) {
                    // Asegúrate de usar publicacion_id en el enlace
                    echo "<a href='detalle_publicacion.php?id=" . $row['publicacion_id'] . "' class='anuncio'>";
                    echo "<img src='" . $row['imagen'] . "' alt='Imagen del anuncio'>";
                    echo "<h3>" . $row['titulo'] . "</h3>";
                    echo "<p class='precio'>" . $row['precio'] . "€</p>";
                    echo "</a>";
                }
            } else {
                echo "<p>No hay anuncios disponibles en esta categoría.</p>";
            }
            ?>
        </section>
    </main>
    <footer id="footer" class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h2 id="footerTitle">CardCapture</h2>
                <p>Explora, compra y vende cartas de colección fácilmente.</p>
            </div>
            <div class="footer-social">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="https://www.facebook.com/" target="_blank"><img class="icon" src="../img/facebook.png" alt="Facebook"></a>
                    <a href="https://x.com/home?lang=es" target="_blank"><img class="icon" src="../img/twitter.png" alt="Twitter"></a>
                    <a href="https://www.instagram.com/" target="_blank"><img class="icon" src="../img/instagram.png" alt="Instagram"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p id="footerText">&copy; 2025 CardCapture. Todos los derechos reservados.</p>
        </div>
        <canvas id="footerCanvas"></canvas> <script src="../js/footerAnimation.js"></script>
    </footer>
</body>
</html>

<?php
$conn->close();
?>