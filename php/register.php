<?php
session_start();
require 'db.php'; // Conexión a la base de datos
require 'mailConfig.php'; // Configuración de PHPMailer para enviar correos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(32)); // Generar un token único

    // Verificar si el correo ya está en uso en ambas tablas
    $checkSql = "SELECT mail FROM Usuari WHERE mail = ? UNION SELECT mail FROM UsuariPendent WHERE mail = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $email, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>
                alert('⚠️ Este correo ya está registrado. Intenta con otro.');
                window.location.href = '../html/Registro.html';
              </script>";
        exit();
    }
    $checkStmt->close();

    // Insertar el usuario en la tabla temporal antes de confirmar
    $sql = "INSERT INTO UsuariPendent (mail, username, passHash, nom, cognom, token, dataCreacio, dataNaixement, localitzacio, descripcio) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NULL, NULL, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $email, $username, $password, $firstName, $lastName, $token);

    if ($stmt->execute()) {
        // Enviar correo de confirmación
        $verificationLink = "localhost/CardCapture/ProjecteXarxaSocial/php/verify.php?token=$token";
        $subject = "Verifica tu cuenta en CardCapture";
        $body = "Hola $firstName,<br><br>Haz clic en el siguiente enlace para confirmar tu cuenta: 
                <a href='$verificationLink'>$verificationLink</a><br><br>Si no creaste esta cuenta, ignora este mensaje.";

        if (sendVerificationEmail($email, $subject, $body)) {
            echo "<script>
                    alert('📩 Se ha enviado un correo de verificación. Revisa tu bandeja de entrada.');
                    window.location.href = '../html/InicioSesion.html';
                  </script>";
        } else {
            echo "<script>
                    alert('⚠️ Error al enviar el correo de verificación.');
                    window.location.href = '../html/Registro.html';
                  </script>";
        }
        exit();
    } else {
        echo "<script>
                alert('⚠️ Error de registro: " . $stmt->error . "');
                window.location.href = '../html/Registro.html';
              </script>";
    }
    $stmt->close();
}
?>
