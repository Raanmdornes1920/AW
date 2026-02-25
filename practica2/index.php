<?php 
session_start(); 
require_once 'static/config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Bistro FDI</title>
    </head>
    <body>
        <?php
        if(isset($_SESSION['login']) && $_SESSION['login']){

            $nombre_sesion = $_SESSION['usuario'];
            $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = '$nombre_sesion'";
            $resultado = mysqli_query($db_connection, $sql);
            
            if ($fila = mysqli_fetch_assoc($resultado)) {
                include 'vistas/inicio.php';
            }
        } else {
            include 'vistas/login.php';
        }
        ?>
    </body>
</html>