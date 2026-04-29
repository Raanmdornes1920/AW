<?php
require_once '../../config.php';
require_once __DIR__ . '/apoyo/formularioCrearCategoria.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$tituloPagina = "Nueva Categoría";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$formulario = new FormularioCrearCategoria();
$contenidoPrincipal = $formulario->gestiona();

require("../comun/plantilla.php");
?>
