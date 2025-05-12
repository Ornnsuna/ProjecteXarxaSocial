<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

// Incluir el archivo de conexión a la base de datos
include '../php/db.php';

// Obtener la categoría de la URL
$categoria = $_GET['categoria'];

// Consulta SQL base
$sql = "SELECT p.* FROM publicacions p WHERE p.categoria = '$categoria'";

// Si hay una sesión iniciada, filtrar los anuncios del usuario actual
if ($sesionIniciada) {
    $usuario_id = $_SESSION['user_id'];
    $sql .= " AND p.usuario_id != $usuario_id";
}

// Variables para los filtros
$orderBy = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_actualizacion_desc';
$precioMin = isset($_GET['precio_min']) ? $_GET['precio_min'] : 0;
$precioMax = isset($_GET['precio_max']) ? $_GET['precio_max'] : 20000;
$fechaFiltro = isset($_GET['fecha_filtro']) ? $_GET['fecha_filtro'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // Nuevo: término de búsqueda

// Función para añadir filtros de fecha a la consulta SQL
function aplicarFiltroFecha($sql, $fechaFiltro) {
    $hoy = date("Y-m-d H:i:s");
    if ($fechaFiltro === 'dia') {
        $ayer = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $sql .= " AND p.fecha_actualizacion >= '$ayer' AND p.fecha_actualizacion <= '$hoy'";
    } elseif ($fechaFiltro === 'semana') {
        $semanaPasada = date("Y-m-d 00:00:00", strtotime("-7 days"));
        $sql .= " AND p.fecha_actualizacion >= '$semanaPasada' AND p.fecha_actualizacion <= '$hoy'";
    } elseif ($fechaFiltro === 'mes') {
        $mesPasado = date("Y-m-d 00:00:00", strtotime("-1 month"));
        $sql .= " AND p.fecha_actualizacion >= '$mesPasado' AND p.fecha_actualizacion <= '$hoy'";
    }
    return $sql;
}

// Aplicar filtro de búsqueda por nombre
if (!empty($searchTerm)) {
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql .= " AND p.titulo LIKE '%$searchTerm%'";
}

// Aplicar filtros a la consulta SQL
if ($orderBy === 'precio_asc') {
    $sql .= " ORDER BY p.precio ASC";
} elseif ($orderBy === 'precio_desc') {
    $sql .= " ORDER BY p.precio DESC";
}

$sql = aplicarFiltroFecha($sql, $fechaFiltro);

if ($precioMin !== null && $precioMin !== '') {
    $sql .= " AND p.precio >= $precioMin";
} else {
    $sql .= " AND p.precio >= 0"; // Asegurar un mínimo de 0 si no se establece
}

if ($precioMax !== null && $precioMax !== '') {
    $sql .= " AND p.precio <= $precioMax";
} else {
    $sql .= " AND p.precio <= 20000"; // Asegurar un máximo de 20000 si no se establece
}

// Obtener el rango de precios máximo real de la base de datos (opcional, para un slider más dinámico)
$sqlMaxPrecio = "SELECT MAX(precio) AS max_precio FROM publicacions WHERE categoria = '$categoria'";
$resultMaxPrecio = $conn->query($sqlMaxPrecio);
$maxPrecioDB = $resultMaxPrecio->fetch_assoc()['max_precio'] ?? 20000; // Valor por defecto si no hay anuncios

$maxSlider = max(20000, $maxPrecioDB); // Usar 20000 o el máximo de la DB

$result = $conn->query($sql);

// Consulta para sugerencias de búsqueda (no se usa directamente aquí, se usa en buscar_sugerencias.php)
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
        .filter-container {
            position: fixed;
            top: 80px; /* Ajuste: Disminuí el valor de top */
            left: 10px;
            z-index: 1001;
        }
        .filter-tab {
        position: fixed;
        top: 90px; /* Ajusta la posición vertical según necesites */
        left: 0;
        z-index: 1002; /* Asegura que esté por encima del contenido */
        transition: left 0.3s ease-in-out;
    }

        .filter-toggle-btn {
            background-color: #DE9929;
            color: black;
            border: 1px solid black;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-toggle-btn:hover {
            background-color: #f5b854;
        }
        .filter-tab.open {
        left: 300px; /* Ancho del panel de filtro */
    }
        .filter-panel {
            position: fixed;
            top: 80px; /* Ajuste: Disminuí el valor de top */
            left: -320px; /* Oculto inicialmente */
            width: 300px;
            background-color: white;
            color: black;
            border-right: 1px solid #DE9929;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            z-index: 1000;
        }
        .filter-tab-btn {
            border: none;
            background-color: white;
            margin-left: -.9em;
        }


        .filter-icon {
            width: 1.5em;
            height: 1.5em;
        }

        .filter-panel.open {
            left: 0;
        }

        .filter-group {
            margin-bottom: 25px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: black;
        }

        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: white;
            color: black;
        }

        /* Estilos para el slider de rango */
        .range-slider-container {
            position: relative;
            width: 100%;
            height: 40px;
        }

        .range-slider {
            position: absolute;
            width: 100%;
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
        }

        .range-selected {
            position: absolute;
            height: 5px;
            background: #DE9929;
            border-radius: 3px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }

        .range-input {
            position: relative;
        }

        .range-input input {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 5px;
            background: transparent;
            pointer-events: none;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
        }

        .range-input input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: white;
            border: 1px solid #DE9929;
            border-radius: 50%;
            cursor: pointer;
            pointer-events: auto;
        }

        .range-input input::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: white;
            border: 1px solid #DE9929;
            border-radius: 50%;
            cursor: pointer;
            pointer-events: auto;
        }

        .price-inputs {
            display: flex;
            justify-content: space-between;
        }

        .price-inputs input[type="number"] {
            width: 45%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: white;
            color: black;
        }

        .filter-panel button[type="submit"],
        .filter-panel button.filter-toggle-btn {
            background-color: #DE9929;
            color: black;
            border: 1px solid black;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-sizing: border-box;
        }

        .filter-panel button[type="submit"]:hover,
        .filter-panel button.filter-toggle-btn:hover {
            background-color: #f5b854;
        }

       .anuncios {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
        margin-left: 20px; /* Espacio inicial */
        transition: margin-left 0.3s ease-in-out;
    }

        .anuncios.open-filter {
            margin-left: 320px; /* Espacio para el panel de filtros abierto */
        }

        .filter-container .filter-toggle-btn {
        display: none;
    }

        .anuncio {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            position: relative; /* Para posicionar el icono de me gusta */
        }

        .anuncio:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Estilos para la imagen del anuncio */
        .anuncio-imagen-container {
            width: 150px; /* Ancho fijo para todas las imágenes */
            height: 150px; /* Alto fijo para todas las imágenes */
            overflow: hidden; /* Recortar si la imagen no coincide con las dimensiones */
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .anuncio-imagen-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Cubrir el contenedor manteniendo la proporción */
        }

        .anuncio h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 1.1em;
            color: white;
        }

        .anuncio .precio {
            font-weight: bold;
            color: #DE9929;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        /* Estilos para el icono de me gusta y la animación */
        .like-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 1.5em;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .like-icon:hover {
            color: red;
        }

        .like-icon.liked {
            color: red;
        }

        .like-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background-color: rgba(255, 0, 0, 0.5);
            opacity: 0;
            transform: translate(-50%, -50%) scale(0);
            transition: width 0.3s ease-out, height 0.3s ease-out, opacity 0.3s ease-out;
        }

        .like-icon.liked::before {
            width: 20px;
            height: 20px;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        @media (max-width: 480px) {
        .anuncios {
            grid-template-columns: repeat(2, 1fr);
            minmax(100px, 1fr); /* Asegurar un ancho mínimo */
            gap: 10px;
            padding: 10px;
        }

        .anuncio-imagen {
            height: 8em; /* Reducir altura de la imagen */
        }

        .anuncio h3 {
            font-size: 0.8em;
        }

        .anuncio .precio {
            font-size: 0.7em;
        }

        .heart-icon {
            width: 1em;
            height: 1em;
        }
    }

    /* Pantallas pequeñas: ajustar un poco el tamaño */
    @media (min-width: 481px) and (max-width: 768px) {
        .anuncios {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            padding: 12px;
        }

        .anuncio-imagen {
            height: 10em;
        }

        .anuncio h3 {
            font-size: 0.85em;
        }

        .anuncio .precio {
            font-size: 0.75em;
        }

        .heart-icon {
            width: 1.1em;
            height: 1.1em;
        }
    }

        /* Estilos para las sugerencias de búsqueda */
        #suggestions {
        position: absolute;
        top: calc(100% + 5px); /* Ajustar la distancia */
        left: 0;
        width: 100%;
        background-color: #fff; /* Fondo blanco para coincidir con los anuncios */
        border: 1px solid #ddd; /* Borde sutil */
        border-top: none;
        border-radius: 8px; /* Bordes ligeramente redondeados */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra suave */
        z-index: 10;
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 0.9em; /* Tamaño de fuente un poco más pequeño */
    }

    #suggestions li {
        padding: 8px 12px; /* Espacio interior reducido */
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
        color: #333; /* Color de texto oscuro */
            border-radius: 8px; /* Bordes ligeramente redondeados */

    }

    #suggestions li:hover {
        background-color: #f5f5f5; /* Gris muy claro al pasar el ratón */
    border-radius: 8px; /* Bordes ligeramente redondeados */

    }

    .divSearch {
        position: relative;
        width: 40%; /* Mantener el ancho */
        margin-left: -5em; /* Mantener el margen */
    }
    .divSearch input[type=text] {
        padding: 8px; /* Reducir el padding */
        border: 1px solid #ccc;
        border-radius: 8px; /* Bordes ligeramente redondeados */
        margin: 0;
        width: 100%;
        height: 2.2em; /* Reducir la altura */
        font-size: 0.9em; /* Reducir el tamaño de la fuente */
        background-color: #f9f9f9; /* Fondo claro */
        color: #333;
    }

    .divSearch input[type=text]:focus {
        outline: none;
        border-color: #DE9929; /* Usar tu color amarillo/dorado al enfocar */
        box-shadow: 0 0 5px rgba(222, 153, 41, 0.5); /* Sombra suave con tu color */
    }

    .divSearch .lupa {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        width: 1.5em; /* Reducir el tamaño de la lupa */
        height: auto;
        cursor: pointer;
        opacity: 0.6; /* Lupa un poco más sutil */
        transition: opacity 0.2s ease-in-out;
    }

    .divSearch .lupa:hover {
        opacity: 1;

    }

    @media (max-width: 800px) {
        .divSearch {
            width: 80%;
            margin-left: -20em;
            margin-top: 5em;
        }
        .divSearch input[type=text] {
            /* Los estilos base ya están definidos */
        }
        .divSearch .lupa {
            margin-left: 93%;
            width: 1.8em; /* Ajustar tamaño en pantallas pequeñas */
        }
    }

    @media (max-width: 1300px) {
        .divSearch .lupa {
            margin-left: 90%;
        }
    }
    </style>
</head>
<body>
<header class="headerx">
        <div class="logo">CARDCAPTURE</div>
        <div class="divSearch">
            <input type="text" class="search" id="searchInput" placeholder="Buscar anuncios...">
            <img src="../img/lupa.png" alt="" class="lupa">
            <ul id="suggestions"></ul>
            <input type="hidden" id="hiddenSearchInput" name="search">
        </div >
        <div class="user-menu">
            <div class="iconx" id="userIcon">
                <img src="../img/user.png" class="user-icon" alt="">
            </div>
            <ul class="dropdown" id="dropdownMenu">
                <?php if (!$sesionIniciada): ?>
                    <li><a href="../html/InicioSesion.html">Iniciar Sesión</a></li>
                <?php else: ?>
                    <li><a href="../html/perfil.php">Perfil</a></li>
                    <li><a href="#">Me gusta</a></li>
                    <li><a href="../html/publicaciones.html">Venda</a></li>
                    <li><a href="../html/chat.php">Bústia</a></li>
                    <li><a href="../php/logout.php">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <script src="../js/scriptHeader.js"></script>

    <div class="paTras">
        <a href="../index.php" class="tornar">&#8592; Volver al Inicio</a>
    </div>

    <div class="filter-container">
        <button class="filter-toggle-btn">Mostrar Filtros</button>
    </div>
    <div class="filter-tab">
    <button class="filter-tab-btn">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="filter-icon">
    <path fill-rule="evenodd" d="M15.97 16.78a.75.75 0 01-1.06 0l-5.75-5.75a.75.75 0 011.06-1.06L15.44 15.94l5.75-5.75a.75.75 0 011.06 1.06l-5.75 5.75z" clip-rule="evenodd" />
</svg>
    </button>
</div>
    <div class="filter-panel">
        <h3>Filtrar Anuncios</h3>
        <form action="" method="GET">
            <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

            <div class="filter-group">
                <label for="fecha_filtro">Fecha de Actualización:</label>
                <select name="fecha_filtro" id="fecha_filtro">
                    <option value="" <?php if ($fechaFiltro === '') echo 'selected'; ?>>Sin filtro</option>
                    <option value="dia" <?php if ($fechaFiltro === 'dia') echo 'selected'; ?>>Último día</option>
                    <option value="semana" <?php if ($fechaFiltro === 'semana') echo 'selected'; ?>>Última semana</option>
                    <option value="mes" <?php if ($fechaFiltro === 'mes') echo 'selected'; ?>>Último mes</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="orden">Ordenar por Precio:</label>
                <select name="orden" id="orden">
                    <option value="fecha_actualizacion_desc">Sin ordenar por precio</option>
                    <option value="precio_desc" <?php if ($orderBy === 'precio_desc') echo 'selected'; ?>>Más caro primero</option>
                    <option value="precio_asc" <?php if ($orderBy === 'precio_asc') echo 'selected'; ?>>Más barato primero</option>
                </select>
                </div>

<div class="filter-group">
    <label for="price-range">Rango de Precio (0 - <?php echo number_format($maxSlider, 0, ',', '.'); ?> €):</label>
    <div class="range-slider-container">
        <div class="range-slider"></div>
        <div class="range-selected"></div>
        <div class="range-input">
            <input type="range" min="0" max="<?php echo $maxSlider; ?>" value="<?php echo $precioMin; ?>" id="min-price-slider">
            <input type="range" min="0" max="<?php echo $maxSlider; ?>" value="<?php echo $precioMax; ?>" id="max-price-slider">
        </div>
    </div>
    <div class="price-inputs">
        <input type="number" id="price-min-input" value="<?php echo $precioMin; ?>">
        <input type="number" id="price-max-input" value="<?php echo $precioMax; ?>">
    </div>
    <input type="hidden" name="precio_min" id="precio_min_hidden" value="<?php echo $precioMin; ?>">
    <input type="hidden" name="precio_max" id="precio_max_hidden" value="<?php echo $precioMax; ?>">
</div>

<button type="submit">Aplicar Filtros</button>

</form>
</div>

<main>
    <section class="anuncios">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sql_primera_imagen = "SELECT imagen FROM galeria_fotos WHERE publicacion_id = " . $row['publicacion_id'] . " LIMIT 1";
                $result_primera_imagen = $conn->query($sql_primera_imagen);
                $ruta_primera_imagen = '';
                if ($result_primera_imagen && $result_primera_imagen->num_rows > 0) {
                    $ruta_primera_imagen = $result_primera_imagen->fetch_assoc()['imagen'];
                }

                echo "<div class='anuncio'>";
                echo "<a href='detalle_publicacion.php?id=" . $row['publicacion_id'] . "' class='anuncio-link'>";
                echo "<div class='anuncio-imagen'>";
                if ($ruta_primera_imagen) {
                    echo "<img src='" . $ruta_primera_imagen . "' alt='Imagen del anuncio'>";
                } else {
                    echo "<img src='../img/placeholder.png' alt='Sin imagen'>";
                }
                echo "</div>";
                echo "<div class='anuncio-info'>";
                echo "<h3>" . $row['titulo'] . "</h3>";
                echo "<p class='precio'>" . number_format($row['precio'], 0, ',', '.') . " €</p>";
                echo "</div>";
                echo "</a>";

                // Verificar si la sesión del usuario está iniciada
                if (isset($_SESSION['user_id'])) {
                    echo "<button class='like-button' data-publicacion-id='" . $row['publicacion_id'] . "'>";
                    echo "<svg class='heart-icon' viewBox='0 0 32 29.6'>";
                    echo "<path d='M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-11.8,16-21.2C32,3.8,28.2,0,23.6,0z'/>";
                    echo "</svg>";
                    echo "</button>";
                }

                echo "</div>";
            }
        } else {
            echo "<p class='no-anuncios'>No hay anuncios disponibles con los filtros seleccionados.</p>";
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTabBtn = document.querySelector('.filter-tab-btn');
        const filterPanel = document.querySelector('.filter-panel');
        const anunciosSection = document.querySelector('.anuncios');
        const filterTab = document.querySelector('.filter-tab'); // Nueva referencia
        const rangeInput = document.querySelectorAll('.range-input input');
        const priceInput = document.querySelectorAll('.price-inputs input');
        const range = document.querySelector('.range-slider');
        const rangeSelected = document.querySelector('.range-selected');
        const precioMinHidden = document.querySelector('#precio_min_hidden');
        const precioMaxHidden = document.querySelector('#precio_max_hidden');
        let priceGap = 1;
        let isFilterOpen = false;
        const mobileBreakpoint = 768;
        const likeButtons = document.querySelectorAll('.like-button');
        const searchInput = document.getElementById('searchInput');
        const suggestionsList = document.getElementById('suggestions');
        const hiddenSearchInput = document.getElementById('hiddenSearchInput');
        let searchTimeout; // Para controlar las peticiones AJAX

        // Establecer el máximo del slider
        const maxRange = <?php echo $maxSlider; ?>;
        rangeInput.forEach(input => {
            input.max = maxRange;
        });
        priceInput[0].max = maxRange;
        priceInput[1].max = maxRange;

        // Establecer valores iniciales
        rangeInput[0].value = <?php echo $precioMin; ?>;
        rangeInput[1].value = <?php echo $precioMax; ?>;

        function updateRange() {
            const minVal = parseInt(rangeInput[0].value);
            const maxVal = parseInt(rangeInput[1].value);
            if (maxVal - minVal < priceGap) {
                if (this.className === "min-price-slider") {
                    rangeInput[0].value = maxVal - priceGap;
                } else {
                    rangeInput[1].value = minVal + priceGap;
                }
            }
            priceInput[0].value = minVal;
            priceInput[1].value = maxVal;
            precioMinHidden.value = minVal;
            precioMaxHidden.value = maxVal;

            const progressStart = (minVal / maxRange) * 100;
            const progressEnd = (maxVal / maxRange) * 100;
            rangeSelected.style.left = progressStart + "%";
            rangeSelected.style.right = (100 - progressEnd) + "%";
        }

        rangeInput.forEach(input => {
            input.addEventListener('input', updateRange);
        });

        priceInput.forEach(input => {
            input.addEventListener('input', function() {
                let minPrice = parseInt(priceInput[0].value);
                let maxPrice = parseInt(priceInput[1].value);

                if (maxPrice - minPrice < priceGap) {
                    if (this.id === "price-min-input") {
                        priceInput[0].value = maxPrice - priceGap;
                    } else {
                        priceInput[1].value = minPrice + priceGap;
                    }
                }

                rangeInput[0].value = minPrice;
                rangeInput[1].value = maxPrice;
                precioMinHidden.value = minPrice;
                precioMaxHidden.value = maxPrice;
                updateRange();
            });
        });

        updateRange(); // Inicializar el rango visual

        if (filterTabBtn && filterPanel && anunciosSection && filterTab) {
            filterTabBtn.addEventListener('click', function() {
                isFilterOpen = !isFilterOpen;
                filterPanel.classList.toggle('open');
                anunciosSection.classList.toggle('open-filter');
                filterTab.classList.toggle('open');
            });
        }

        function checkMobileView() {
            if (window.innerWidth <= mobileBreakpoint && isFilterOpen) {
                anunciosSection.classList.add('filter-open-mobile');
            } else {
                anunciosSection.classList.remove('filter-open-mobile');
            }
        }

        window.addEventListener('resize', checkMobileView);
        checkMobileView();

        likeButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                this.classList.toggle('liked');
                const publicacionId = this.dataset.publicacionId;
                const isLiked = this.classList.contains('liked');

                fetch('../php/favoritos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `publicacion_id=${publicacionId}&accion=${isLiked ? 'agregar' : 'eliminar'}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error al actualizar favoritos:', error);
                });
            });
        });

        function cargarEstadoFavoritos() {
            fetch('../php/favoritos.php?accion=obtener_favoritos')
            .then(response => response.json())
            .then(favoritos => {
                likeButtons.forEach(button => {
                    const publicacionId = button.dataset.publicacionId;
                    if (favoritos.includes(publicacionId)) {
                        button.classList.add('liked');
                    }
                });
            })
            .catch(error => {
                console.error('Error al cargar favoritos:', error);
            });
        }

        cargarEstadoFavoritos();

        // Funcionalidad de búsqueda con sugerencias INSTANTÁNEAS
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            hiddenSearchInput.value = query;

            // Limpiar el timeout anterior para evitar peticiones innecesarias
            clearTimeout(searchTimeout);

            if (query.length >= 2) {
                // Establecer un pequeño delay antes de hacer la petición
                searchTimeout = setTimeout(function() {
                    fetchSuggestions(query);
                }, 200); // 200 milisegundos de espera
            } else {
                suggestionsList.innerHTML = '';
            }
        });

        function fetchSuggestions(query) {
            fetch(`../php/buscar_sugerencias.php?categoria=<?php echo $categoria; ?>&term_inicio=${query}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(suggestion => {
                        const li = document.createElement('li');
                        li.textContent = suggestion;
                        li.addEventListener('click', function() {
                            searchInput.value = suggestion;
                            hiddenSearchInput.value = suggestion;
                            suggestionsList.innerHTML = '';
                            // Enviar el formulario al seleccionar una sugerencia
                            const form = this.closest('form');
                            if (form) {
                                form.submit();
                            } else {
                                window.location.href = `?categoria=<?php echo $categoria; ?>&search=${suggestion}`;
                            }
                        });
                        suggestionsList.appendChild(li);
                    });
                }
            })
            .catch(error => {
                console.error('Error al obtener sugerencias:', error);
            });
        }

        // Mantener el término de búsqueda al cargar la página
        const initialSearchTerm = "<?php echo $searchTerm; ?>";
        if (initialSearchTerm) {
            searchInput.value = initialSearchTerm;
            hiddenSearchInput.value = initialSearchTerm;
        }

        // Enviar el formulario al hacer clic en la lupa
        const searchButton = document.querySelector('.divSearch .lupa');
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    window.location.href = `?categoria=<?php echo $categoria; ?>&search=${searchInput.value}`;
                }
            });
        }

        // Enviar el formulario al presionar Enter en el input de búsqueda
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    window.location.href = `?categoria=<?php echo $categoria; ?>&search=${this.value}`;
                }
            }
        });
    });
</script>