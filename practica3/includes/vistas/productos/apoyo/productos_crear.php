<?php
require_once (__DIR__ . '/../../../config.php'); 
require_once (__DIR__ . '/formularioCrearProducto.php');
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); 
    exit;
}

$tituloPagina = "Crear Producto";
$css = [];
$header = "../../comun/header.php"; 
$claseMain = "contenedor-centro";

$form = new FormularioCrearProducto($db_connection);
$htmlForm = $form->gestiona();

$contenidoPrincipal = <<<EOF
    <section class="contenedor-centro" id="contenido">
        $htmlForm
    </section>
EOF;

$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");