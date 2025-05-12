<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "Debes iniciar sesión para subir una publicación.";
    exit;
}

// Incluir el archivo de conexión a la base de datos
include 'db.php';

// Definir el límite máximo de imágenes
$max_images = 12;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];
    $ubicacion = $_POST["ubicacion"];
    $estado = $_POST["estado"];
    $usuario_id = $_SESSION['user_id']; // Obtener el ID del usuario de la sesión

    // Iniciar transacción para asegurar la integridad de los datos
    $conn->begin_transaction();
    $uploadOk = 1;
    $error_message = "";

    // Guardar la publicación en la tabla 'publicacions'
    $sql_publicacion = "INSERT INTO publicacions (usuario_id, titulo, descripcion, categoria, precio, ubicacion, estado, fecha_creacion, fecha_actualizacion) VALUES ($usuario_id, '$titulo', '$descripcion', '$categoria', $precio, '$ubicacion', '$estado', NOW(), NOW())";

    if ($conn->query($sql_publicacion) === TRUE) {
        $publicacion_id = $conn->insert_id; // Obtener el ID de la publicación recién insertada

        // Subir y guardar las imágenes en la tabla 'galeria_fotos'
        $target_dir = "../uploads/";

        // Asegurarse de que el directorio uploads exista
        if (!file_exists($target_dir) && !is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Crear el directorio si no existe
        }

        if (isset($_FILES["imagenes"]) && is_array($_FILES["imagenes"]["name"])) {
            $total_images = count($_FILES["imagenes"]["name"]);

            if ($total_images > $max_images) {
                $error_message = "Se ha excedido el límite de " . $max_images . " imágenes.";
                $uploadOk = 0;
            } else {
                for ($i = 0; $i < $total_images; $i++) {
                    $file_name = basename($_FILES["imagenes"]["name"][$i]);
                    $target_file = $target_dir . $file_name;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    // Verificar si el archivo es una imagen real
                    $check = getimagesize($_FILES["imagenes"]["tmp_name"][$i]);
                    if ($check === false) {
                        $error_message = "Uno de los archivos no es una imagen.";
                        $uploadOk = 0;
                        break;
                    }

                    // Verificar el tamaño del archivo
                    if ($_FILES["imagenes"]["size"][$i] > 500000) {
                        $error_message = "Uno de los archivos es demasiado grande.";
                        $uploadOk = 0;
                        break;
                    }

                    // Permitir ciertos formatos de archivo
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                        $error_message = "Solo se permiten archivos JPG, JPEG, PNG y GIF para las imágenes.";
                        $uploadOk = 0;
                        break;
                    }

                    // Si no hay errores, intentar subir el archivo
                    if ($uploadOk) {
                        if (move_uploaded_file($_FILES["imagenes"]["tmp_name"][$i], $target_file)) {
                            // Guardar la ruta de la imagen en la tabla 'galeria_fotos'
                            $sql_galeria = "INSERT INTO galeria_fotos (publicacion_id, imagen) VALUES ($publicacion_id, '$target_file')";
                            if ($conn->query($sql_galeria) !== TRUE) {
                                $error_message = "Error al guardar la ruta de la imagen en la galería: " . $conn->error;
                                $uploadOk = 0;
                                break;
                            }
                        } else {
                            $error_message = "Error al subir uno de los archivos.";
                            $uploadOk = 0;
                            break;
                        }
                    }
                }
            }
        } else {
            $error_message = "No se seleccionaron imágenes.";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            $conn->commit();
            echo "<script>
                    alert('Publicación subida con éxito.');
                    window.location.href = '../index.php'; // Redirige al index
                  </script>";
            exit();
        } else {
            $conn->rollback();
            echo "<script>
                    alert('Error al subir la publicación: " . $error_message . "');
                    window.location.href = 'publicaciones.html'; // Redirige al formulario
                  </script>";
        }

    } else {
        $conn->rollback();
        echo "<script>
                alert('Error al guardar la publicación principal: " . $conn->error . "');
                window.location.href = 'publicaciones.html'; // Redirige al formulario
              </script>";
    }
}

$conn->close();
?>