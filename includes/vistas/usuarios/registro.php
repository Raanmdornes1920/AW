<?php
require_once (__DIR__ . '/../../config.php');
require_once (DIR_RAIZ . '/includes/vistas/usuarios/apoyo/formularioRegistro.php');
session_start();

$tituloPagina = "Registro - Bistro FDI";
$css = [];
$header = (DIR_RAIZ . "/includes/vistas/comun/header_registro.php");
$claseMain = "contenedor-centro";

$form = new FormularioRegistro();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-4 text-center">Registro</h1>
                $htmlForm
            </div>
        </div>
    </div>
</div>
EOF;

$js = [(RAIZ_APP . "/js/script.js")];

require("../comun/plantilla.php");

?>
