<?php
require_once (__DIR__ . '/../../config.php');
require_once (DIR_RAIZ . '/includes/vistas/usuarios/apoyo/formularioRegistro.php');
session_start();

$tituloPagina = "Registro - Bistro FDI";
$css = [(RAIZ_APP . "/css/default.css")];
$header = (DIR_RAIZ . "/includes/vistas/comun/header_registro.php");
$claseMain = "contenedor-centro";

$form = new FormularioRegistro();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF
        <section class="contenedor-centro" id="contenido">
            <h1 id="titulo-registro">Registro</h1>
            $htmlForm
        </section>
EOF;

$js = [(RAIZ_APP . "/js/script.js")];

require("../comun/plantilla.php");

?>