<?php
require_once '../../../config.php';
require_once (__DIR__ . '/formularioCrearCategoria.php');

session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$formulario = new FormularioCrearCategoria();

$tituloPagina = "Nueva Categoría";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$contenidoPrincipal = $formulario->gestiona();

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");