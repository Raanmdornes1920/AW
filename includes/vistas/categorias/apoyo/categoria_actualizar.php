<?php
require_once '../../../config.php';
require_once (__DIR__ . '/formularioActualizarCategoria.php');

session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$sa = new CategoriaSA($db_connection);
$cat = $sa->obtenerPorId($_GET['id'] ?? 0);

if (!$cat) {
    header("Location: ../categorias_gerente.php");
    exit;
}

$formulario = new FormularioActualizarCategoria($cat);

$tituloPagina = "Editar Categoría";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$contenidoPrincipal = $formulario->gestiona();

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");