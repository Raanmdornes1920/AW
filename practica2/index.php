<?php 
session_start(); 
require_once 'static/config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bistro FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
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