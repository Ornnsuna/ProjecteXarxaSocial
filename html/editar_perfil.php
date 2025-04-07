<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
require_once('../php/funciones_perfil.php');

$datosUsuario = obtenerDatosUsuario($user_id);
if (!$datosUsuario) {
    echo "Usuario no encontrado";
    exit();
}
$imagen_perfil_actual = $datosUsuario['imagen_perfil'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cambiar_foto'])) {
    $resultado = actualizarFotoPerfil($user_id, $_FILES['nueva_imagen']);
    if ($resultado === true) {
        echo "<script>alert('Foto de perfil actualizada con éxito'); window.location.href='editar_perfil.php';</script>";
        exit();
    } else {
        echo "Error al actualizar la foto de perfil: " . $resultado;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_cambios'])) {
    $resultado = actualizarDatosUsuario(
        $user_id,
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['fecha_nacimiento'],
        $_POST['localizacion'],
        $_POST['descripcion']
    );

    if ($resultado === true) {
        echo "<script>alert('Perfil actualizado con éxito'); window.location.href='perfil.php';</script>";
        exit();
    } else {
        echo "Error al actualizar el perfil: " . $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - CardCapture</title>
    <link rel="stylesheet" href="../css/cssEditarPerfil.css">
    <style>
        .profile-image-container {
            text-align: center;
            position: relative;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.1);
        }

        .upload-buttons {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }

        .upload-buttons label, .upload-buttons button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .upload-buttons label {
            background-color: #f0f0f0;
            color: #333;
        }

        .upload-buttons button {
            background-color: #007bff;
            color: white;
        }

        #nueva_imagen {
            display: none;
        }

        .image-changed {
            animation: pixelateImage 0.5s ease;
        }

        @keyframes pixelateImage {
            0% { filter: blur(20px); opacity: 0; }
            50% { filter: blur(0px); opacity: 1; }
            100% { filter: blur(0px); opacity: 1; }
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.querySelector('.profile-image').src = e.target.result;
                    document.querySelector('.profile-image').classList.add('image-changed');
                    setTimeout(function() {
                        document.querySelector('.profile-image').classList.remove('image-changed');
                    }, 500);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>CARDCAPTURE</h1>
    </header>
    <main>
        <div class="edit-profile-container">
            <h1 class="edit-profile-title">Editar Perfil</h1>
            <div class="profile-image-container">
                <img src="<?php echo $imagen_perfil_actual ? '../' . $imagen_perfil_actual : '../img/addImage.png'; ?>" alt="Foto de perfil" class="profile-image">
                <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="nueva_imagen" id="nueva_imagen" accept="image/*" onchange="previewImage(this)" style="display: none;">
                    <div class="upload-buttons">
                        <label for="nueva_imagen">Seleccionar Archivo</label>
                        <button type="submit" name="cambiar_foto">Cambiar Imagen</button>
                    </div>
                </form>
            </div>
            <form action="editar_perfil.php" method="post" class="edit-profile-form">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosUsuario['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($datosUsuario['cognom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($datosUsuario['dataNaixement']); ?>" >
                </div>
                <div class="form-group">
                    <label for="localizacion">Localización:</label>
                    <input type="text" id="localizacion" name="localizacion" value="<?php echo htmlspecialchars($datosUsuario['localitzacio']); ?>" >
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($datosUsuario['descripcio']); ?></textarea>
                </div>
                <button type="submit" class="save-button" name="guardar_cambios">Guardar Cambios</button>
            </form>
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
</body>
</html>