<?php
require_once ('includes/config.php');
session_start();
$SA = new UsuarioSA($db_connection);

// DEFINIR tituloPagina, css, header, contenidoPrincipal y js  antes del include

$tituloPagina = "Bistro FDI";
$css = [];
$js = [(RAIZ_APP . "/js/script.js"),(RAIZ_APP . "/js/pedidos.js")];
$claseMain = "contenedor-centro";

ob_start(); // Capturamos el contenido del include
if(isset($_SESSION['login']) && $_SESSION['login']){
    if ($SA->usuarioValido($_SESSION['usuario'])) {
        $header = (DIR_RAIZ . '/includes/vistas/comun/header.php');
        include (DIR_RAIZ . '/includes/vistas/usuarios/inicio.php');
    }
    else{
        ob_get_clean(); // Cancelamos la captura de contenido y redirigimos a logout
        header("Location: " . RAIZ_APP . "/includes/vistas/usuarios/apoyo/logout.php");
        exit();
    }
} else {
    $header = (DIR_RAIZ . '/includes/vistas/comun/header_login.php');
    include (DIR_RAIZ . '/includes/vistas/usuarios/login.php');
}
$contenidoPrincipal = ob_get_clean(); // Guardamos contenido del include

require("./includes/vistas/comun/plantilla.php");
?>
