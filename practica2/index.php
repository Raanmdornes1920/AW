<?php 
session_start(); 
require_once 'config.php'; // El archivo inteligente de arriba
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Bistro FDI</title>
    </head>
    <body>
        <?php
        if(isset($_SESSION['login']) && $_SESSION['login']){
            // Aquí ya usamos la conexión que se configuró arriba
            $nombre_sesion = $_SESSION['nombre'];
            $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = '$nombre_sesion'";
            $resultado = mysqli_query($db_connection, $sql);
            
            if ($fila = mysqli_fetch_assoc($resultado)) {
                echo '<h1>Bienvenido ' . htmlspecialchars($fila['nombre_usuario']) . '!</h1>';
                echo '<button onclick="window.location.href=\'logout.php\'">Cerrar Sesión</button>';
            }
        } else {
            echo '<h1>Bienvenido invitado!</h1><br>';
            echo '<button onclick="window.location.href=\'login.php\'">Iniciar Sesión</button>';
        }
        ?>
    </body>
</html>