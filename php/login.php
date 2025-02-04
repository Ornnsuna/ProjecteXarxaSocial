<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Buscar usuario en la base de datos
    $sql = "SELECT id_user, passHash, userFirstName, userLastName FROM Usuari WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["passHash"])) {
        $_SESSION["user_id"] = $user["id_user"];
        $_SESSION["username"] = $username;
        $_SESSION["full_name"] = $user["userFirstName"] . " " . $user["userLastName"];

        echo "Inicio de sesión exitoso. <a href='dashboard.php'>Ir al panel</a>";
    } else {
        echo "Error: Usuario o contraseña incorrectos.";
    }
}
?>
