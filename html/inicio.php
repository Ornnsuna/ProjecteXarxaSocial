<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardCapture</title>
    <link rel="stylesheet" href="../css/css.css">
    <link rel="stylesheet" href="../css/PAGINIcssHeaderFooter.css">
</head>
<body>
    <header id="header">
        <canvas id="headerCanvas"></canvas> <div class="header-content">
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
                        <li><a href="./perfil.php">Mi perfil</a></li>
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
    <script>
        window.addEventListener("scroll", function () {
            let scrollPos = window.scrollY;
            let tableContainer = document.querySelector(".table-container");
            let anuncios = document.querySelector(".anuncios");

            if (scrollPos > 450) {
                tableContainer.classList.add("scrolled");
                if (window.innerWidth > 1024) {
                    anuncios.style.marginLeft = tableContainer.offsetWidth + 20 + "px";
                } else {
                    anuncios.style.marginLeft = 0; // Restablece el margen izquierdo en pantallas pequeñas
                }
            } else {
                tableContainer.classList.remove("scrolled");
                anuncios.style.marginLeft = 0; // Restablece el margen izquierdo
            }
        });
    </script>
    <div class="paTras">
        <a href="../index.php" class="tornar">&#8592; Volver al Inicio</a>
    </div>
    <main>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Juan Pérez</td>
                        <td>30</td>
                        <td>juan@example.com</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>María López</td>
                        <td>25</td>
                        <td>maria@example.com</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Carlos Gómez</td>
                        <td>35</td>
                        <td>carlos@example.com</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <section class="anuncios">
            <div id="apiData">Cargando datos...</div>
        </section>
        <script src="../js/api.js"></script>
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