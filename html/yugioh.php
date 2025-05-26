<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

$categorias = [
    'Magic' => 'Magic: The Gathering',
    'Pokemon' => 'Pokémon',
    'One Piece' => 'One Piece',
    'Yu-Gi-Oh' => 'Yu-Gi-Oh!',
    'My Little Pony' => 'My Little Pony',
    'Invizimals' => 'Invizimals'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades Yu-Gi-Oh! - CardCapture</title>
    <link rel="stylesheet" href="../css/INDEXmain.css">
    <link rel="stylesheet" href="../css/yugioh.css">
            <link rel="shortcut icon" href="../img/logo.png" />

    <style>
        /* --- Estilos específicos para la página de Yu-Gi-Oh! --- */
        .hero {
            display: flex;
            align-items: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .hero-content {
            flex: 1;
            padding-right: 20px;
        }

        .hero-content h1 {
            color: #dc3545; /* Rojo Yu-Gi-Oh! */
            margin-bottom: 10px;
        }

        .hero-content p {
            color: #555;
            line-height: 1.6;
        }

        .hero-image-juego {
            flex-shrink: 0;
            width: 200px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .hero-image-juego img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .novedades-juego {
            padding: 20px;
        }

        .novedad {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .novedad h2 {
            color: #343a40;
        }

        .novedad p {
            line-height: 1.7;
        }

        .novedad img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .fecha-publicacion {
            color: #777;
            font-size: 0.9em;
        }

        /* --- Nuevo estilo para el cursor del logo --- */
        .logo {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="headerx">
        <div class="logo">CARDCAPTURE</div>
        <div class="user-menu">
            <div class="iconx" id="userIcon">
                <img src="../img/user.png" class="user-icon" alt="">
            </div>
            <ul class="dropdown" id="dropdownMenu">
                <?php if (!$sesionIniciada): ?>
                    <li><a href="./InicioSesion.php">Iniciar Sesión</a></li>
                <?php else: ?>
                    <li><a href="./perfil.php">Perfil</a></li>
                    <li><a href="./meGusta.php">Me gusta</a></li>
                    <li><a href="./publicaciones.php">Venda</a></li>
                    <li><a href="./chat.php">Bústia</a></li>
                    <li><a href="../php/logout.php">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <nav class="menu-categorias" id="menuCategorias">
        <button class="hamburger-btn" id="hamburgerBtn">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>
        <ul class="menu-lista" id="menuLista">
            <?php foreach ($categorias as $clave => $nombre): ?>
                <li><a href="../html/inicio.php?categoria=<?php echo urlencode($clave); ?>"><?php echo htmlspecialchars($nombre); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <main class="contenido-principal">
        <section class="hero">
            <div class="hero-content">
                <h1>Novedades y Actualizaciones de Yu-Gi-Oh!</h1>
                <p>Mantente al día con las últimas noticias, expansiones y eventos del Juego de Cartas Coleccionables Yu-Gi-Oh! en CardCapture.</p>
            </div>
            <div class="hero-image hero-image-juego">
                <img src="../img/yugiohTitulo.png" alt="Imagen de Yu-Gi-Oh! TCG">
            </div>
        </section>

        <section class="novedades-juego">
            <div class="novedad">
                <div class="novedad-imagen">
                    <img src="../img/extensionYu.png" alt="Novedad Yu-Gi-Oh! 1">
                </div>
                <div class="novedad-texto">
                    <h2>Nueva Expansión de Yu-Gi-Oh! en Camino</h2>
                    <p>Prepárate para la llegada de la nueva y emocionante expansión de Yu-Gi-Oh! con monstruos poderosos y estrategias innovadoras.</p>
                    <p class="fecha-publicacion">Publicado el 15 de mayo de 2025</p>
                </div>
            </div>

            <div class="novedad invertida">
                <div class="novedad-imagen">
                    <img src="../img/torneoYu.png" alt="Torneo Yu-Gi-Oh!">
                </div>
                <div class="novedad-texto">
                    <h2>Gran Torneo Nacional de Yu-Gi-Oh! Anunciado</h2>
                    <p>Participa en el gran torneo nacional de Yu-Gi-Oh! y demuestra tus habilidades como duelista. ¡Grandes premios esperan a los ganadores!</p>
                    <p class="fecha-publicacion">Publicado el 10 de mayo de 2025</p>
                </div>
            </div>

            <div class="novedad">
                <div class="novedad-imagen">
                    <img src="../img/productosYu.png" alt="Nuevo Producto Yu-Gi-Oh!">
                </div>
                <div class="novedad-texto">
                    <h2>Nuevo Producto Especial de Yu-Gi-Oh! Revelado</h2>
                    <p>Un nuevo producto especial con cartas raras y accesorios exclusivos ha sido revelado para los fans de Yu-Gi-Oh!.</p>
                    <p class="fecha-publicacion">Publicado el 2 de mayo de 2025</p>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer">
        <canvas id="footerCanvas"></canvas>
        <div class="footer-container">
            <div class="footer-logo">
                <h2>CARDCAPTURE</h2>
                <p>Tu mercado definitivo para coleccionistas de cartas.</p>
            </div>
            <div class="footer-social">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#"><img src="../img/Facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="../img/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="../img/twitter.png" alt="Twitter"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p id="footerText">&copy; 2025 CardCapture. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script src="../js/footer_animation.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // *** Lógica para el Dropdown de Usuario ***
        const userIcon = document.getElementById('userIcon');
        const dropdownMenu = document.getElementById('dropdownMenu');

        function toggleDropdown() {
            dropdownMenu.classList.toggle('open');
        }

        userIcon.addEventListener('click', toggleDropdown);

        document.addEventListener('click', function(event) {
            const isClickInside = userIcon.contains(event.target) || dropdownMenu.contains(event.target);
            if (!isClickInside && dropdownMenu.classList.contains('open')) {
                dropdownMenu.classList.remove('open');
            }
        });

        // *** Lógica para el Menú de Categorías Sticky ***
        const menuCategorias = document.getElementById('menuCategorias');
        const header = document.querySelector('.headerx');
        const logo = document.querySelector('.logo'); // Seleccionamos el elemento con la clase "logo"

        window.addEventListener('scroll', () => {
            if (window.scrollY > header.offsetHeight) {
                menuCategorias.classList.add('sticky');
            } else {
                menuCategorias.classList.remove('sticky');
            }
        });

        // *** Lógica para volver a la página anterior al hacer clic en el logo ***
        logo.addEventListener('click', function() {
            window.history.back();
        });

        // *** Lógica para el Menú Hamburguesa ***
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const menuLista = document.getElementById('menuLista');

        function toggleMenu() {
            menuLista.classList.toggle('open');
        }

        hamburgerBtn.addEventListener('click', toggleMenu);

        document.addEventListener('click', function(event) {
            const isClickInside = hamburgerBtn.contains(event.target) || menuLista.contains(event.target);
            if (!isClickInside && menuLista.classList.contains('open')) {
                menuLista.classList.remove('open');
            }
        });
    });
</script>
    <script src="../js/scriptHeader.js"></script>
    <script src="../js/footerAnimation.js"></script>
</body>
</html>