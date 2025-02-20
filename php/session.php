<?php
session_start();
header('Content-Type: application/json');

// Si la sesión "usuario" está definida, el usuario está autenticado
if (isset($_SESSION['usuario'])) {
    echo json_encode(["isLoggedIn" => true, "usuario" => $_SESSION['usuario']]);
} else {
    echo json_encode(["isLoggedIn" => false]);
}
?>