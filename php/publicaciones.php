<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "Debes iniciar sesión para subir una publicación.";
    exit;
}

// Incluir el archivo de conexión a la base de datos
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];
    $ubicacion = $_POST["ubicacion"];
    $estado = $_POST["estado"];
    $usuario_id = $_SESSION['user_id']; // Obtener el ID del usuario de la sesión

    // Subir imagen
    $target_dir = "../uploads/";
    
    // Asegurarse de que el directorio uploads exista
    if (!file_exists($target_dir) && !is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Crear el directorio si no existe
    }
    
    $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES["imagen"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>
                alert('El archivo no es una imagen.');
                window.location.href = 'publicaciones.html'; // Redirige al formulario
              </script>";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo
    if ($_FILES["imagen"]["size"] > 500000) {
        echo "<script>
                alert('El archivo es demasiado grande.');
                window.location.href = 'publicaciones.html'; // Redirige al formulario
              </script>";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>
                alert('Solo se permiten archivos JPG, JPEG, PNG y GIF.');
                window.location.href = 'publicaciones.html'; // Redirige al formulario
              </script>";
        $uploadOk = 0;
    }

    // Verificar si $uploadOk está establecido en 0 por un error
    if ($uploadOk == 0) {
        echo "<script>
                alert('Tu archivo no fue subido.');
                window.location.href = 'publicaciones.html'; // Redirige al formulario
              </script>";
    // Si todo está bien, intenta subir el archivo
    } else {
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            // Guardar datos en la base de datos
            $sql = "INSERT INTO publicacions (usuario_id, titulo, descripcion, categoria, precio, ubicacion, estado, imagen) VALUES ($usuario_id, '$titulo', '$descripcion', '$categoria', $precio, '$ubicacion', '$estado', '$target_file')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>
                        alert('Publicación subida con éxito.');
                        window.location.href = '../index.php'; // Redirige al index
                      </script>";
                exit(); // Importante: detener la ejecución del script después de la redirección
            } else {
                echo "<script>
                        alert('Error al subir la publicación: " . $conn->error . "');
                        window.location.href = 'publicaciones.html'; // Redirige al formulario
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Hubo un error al subir tu archivo.');
                    window.location.href = 'publicaciones.html'; // Redirige al formulario
                  </script>";
        }
    }
}

$conn->close();
?>
