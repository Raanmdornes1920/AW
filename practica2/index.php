<?php 
require_once ('includes/config.php');
session_start(); 

// DEFINIR tituloPagina, css, header, contenidoPrincipal y js  antes del include

$tituloPagina = "Bistro FDI";
$css = [(RUTA_CSS . "/default.css")];
$js = [(RAIZ_APP . "/js/script.js")];

ob_start(); // Capturamos el contenido del include
if(isset($_SESSION['login']) && $_SESSION['login']){        
    global $db_connection;

    $nombre_sesion = $_SESSION['usuario']->username();
    $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = '$nombre_sesion'";
    $resultado = mysqli_query($db_connection, $sql);
    
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $header = (RUTA_COMUN . '/header.php');
        include (DIR_RAIZ . '/includes/vistas/inicio.php');
    }
} else {
    $header = (RUTA_COMUN . '/header_login.php');
    include (DIR_RAIZ . '/includes/vistas/login.php');
}
$contenidoPrincipal = ob_get_clean(); // Guardamos contenido del include

$header = './includes/vistas/comun/header_login.php';

require("./includes/vistas/comun/plantilla.php");
?>