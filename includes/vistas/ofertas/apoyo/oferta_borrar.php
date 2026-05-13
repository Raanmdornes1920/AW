<?php
require_once __DIR__ . '/../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new OfertaSA($db_connection);
$oferta = $sa->buscarPorId($_GET['id'] ?? 0);

if (!$oferta) { 
    header("Location: ../ofertas_gerente.php"); 
    exit; 
}

// Si se envía el formulario de confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_borrar'])) {
    $sa->borrarOferta($oferta->getId());
    header("Location: ../ofertas_gerente.php");
    exit;
}

$tituloPagina = "Eliminar Oferta";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [RAIZ_APP . "/js/script.js"];

$nombre = htmlspecialchars($oferta->getNombre());

// Mostrar productos de la oferta en la confirmación
$productosHtml = "<ul class='list-group mb-3'>";
foreach ($oferta->getProductos() as $p) {
    $productosHtml .= "<li class='list-group-item'>" . $p['cantidad'] . "x " . htmlspecialchars($p['nombre']) . "</li>";
}
$productosHtml .= "</ul>";

$contenidoPrincipal = <<<EOF
    <section>
    <div class="card shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4">
        <h1 class="h3">Confirmar eliminación de oferta</h1>
        <p>¿Estás seguro de que deseas eliminar la oferta <strong>{$nombre}</strong>?</p>
        <p>Productos incluidos:</p>
        {$productosHtml}
        <p class="text-secondary small">Esta acción no se puede deshacer. Los pedidos que ya aplicaron esta oferta no se verán afectados.</p>
        
        <div class="d-flex flex-wrap gap-2">
            <form method="POST">
                <input type="hidden" name="confirmar_borrar" value="1">
                <button type="submit" class="btn btn-danger">Sí, eliminar oferta</button>
            </form>
            <a href="../ofertas_gerente.php" class="btn btn-outline-secondary">No, cancelar</a>
        </div>
        </div>
    </div>
    </section>
EOF;

require("../../comun/plantilla.php");
