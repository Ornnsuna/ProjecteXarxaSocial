<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Xarxa Social</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/INDEXmain.css">
</head>
<body>
    <header class="headerx">
        <div class="logo">CARDCAPTURE</div>
        <div class="user-menu">
            <div class="iconx" id="userIcon">
                <img src="./img/user.png" class="user-icon" alt="">
            </div>
            <ul class="dropdown" id="dropdownMenu">
                <?php if (!$sesionIniciada): ?>
                    <li><a href="./html/InicioSesion.html">Iniciar Sesión</a></li>
                <?php else: ?>
                    <li><a href="./html/perfil.php">Perfil</a></li>
                    <li><a href="#">Me gusta</a></li>
                    <li><a href="./html/publicaciones.html">Venda</a></li>
                    <li><a href="./html/chat.php">Bústia</a></li>
                    <li><a href="./php/logout.php">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <script src="./js/scriptHeader.js"></script>
    <div class="content">
        <h1>Bienvenido a mi página de cartas</h1>
        <p>Explora las cartas más icónicas de Magic, Pokémon, Yu-Gi-Oh! y One Piece.</p>
    </div>
    <div class="card-container"></div>
    <div class="card-container2 col-12 col-sm-12 col-md-12 col-lg-12">
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="magic" href="./html/inicio.php?categoria=Magic"><img src="./html/index.html" alt=""></a>
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="pokemon" href="./html/inicio.php?categoria=Pokemon"><img src="/html/webs/cartas_pokemon.html" alt=""></a>
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="onepiece" href="./html/inicio.php?categoria=One Piece"><img src="./html/webs/cartas_onepiece.html" alt=""></a>
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="yugioh" href="./html/inicio.php?categoria=Yu-Gi-Oh"><img src="./html/webs/cartas_yugioh.html" alt=""></a>
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="mylittlepony" href="./html/inicio.php?categoria=My Little Pony"><img src="./html/webs/cartas_mytillepony.html" alt=""></a>
        <a class="card col-3 col-sm-3 col-md-4 col-lg-6" id="invizimals" href="./html/inicio.php?categoria=Invizimals"><img src="./html/webs/cartas_invizimals.html" alt=""></a>
    </div>

    <footer id="footer" class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h2 id="footerTitle">CardCapture</h2>
                <p>Explora, compra y vende cartas de colección fácilmente.</p>
            </div>
            <div class="footer-social">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="https://www.facebook.com/" target="_blank"><img class="icon" src="./img/facebook.png" alt="Facebook"></a>
                    <a href="https://x.com/home?lang=es" target="_blank"><img class="icon" src="./img/twitter.png" alt="Twitter"></a>
                    <a href="https://www.instagram.com/" target="_blank"><img class="icon" src="./img/instagram.png" alt="Instagram"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p id="footerText">&copy; 2025 CardCapture. Todos los derechos reservados.</p>
        </div>
        <canvas id="footerCanvas"></canvas> <script src="./js/footerAnimation.js"></script>
    </footer>
</body>
</html>