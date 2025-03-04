<?php
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar usuario en la tabla temporal
    $sql = "SELECT * FROM UsuariosPendientes WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Insertar en la tabla final `Usuari`
        $sqlInsert = "INSERT INTO Usuari (mail, username, passHash, userFirstName, userLastName, creationDate) 
                      VALUES (?, ?, ?, ?, ?, NOW())";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("sssss", $user['mail'], $user['username'], $user['passHash'], $user['userFirstName'], $user['userLastName']);
        
        if ($stmtInsert->execute()) {
            // Eliminar usuario de `UsuariosPendientes`
            $sqlDelete = "DELETE FROM UsuariosPendientes WHERE token = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param("s", $token);
            $stmtDelete->execute();

            echo "<script>
                    alert('✅ Cuenta verificada correctamente. Ahora puedes iniciar sesión.');
                    window.location.href = '../html/InicioSesion.html';
                  </script>";
        }
    } else {
        echo "<script>
                alert('❌ Token inválido o ya utilizado.');
                window.location.href = '../html/Registro.html';
              </script>";
    }
}
?>
