<?php
$host = "localhost"; // Servidor MySQL
$user = "root"; // Usuario de phpMyAdmin
$password = ""; // Contraseña (deja vacío si no tienes contraseña)
$database = "cardCapture"; // Nombre de la base de datos

// Conexión a MySQL con MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
