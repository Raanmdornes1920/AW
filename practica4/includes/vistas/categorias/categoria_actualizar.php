<?php
require_once '../../config.php';
require_once __DIR__ . '/apoyo/formularioActualizarCategoria.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$id = $_GET['id'] ?? 0;
$sa = new CategoriaSA($db_connection);
$categoria = $sa->obtenerPorId($id);

if (!$categoria) {
    header("Location: categorias_gerente.php"); exit;
}

$tituloPagina = "Editar Categoría";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$formulario = new FormularioActualizarCategoria($categoria);
$contenidoPrincipal = $formulario->gestiona();

require("../comun/plantilla.php");
?>
