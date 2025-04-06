<?php
session_start();
$sesionIniciada = isset($_SESSION['user_id']);

// Incluir el archivo de conexión a la base de datos
include '../php/db.php';

// Verificar si el usuario ha iniciado sesión
if (!$sesionIniciada) {
    header('Location: InicioSesion.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];

// Obtener el chat_id de la URL
$chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : null;

if ($chat_id) {
    // Obtener la información del chat
    $sql_chat = "SELECT c.usuario1_id, c.usuario2_id, p.imagen AS imagen_publicacion, p.titulo AS titulo_publicacion,
                       u1.username AS usuario1_nombre, u1.imagen_perfil AS usuario1_imagen,
                       u2.username AS usuario2_nombre, u2.imagen_perfil AS usuario2_imagen
                FROM chats c
                JOIN publicacions p ON c.id_publicacion = p.publicacion_id
                JOIN usuari u1 ON c.usuario1_id = u1.id_user
                JOIN usuari u2 ON c.usuario2_id = u2.id_user
                WHERE c.chat_id = $chat_id";
    $result_chat = $conn->query($sql_chat);

    if ($result_chat && $result_chat->num_rows > 0) {
        $chat = $result_chat->fetch_assoc();

        // Determinar el usuario con el que se está hablando
        $otro_usuario_id = ($chat['usuario1_id'] == $usuario_id) ? $chat['usuario2_id'] : $chat['usuario1_id'];
        $otro_usuario_nombre = ($chat['usuario1_id'] == $usuario_id) ? $chat['usuario2_nombre'] : $chat['usuario1_nombre'];
        // Construir la ruta completa de la imagen del otro usuario
        $otro_usuario_imagen = ($chat['usuario1_id'] == $usuario_id) ? '../' . $chat['usuario2_imagen'] : '../' . $chat['usuario1_imagen'];

        // Obtener los mensajes del chat
        $mensajes = [];
        $sql_mensajes = "SELECT m.*, u.username, u.imagen_perfil AS emisor_imagen_perfil
                        FROM mensajes m
                        JOIN usuari u ON m.usuario_id = u.id_user
                        WHERE m.chat_id = $chat_id
                        ORDER BY m.fecha_envio ASC";
        $result_mensajes = $conn->query($sql_mensajes);
        if ($result_mensajes) {
            while ($row_mensaje = $result_mensajes->fetch_assoc()) {
                // Construir la ruta completa de la imagen del emisor del mensaje
                $row_mensaje['emisor_imagen_perfil'] = '../' . $row_mensaje['emisor_imagen_perfil'];
                $mensajes[] = $row_mensaje;
            }
        }
    } else {
        // Si no se encuentra el chat, establecer variables a null o mostrar un mensaje de error
        $chat = null;
        $mensajes = [];
        echo "Chat no encontrado.";
    }
} else {
    // Código para mostrar la lista de chats del usuario
    $sql_chats = "SELECT c.chat_id, u.username, u.imagen_perfil, p.titulo AS titulo_publicacion, u2.username AS otro_usuario_nombre, u2.imagen_perfil AS otro_usuario_imagen, c.usuario1_id, c.usuario2_id
                  FROM chats c
                  JOIN usuari u ON (c.usuario1_id = u.id_user OR c.usuario2_id = u.id_user)
                  JOIN usuari u2 ON (c.usuario1_id = u2.id_user OR c.usuario2_id = u2.id_user) AND u2.id_user != $usuario_id
                  JOIN publicacions p ON c.id_publicacion = p.publicacion_id
                  WHERE (c.usuario1_id = $usuario_id OR c.usuario2_id = $usuario_id)
                  GROUP BY c.chat_id";

    $result_chats = $conn->query($sql_chats);
    $chats = [];
    if ($result_chats) {
        while ($row_chat = $result_chats->fetch_assoc()) {
            // Determinar el ID del otro usuario para obtener su imagen de perfil
            $otro_usuario_id_chat = ($row_chat['usuario1_id'] == $usuario_id) ? $row_chat['usuario2_id'] : $row_chat['usuario1_id'];
            // Obtener la imagen de perfil del otro usuario
            $sql_otro_usuario_imagen = "SELECT imagen_perfil FROM usuari WHERE id_user = $otro_usuario_id_chat";
            $result_otro_usuario_imagen = $conn->query($sql_otro_usuario_imagen);
            if ($result_otro_usuario_imagen && $result_otro_usuario_imagen->num_rows > 0) {
                $row_otro_usuario_imagen = $result_otro_usuario_imagen->fetch_assoc();
                $row_chat['otro_usuario_imagen'] = '../' . $row_otro_usuario_imagen['imagen_perfil'];
            } else {
                $row_chat['otro_usuario_imagen'] = ''; // O una imagen por defecto
            }

            // Aseguramos que el 'otro_usuario_nombre' sea del otro usuario.
            $row_chat['otro_usuario_nombre'] = ($row_chat['username'] == $_SESSION['username']) ? $row_chat['otro_usuario_nombre'] : $row_chat['username'];
            $chats[] = $row_chat;
        }
    } else {
        $chats = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #DE9929;
            color: #1f2937;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .chat-container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin: 20px;
            display: flex;
            flex-direction: column;
            height: auto;
            min-height: calc(100vh - 40px);
        }

        .usuarios {
            background-color: #1a1a1a;
            color: #DE9929;
            padding: 16px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .usuarios .usuario {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .usuarios img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .usuarios .usuario-info {
            display: flex;
            flex-direction: column;
        }

        .usuarios .usuario-info h3 {
            margin: 0 0 4px 0;
            font-size: 1.2em;
            font-weight: 600;
        }

        .usuarios .usuario-info p {
            margin: 0;
            font-size: 0.9em;
            color: #DE9929;
        }

        .mensajes {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .mensaje {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
            width: fit-content;
            max-width: 80%;
        }

        .mensaje-propio {
            align-self: flex-end;
            align-items: flex-end;
        }

        .mensaje-contenido {
            background-color: #dcf8c6;
            color: #1a1a1a;
            padding: 12px 16px;
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        .mensaje-propio .mensaje-contenido {
            background-color: #e0e0e0;
            color: #1a1a1a;
        }

        .mensaje-fecha {
            font-size: 0.75em;
            color: #a3a3a3;
            margin-top: 4px;
        }

        .mensaje-leido {
            font-size: 0.75em;
            color: #a3a3a3;
            align-self: flex-end;
            margin-top: 4px;
        }

        .enviar-mensaje {
            padding: 16px;
            display: flex;
            align-items: center;
            border-top: 1px solid #e5e7eb;
        }

        .enviar-mensaje input {
            flex: 1;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 24px;
            margin-right: 12px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .enviar-mensaje input:focus {
            border-color: #DE9929;
            box-shadow: 0 0 0 2px rgba(222, 153, 41, 0.2);
        }

        .enviar-mensaje button {
            padding: 12px 24px;
            background-color: #DE9929;
            color: #fff;
            border: none;
            border-radius: 24px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .enviar-mensaje button:hover {
            background-color: #c87815;
        }

        .publicacion-info {
            padding: 16px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .publicacion-info img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .publicacion-info p {
            font-size: 0.9em;
            color: #4b5563;
        }

        .chat-list {
            margin-top: 20px;
            padding: 0 16px;
            overflow-y: auto;
            flex: 1;
        }

        .chat-list h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
            font-weight: 600;
            color: #1e293b;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-decoration: none;
            color: #1f2937;
            margin-bottom: 0;
            transition: background-color 0.3s ease;
            border-radius: 12px;
            justify-content: space-between;
            width: 100%;
            box-sizing: border-box;
        }

        .chat-item:hover {
            background-color: #f9fafb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .chat-item img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .chat-item-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .chat-item-info h3 {
            margin: 0 0 4px 0;
            font-size: 1.1em;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-info p {
            margin: 0;
            font-size: 0.9em;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-last-message {
            font-size: 0.8em;
            color: #9ca3af;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: right;
            margin-left: auto;
            min-width: 80px;
        }

        .chat-item-content {
            display: flex;
            align-items: center;
            width: calc(100% - 120px);
            min-width: calc(100% - 120px);
            justify-content: flex-start;
        }
        .paTras{
        margin: 2em;
        }
        .tornar {
        text-decoration: none;
        color: black;
        font-size: 1.2em;
        margin-bottom: 0.5em;
        margin-left: 10%;
        }

        @media (max-width: 640px) {
            .chat-container {
                margin: 10px;
                border-radius: 8px;
                height: auto;
                max-height: 100%;
            }

            .usuarios {
                padding: 12px;
            }

            .mensajes {
                padding: 10px;
            }

            .enviar-mensaje {
                padding: 12px;
            }

            .chat-list {
                padding: 0 12px;
            }

            .chat-item-content {
                width: calc(100% - 100px);
                min-width: calc(100% - 100px);
            }

            .chat-item-last-message{
                min-width: 60px;
                text-align: right;
            }
        }
    </style>
</head>
<body>
<div class="paTras">
        <a href="../index.php" class="tornar">&#8592; Volver al Inicio</a>
    </div>
    <div class="chat-container">
        <?php if ($chat_id && $chat != null): ?>
            <div class="usuarios">
                <div class="usuario">
                    <img src="<?php echo $otro_usuario_imagen; ?>" alt="Imagen de perfil de <?php echo $otro_usuario_nombre; ?>">
                    <div class="usuario-info">
                        <h3><?php echo $otro_usuario_nombre; ?></h3>
                        <p><?php echo $chat['titulo_publicacion']; ?></p>
                    </div>
                </div>
            </div>
            <div class="mensajes">
                <?php if (isset($mensajes) && is_array($mensajes)): ?>
                    <?php foreach ($mensajes as $mensaje): ?>
                        <div class="mensaje <?php echo ($mensaje['usuario_id'] == $usuario_id) ? 'mensaje-propio' : 'mensaje-ajeno'; ?>">
                            <div class="mensaje-contenido">
                                <p><?php echo $mensaje['contenido']; ?></p>
                            </div>
                            <span class="mensaje-fecha"><?php echo date('H:i', strtotime($mensaje['fecha_envio'])); ?></span>
                            <?php if ($mensaje['leido'] == 1): ?>
                                <span class="mensaje-leido">Leído</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay mensajes en este chat.</p>
                <?php endif; ?>
                <div class="enviar-mensaje">
                    <input type="text" id="mensaje-input" placeholder="Escribe un mensaje...">
                    <button id="enviar-btn">Enviar</button>
                </div>
            </div>
            <div class="publicacion-info">
                <img src="<?php echo $chat['imagen_publicacion']; ?>" alt="Imagen de la publicación">
                <p><?php echo $chat['titulo_publicacion']; ?></p>
            </div>
        <?php else: ?>
            <div class="chat-list">
                <h2>Tus Chats</h2>
                <?php if (isset($chats) && is_array($chats) && count($chats) > 0): ?>
                    <?php foreach ($chats as $chat): ?>
                        <a href="?chat_id=<?php echo $chat['chat_id']; ?>" class="chat-item">
                            <div class="chat-item-content">
                                <img src="<?php echo $chat['otro_usuario_imagen']; ?>" alt="Imagen de perfil de <?php echo $chat['otro_usuario_nombre']; ?>">
                                <div class="chat-item-info">
                                    <h3><?php echo $chat['otro_usuario_nombre']; ?></h3>
                                    <p><?php echo $chat['titulo_publicacion']; ?></p>
                                </div>
                            </div>
                            <span class="chat-item-last-message">
                                Último mensaje
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes chats activos.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // ... (código JavaScript para enviar mensajes)
        document.getElementById('enviar-btn').addEventListener('click', function() {
            var mensaje = document.getElementById('mensaje-input').value;
            var chat_id = <?php echo $chat_id; ?>;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../php/enviar_mensaje.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // El mensaje se envió correctamente
                    location.reload(); // Recargar la página para mostrar el nuevo mensaje
                } else {
                    console.error('Error al enviar el mensaje:', xhr.status, xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error de red al enviar el mensaje');
            };
            xhr.send('chat_id=' + encodeURIComponent(chat_id) + '&mensaje=' + encodeURIComponent(mensaje));
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
