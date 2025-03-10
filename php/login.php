<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $sql = "SELECT id_user, passHash, nom, cognom FROM Usuari WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["passHash"])) {
        $_SESSION["user_id"] = $user["id_user"];
        $_SESSION["username"] = $username;
        $_SESSION["full_name"] = $user["nom"] . " " . $user["cognom"];

        header("Location: ../html/inicio.html");
        exit();
    } else {
        echo "<script>
                alert('Error: Usuario o contraseña incorrectos.');
                window.location.href = '../html/InicioSesion.html';
              </script>";
        exit(); 
    }
}
?>