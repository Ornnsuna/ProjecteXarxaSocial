<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige al login si no ha iniciado sesión
    exit();
}

$user_id = $_SESSION['user_id'];

// Conexión a la base de datos (usando tus credenciales)
require '../php/db.php';

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los datos del usuario, incluyendo la imagen de perfil
$sql = "SELECT username, nom, cognom, dataNaixement, localitzacio, descripcio, imagen_perfil FROM Usuari WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $nom = $row['nom'];
    $cognom = $row['cognom'];
    $dataNaixement = $row['dataNaixement'];
    $localitzacio = $row['localitzacio'];
    $descripcio = $row['descripcio'];
    $imagen_perfil = $row['imagen_perfil']; // Obtiene la ruta de la imagen de perfil
} else {
    echo "Usuario no encontrado";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardCapture</title>
            <link rel="shortcut icon" href="../img/logo.png" />

    <link rel="stylesheet" href="../css/cssPerfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .profile-image-container {
            width: 180px;
            height: 180px;
            border-radius: 10px;
            overflow: hidden;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <header>
        <h1>CARDCAPTURE</h1>
    </header>
    <main>
        <div class="back-link">
            <a href="../index.php">&#8592; Volver</a>
        </div>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-image-container">
                    <img src="<?php echo $imagen_perfil ? '../' . $imagen_perfil : '../img/addImage.png'; ?>" alt="Imagen de perfil" class="profile-image">
                </div>
                <h1 class="profile-name"><?php echo $username; ?></h1>
                <p class="profile-fullname"><?php echo $nom . " " . $cognom; ?></p>
            </div>
            <div class="profile-details">
                <div class="detail-section">
                    <h2 class="detail-title">Fecha de Nacimiento</h2>
                    <p class="detail-text"><?php echo $dataNaixement; ?></p>
                </div>
                <div class="detail-section">
                    <h2 class="detail-title">Localización</h2>
                    <p class="detail-text"><?php echo $localitzacio; ?></p>
                </div>
                <div class="detail-section">
                    <h2 class="detail-title">Sobre Mí</h2>
                    <p class="detail-text description"><?php echo $descripcio; ?></p>
                </div>
            </div>
            <div class="edit-profile-link">
                <a href="./editar_perfil.php">Editar perfil ✎</a>
            </div>
        </div>
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
    <div class="card-animation"></div>
    <script src="./../js/script2.js"></script>
</body>
</html>