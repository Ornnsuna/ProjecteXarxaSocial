<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    
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

        header("Location: ../html/index.html");
        exit();
    
    } else {

        echo "<script>
                alert('Error: Usuario o contraseña incorrectos.');
                window.location.href = '../sesion/InicioSesion.html';
              </script>";
        exit(); 
    }
    
}
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
    </head>
    <body>
        echo "hola";
        
        <script src="" async defer></script>
    </body>
</html>