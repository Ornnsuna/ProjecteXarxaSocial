<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

// Incluir el archivo de conexión a la base de datos
include '../php/db.php';

// Verificar si el ID de la publicación está definido
if (isset($_GET['id'])) {
    $publicacion_id = $_GET['id'];
} else {
    echo "Error: ID de publicación no especificado.";
    exit;
}

// Consulta SQL corregida para usar id_user y obtener id_publicacion
$sql = "SELECT p.*, u.username AS nombre_vendedor, u.id_user AS vendedor_id
        FROM publicacions p
        JOIN usuari u ON p.usuario_id = u.id_user
        WHERE p.publicacion_id = $publicacion_id"; // Cambio clave: p.id_publicacion

$result = $conn->query($sql);

if (!$result) {
    // Depuración: Imprime el mensaje de error de la consulta SQL
    echo "Error en la consulta: " . $conn->error . "<br>";
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $vendedor_id = $row['vendedor_id'];
} else {
    echo "Publicación no encontrada.";
    exit;
}

// Crear un nuevo chat si se hace clic en el botón de chat
if (isset($_GET['crear_chat'])) {
    $usuario_id = $_SESSION['user_id'];

    // Verificar si ya existe un chat entre los dos usuarios para esta publicación
    $sql_verificar_chat = "SELECT chat_id FROM chats 
                           WHERE (usuario1_id = $usuario_id AND usuario2_id = $vendedor_id AND id_publicacion = $publicacion_id) 
                           OR (usuario1_id = $vendedor_id AND usuario2_id = $usuario_id AND id_publicacion = $publicacion_id)";
    $result_verificar_chat = $conn->query($sql_verificar_chat);

    if ($result_verificar_chat->num_rows > 0) {
        $row_chat = $result_verificar_chat->fetch_assoc();
        $chat_id = $row_chat['chat_id'];
        header("Location: chat.php?chat_id=$chat_id");
        exit;
    } else {
        // Crear un nuevo chat si no existe
        $sql_crear_chat = "INSERT INTO chats (usuario1_id, usuario2_id, id_publicacion) 
                           VALUES ($usuario_id, $vendedor_id, $publicacion_id)"; // Cambio clave: Se añade id_publicacion
        if ($conn->query($sql_crear_chat)) {
            $chat_id = $conn->insert_id;
            header("Location: chat.php?chat_id=$chat_id");
            exit;
        } else {
            echo "Error al crear el chat: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Publicación</title>
    <link rel="stylesheet" href="../css/detalle_publicacion.css">
</head>
<body>
    <div class="detalle-publicacion">
        <a href="javascript:history.back()" class="volver-button">Volver</a>
        <img src="<?php echo $row['imagen']; ?>" alt="Imagen de la publicación">
        <h2><?php echo $row['titulo']; ?></h2>
        <p><strong>Descripción:</strong> <?php echo $row['descripcion']; ?></p>
        <p><strong>Precio:</strong> <?php echo $row['precio']; ?>€</p>
        <p><strong>Ubicación:</strong> <?php echo $row['ubicacion']; ?></p>
        <p><strong>Estado:</strong> <?php echo $row['estado']; ?></p>
        <p><strong>Vendedor:</strong> <?php echo $row['nombre_vendedor']; ?></p>
        <?php if ($sesionIniciada): ?>
            <a href="?id=<?php echo $publicacion_id; ?>&crear_chat=1" class="chat-button">Chat</a>
        <?php else: ?>
            <a href="InicioSesion.html" class="chat-button">Iniciar Sesión para Chatear</a>
        <?php endif; ?>
    </div>
    <script src="../js/detalle_publicacion.js"></script>
    <div class="card-animation"></div>
    <script src="../js/script2.js"></script>
</body>
</html>

<?php $conn->close(); ?>
