<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/formularioCrearOferta.php';

session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$tituloPagina = "Nueva Oferta - Bistro FDI";
$header = "../../comun/header.php";
$claseMain = "contenedor-cliente";

$form = new FormularioCrearOferta($db_connection);
$htmlForm = $form->gestiona();

$contenidoPrincipal = <<<EOF
    <section class="pagina-cliente pagina-formulario-oferta" id="contenido">
        {$htmlForm}
    </section>
EOF;

$js = [
    RAIZ_APP . "/js/ofertas.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");
