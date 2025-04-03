<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

// Incluir el archivo de conexión a la base de datos
include '../php/db.php';

// Obtener el ID de la publicación de la URL
$publicacion_id = $_GET['id'];

// Depuración: Verifica si $publicacion_id está definido
if (empty($publicacion_id)) {
    echo "Error: ID de publicación no especificado.";
    exit;
}

// Consulta SQL para obtener los detalles de la publicación
$sql = "SELECT p.*, u.username AS nombre_vendedor FROM publicacions p JOIN usuarios u ON p.usuario_id = u.id_user WHERE p.id = $publicacion_id";

// Depuración: Imprime la consulta SQL
echo "Consulta SQL: " . $sql . "<br>";

$result = $conn->query($sql);

// Depuración: Imprime el número de filas encontradas
echo "Número de filas encontradas: " . $result->num_rows . "<br>";

if (!$result) {
    // Depuración: Imprime el mensaje de error de la consulta SQL
    echo "Error en la consulta: " . $conn->error . "<br>";
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Depuración: Imprime el contenido de $row
    print_r($row);
} else {
    echo "Publicación no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Publicación</title>
    <link rel="stylesheet" href="../css/css.css">
    <link rel="stylesheet" href="../css/PAGINIcssHeaderFooter.css">
    <style>
        /* ... (tus estilos CSS) ... */
    </style>
</head>
<body>
    <header id="header">
        </header>
    <div class="detalle-publicacion">
        <img src="<?php echo $row['imagen']; ?>" alt="Imagen de la publicación">
        <h2><?php echo $row['titulo']; ?></h2>
        <p><strong>Descripción:</strong> <?php echo $row['descripcion']; ?></p>
        <p><strong>Precio:</strong> <?php echo $row['precio']; ?>€</p>
        <p><strong>Ubicación:</strong> <?php echo $row['ubicacion']; ?></p>
        <p><strong>Estado:</strong> <?php echo $row['estado']; ?></p>
        <p><strong>Vendedor:</strong> <?php echo $row['nombre_vendedor']; ?></p>
        <button class="chat-button">Chat</button>
    </div>
    <footer id="footer" class="footer">
        </footer>
</body>
</html>

<?php
$conn->close();
?>