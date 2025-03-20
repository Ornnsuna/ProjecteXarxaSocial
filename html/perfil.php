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

// Consulta para obtener los datos del usuario
$sql = "SELECT username, nom, cognom, dataNaixement, localitzacio, descripcio FROM Usuari WHERE id_user = ?";
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
    <link rel="stylesheet" href="../css/cssPerfil.css">
</head>
<body>
    <header>
        <h1>CARDCAPTURE</h1>
    </header>
    <main>
        <a href="./inicio.html" class="tornar">&#8592; Volver a la página principal</a>
        <div class="perfil">
            <div class="coses">
                <h1 class="titol"><?php echo $username; ?></h1>
                <p class="text"><?php echo $nom . " " . $cognom; ?></p>
                <p class="text"><?php echo $dataNaixement; ?></p>
                <p class="text"><?php echo $localitzacio; ?></p>
                <p class="text">Sobre Mi</p>
                <textarea readonly><?php echo $descripcio; ?></textarea>
            </div>
        </div>
        <a href="#" class="edit">Editar perfil ✎</a>
    </main>
    <script src="../js/perfil.js"></script>
    <footer id="footer" class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h2 id="footerTitle">CardCapture</h2>
                <p>Explora, compra y vende cartas de colección fácilmente.</p>
            </div> 
            <div class="footer-social">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="https://www.facebook.com/" target="_blank"><img class="icon" src="../img/facebook.png" alt="Facebook" ></a>
                    <a href="https://x.com/home?lang=es" target="_blank"><img class="icon" src="../img/twitter.png" alt="Twitter"></a>
                    <a href="https://www.instagram.com/" target="_blank"><img class="icon" src="../img/instagram.png" alt="Instagram"></a>
                </div>
            </div>
        </div>
    
        <div class="footer-bottom">
            <p id="footerText">&copy; 2025 CardCapture. Todos los derechos reservados.</p>
        </div>
    
        <canvas id="footerCanvas"></canvas> <!-- Fondo Animado -->
    
        <script src="../js/footerAnimation.js"></script>
    </footer>
    <div class="card-animation"></div>
    <script src="./../js/script2.js"></script>
</body>
</html>