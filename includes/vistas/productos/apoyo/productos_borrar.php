<?php
require_once __DIR__ . '/../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_SPECIAL_CHARS);
    $idPost = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($accion === 'borrar' && $idPost && $sa->retirarDeCarta($idPost)) {
        header("Location: ../productos_gerente.php?msg=retirado_ok");
        exit;
    }

    header("Location: ../productos_gerente.php?error=retirar_producto");
    exit;
}

$producto = $sa->buscarProducto($_GET['id'] ?? 0);

if (!$producto) {
    header("Location: ../productos_gerente.php");
    exit;
}

$tituloPagina = "Retirar Producto";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$js = [RAIZ_APP . "/js/script.js"];

$nombre = htmlspecialchars($producto->getNombre());

$contenidoPrincipal = <<<EOF
    <div class="card shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4">
        <h1 class="h3">Confirmar retirada de carta</h1>
        <p>¿Estás seguro de que deseas retirar el producto <strong>$nombre</strong> de la carta?</p>
        <p class="text-secondary small">El producto dejará de estar visible para los clientes pero se mantendrá en el historial del inventario.</p>

        <div class="d-flex flex-wrap gap-2">
            <form action="productos_borrar.php" method="POST">
                <input type="hidden" name="id" value="{$producto->getId()}">
                <input type="hidden" name="accion" value="borrar">
                <button type="submit" class="btn btn-danger">Sí, retirar de la carta</button>
            </form>
            <a href="../productos_gerente.php" class="btn btn-outline-secondary">No, cancelar</a>
        </div>
        </div>
    </div>
EOF;

require("../../comun/plantilla.php");
