<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new CategoriaSA($db_connection);
$cat = $sa->obtenerPorId($_GET['id'] ?? 0);

if (!$cat) { header("Location: ../categorias_gerente.php"); exit; }

$errorMsg = $_GET['error'] ?? null;
$nombre = htmlspecialchars($cat->getNombre());

$tituloPagina = "Eliminar Categoría";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$htmlError = "";
if ($errorMsg) {
    $htmlError = "<div class='alert alert-warning'><strong>Aviso:</strong> " . htmlspecialchars($errorMsg) . "</div>";
}

$botonBorrar = !$errorMsg ? '<button type="submit" class="btn btn-danger">Sí, eliminar definitivamente</button>' : '';

$contenidoPrincipal = <<<EOF
    <div class="card shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4">
        <h1 class="h3">Confirmar eliminación</h1>
        <p>Vas a eliminar la categoría <strong>$nombre</strong>.</p>

        $htmlError

        <div class="d-flex flex-wrap gap-2">
            <form action="procesar_categoria.php" method="POST">
                <input type="hidden" name="id" value="{$cat->getId()}">
                <input type="hidden" name="accion" value="eliminar_definitivo">
                $botonBorrar
            </form>
            <a href="../categorias_gerente.php" class="btn btn-outline-secondary">No, volver al listado</a>
        </div>
        </div>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../../comun/plantilla.php");
