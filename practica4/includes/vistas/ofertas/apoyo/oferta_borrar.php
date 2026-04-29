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
$productosHtml = "<ul class='lista-pack lista-confirmacion-oferta'>";
foreach ($oferta->getProductos() as $p) {
    $productosHtml .= "<li>" . $p['cantidad'] . "x " . htmlspecialchars($p['nombre']) . "</li>";
}
$productosHtml .= "</ul>";

$contenidoPrincipal = <<<EOF
    <section class="pagina-cliente pagina-confirmacion-borrado">
    <div class="alerta-borrado">
        <h2>Confirmar Eliminación de Oferta</h2>
        <p>¿Estás seguro de que deseas eliminar la oferta <strong>{$nombre}</strong>?</p>
        <p>Productos incluidos:</p>
        {$productosHtml}
        <p><small>Nota: Esta acción no se puede deshacer. Los pedidos que ya aplicaron esta oferta no se verán afectados.</small></p>
        
        <div class="botones-confirmacion">
            <form class="form-accion-oferta" method="POST">
                <input type="hidden" name="confirmar_borrar" value="1">
                <button type="submit" class="boton-peligro">Sí, eliminar oferta</button>
            </form>
            <a href="../ofertas_gerente.php" class="boton-cancelar">No, cancelar</a>
        </div>
    </div>
    </section>
EOF;

require("../../comun/plantilla.php");
