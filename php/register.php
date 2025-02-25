<?php
session_start(); 
require 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); 

    $checkSql = "SELECT mail FROM Usuari WHERE mail = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
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

    $sql = "INSERT INTO Usuari (mail, username, passHash, userFirstName, userLastName, creationDate) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $email, $username, $password, $firstName, $lastName);

    if ($stmt->execute()) {
        $_SESSION["username"] = $username;
        header("Location: ../html/InicioSesion.html"); 
        
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
