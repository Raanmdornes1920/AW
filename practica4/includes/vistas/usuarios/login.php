<?php
//require_once (__DIR__ '/../../config.php');
require_once __DIR__ . '/apoyo/formularioLogin.php';
//require_once __DIR__ . '/comun/plantilla.php';
//session_start();

$tituloPagina = "Login - Bistro FDI";
$css = [(RAIZ_APP . "/css/default.css")];
$claseMain = "contenedor-centro";

$form = new FormularioLogin();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF

            <h1>Inicio de Sesion</h1>
            $htmlForm

EOF;

$js = [(RAIZ_APP . "/js/script.js")];

echo $contenidoPrincipal;
?>