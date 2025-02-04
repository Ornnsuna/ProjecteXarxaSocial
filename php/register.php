<?php
require 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Encriptar contraseña

    // Insertar usuario en la base de datos
    $sql = "INSERT INTO Usuari (mail, username, passHash, userFirstName, userLastName, creationDate) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $email, $username, $password, $firstName, $lastName);

    if ($stmt->execute()) {
        header("Location: ../sesion/InicioSesion.html");
        exit();
    }     
    else {
        echo "<script>alert('⚠️ Error de registro: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
