<?php 
session_start(); 
require_once 'config.php';
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
                include 'inicio.php';
            }
        } else {
            include 'login.php';
        }
        ?>
    </body>
</html>