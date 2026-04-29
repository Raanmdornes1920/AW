<?php
require_once (__DIR__ . '/../../config.php');
require_once (DIR_RAIZ . '/includes/vistas/usuarios/apoyo/formularioCrearUsuario.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}

$tituloPagina = "Gestionar Usuarios - Bistro FDI";
$css = [(RAIZ_APP . "/css/default.css"), (RAIZ_APP . "/css/modales.css")];
$header = (DIR_RAIZ . "/includes/vistas/comun/header.php");
$claseMain = "contenedor-centro";

$form = new FormularioCrearUsuario();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF
        <section class="contenedor-centro" id="contenido">
            <h1 id="titulo-registro">Nuevo Usuario</h1>
            $htmlForm
        </section>
EOF;

$js = [(RAIZ_APP . "/js/script.js")];

require("../comun/plantilla.php");

?>