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

$carouselImages = [
    [
        'src' => './img/pokemonCarrusel.jpg',
        'alt' => 'Carrusel Pokémon',
        'link' => './html/pokemon.php' // Enlace al nuevo HTML de exploración
    ],
    [
        'src' => './img/yuCarrusel.jpg',
        'alt' => 'Carrusel Yu-Gi-Oh!',
        'link' => './html/yugioh.php' // Enlace al nuevo HTML de exploración
    ],
    [
        'src' => './img/magikCarrusel.jpg',
        'alt' => 'Carrusel Magic',
        'link' => './html/magic.php' // Enlace al nuevo HTML de exploración
    ],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CardCapture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo.png"/>
    <link rel="stylesheet" href="./css/INDEXmain.css">
    <link rel="stylesheet" href="./css/nuevo_diseno.css"> </head>
<body>
    <header class="headerx">
    <div class="logo">CARDCAPTURE</div>
    <div class="header-icons">
        <img src="./img/fuego.png" alt="Icono de fuego" class="fuego-icon">
        <div class="user-menu">
            <div class="iconx" id="userIcon">
                <img src="./img/user.png" class="user-icon" alt="">
            </div>
            <ul class="dropdown" id="dropdownMenu">
                <?php if (!$sesionIniciada): ?>
                    <li><a href="./html/InicioSesion.html">Iniciar Sesión</a></li>
                <?php else: ?>
                    <li><a href="./html/perfil.php">Perfil</a></li>
                    <li><a href="./html/meGusta.php">Me gusta</a></li>
                    <li><a href="./html/publicaciones.html">Venda</a></li>
                    <li><a href="./html/chat.php">Bústia</a></li>
                    <li><a href="./php/logout.php">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
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
                <li><a href="./html/inicio.php?categoria=<?php echo urlencode($clave); ?>"><?php echo htmlspecialchars($nombre); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <section class="hero">
    <div class="hero-content">
        <h1>El Mercado Definitivo para Coleccionistas de Cartas</h1>
        <p>CardCapture es tu plataforma ideal para comprar, vender e intercambiar cartas coleccionables de tus juegos favoritos. Sumérgete en un universo de Magic: The Gathering, Pokémon, Yu-Gi-Oh!, One Piece, My Little Pony e Invizimals. Conecta con otros apasionados, descubre cartas raras y expande tu colección.</p>
        <a href="./html/explorar.php" class="explore-button">Explora</a>
    </div>
    <div class="hero-image">
        </div>
</section>

<script src="./js/animacionCarta.js"></script>

    <div class="carousel-container">
    <div class="carousel-wrapper" id="carouselWrapper">
        <?php foreach ($carouselImages as $image): ?>
            <div class="carousel-item">
                <a href="<?php echo $image['link']; ?>">
                    <img src="<?php echo $image['src']; ?>" alt="<?php echo $image['alt']; ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="carousel-controls">
        <button class="prev-btn" onclick="prevSlide()">&#10094;</button>
        <button class="next-btn" onclick="nextSlide()">&#10095;</button>
    </div>
    <div class="carousel-indicators" id="carouselIndicators">
        <?php foreach ($carouselImages as $index => $image): ?>
            <button class="<?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></button>
        <?php endforeach; ?>
    </div>
</div>

    <div class="content">
        <h2>Únete a la Comunidad CardCapture</h2>
        <p>Descubre ofertas increíbles, encuentra esa carta que tanto buscas y conecta con vendedores y compradores de todo el mundo. ¡Tu próxima gran adquisición está a solo un clic de distancia!</p>
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
    </footer>

   <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu Categorías
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const menuLista = document.getElementById('menuLista');
            const menuCategorias = document.getElementById('menuCategorias');
            const header = document.querySelector('.headerx');

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

            window.addEventListener('scroll', () => {
                // Añadimos o quitamos la clase 'sticky' al menu-categorias basado en la posición del scroll
                if (window.scrollY > header.offsetHeight) {
                    menuCategorias.classList.add('sticky');
                } else {
                    menuCategorias.classList.remove('sticky');
                }
            });

            // Carrusel (el resto del código del carrusel se mantiene igual)
            const carouselContainer = document.querySelector('.carousel-container');
            const carouselWrapper = document.getElementById('carouselWrapper');
            const carouselItems = document.querySelectorAll('.carousel-item');
            const prevBtn = document.querySelector('.carousel-controls .prev-btn');
            const nextBtn = document.querySelector('.carousel-controls .next-btn');
            const carouselIndicatorsContainer = document.getElementById('carouselIndicators');
            const carouselIndicators = document.querySelectorAll('#carouselIndicators button');
            const numImages = carouselItems.length;
            let currentIndex = 0;
            let intervalId;
            let isDragging = false;
            let startX;
            let scrollLeft;

            function updateCarousel() {
                const translateX = -currentIndex * 100 + '%';
                carouselWrapper.style.transform = `translateX(${translateX})`;
                updateIndicators();
            }

            function updateIndicators() {
                carouselIndicators.forEach((indicator, index) => {
                    indicator.classList.remove('active');
                    if (index === currentIndex) {
                        indicator.classList.add('active');
                    }
                });
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % numImages;
                updateCarousel();
                resetInterval();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + numImages) % numImages;
                updateCarousel();
                resetInterval();
            }

            function goToSlide(index) {
                currentIndex = index;
                updateCarousel();
                resetInterval();
            }

            function startAutoplay() {
                intervalId = setInterval(nextSlide, 3000); // Cambiado a 3 segundos
            }

            function stopAutoplay() {
                clearInterval(intervalId);
            }

            function resetInterval() {
                stopAutoplay();
                startAutoplay();
            }

            // Drag functionality
            carouselWrapper.addEventListener('mousedown', (e) => {
                isDragging = true;
                startX = e.pageX - carouselWrapper.offsetLeft;
                scrollLeft = carouselWrapper.scrollLeft;
                carouselWrapper.classList.add('dragging');
                stopAutoplay();
            });

            carouselWrapper.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - carouselWrapper.offsetLeft;
                const walk = (x - startX) * 1; // Adjust sensitivity
                carouselWrapper.scrollLeft = scrollLeft - walk;
            });

            carouselWrapper.addEventListener('mouseup', () => {
                isDragging = false;
                carouselWrapper.classList.remove('dragging');
                // Calculate which slide to snap to
                const scrollWidth = carouselWrapper.scrollWidth - carouselWrapper.offsetWidth;
                const scrollPercentage = carouselWrapper.scrollLeft / scrollWidth;
                const newIndex = Math.round(scrollPercentage * (numImages - 1));
                currentIndex = Math.max(0, Math.min(newIndex, numImages - 1));
                updateCarousel();
                startAutoplay();
            });

            carouselWrapper.addEventListener('mouseleave', () => {
                if (isDragging) {
                    isDragging = false;
                    carouselWrapper.classList.remove('dragging');
                    const scrollWidth = carouselWrapper.scrollWidth - carouselWrapper.offsetWidth;
                    const scrollPercentage = carouselWrapper.scrollLeft / scrollWidth;
                    const newIndex = Math.round(scrollPercentage * (numImages - 1));
                    currentIndex = Math.max(0, Math.min(newIndex, numImages - 1));
                    updateCarousel();
                    startAutoplay();
                }
            });

            // Touch event listeners for mobile
            carouselWrapper.addEventListener('touchstart', (e) => {
                isDragging = true;
                startX = e.touches[0].pageX - carouselWrapper.offsetLeft;
                scrollLeft = carouselWrapper.scrollLeft;
                carouselWrapper.classList.add('dragging');
                stopAutoplay();
            });

            carouselWrapper.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const x = e.touches[0].pageX - carouselWrapper.offsetLeft;
                const walk = (x - startX) * 1;
                carouselWrapper.scrollLeft = scrollLeft - walk;
            });

            carouselWrapper.addEventListener('touchend', () => {
                isDragging = false;
                carouselWrapper.classList.remove('dragging');
                const scrollWidth = carouselWrapper.scrollWidth - carouselWrapper.offsetWidth;
                const scrollPercentage = carouselWrapper.scrollLeft / scrollWidth;
                const newIndex = Math.round(scrollPercentage * (numImages - 1));
                currentIndex = Math.max(0, Math.min(newIndex, numImages - 1));
                updateCarousel();
                startAutoplay();
            });

            // Initialize
            updateCarousel();
            startAutoplay();
        });
    </script>
    <script src="./js/scriptHeader.js"></script>
</body>
</html>