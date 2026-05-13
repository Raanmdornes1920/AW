<?php
//require_once (__DIR__ '/../../config.php');
require_once __DIR__ . '/apoyo/formularioLogin.php';
//require_once __DIR__ . '/comun/plantilla.php';
//session_start();

$tituloPagina = "Login - Bistro FDI";
$css = [];
$claseMain = "contenedor-centro";

$form = new FormularioLogin();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-4 text-center">Inicio de sesión</h1>
                $htmlForm
            </div>
        </div>
    </div>
</div>
EOF;

$js = [(RAIZ_APP . "/js/script.js")];

echo $contenidoPrincipal;
?>
