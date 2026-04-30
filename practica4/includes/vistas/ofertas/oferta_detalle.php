<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$ofertaSA = new OfertaSA($db_connection);
$oferta = $id ? $ofertaSA->buscarPorId($id) : null;

if (!$oferta || !$oferta->estaActiva()) {
    header("Location: ofertas_cliente.php?error=" . urlencode("Oferta no disponible."));
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
$activadas = $_SESSION['ofertas_aplicadas'] ?? [];
$resumen = $ofertaSA->aplicarOfertasACarrito($carrito, $activadas);
$yaAplicada = in_array((int)$oferta->getId(), array_map('intval', $activadas), true);
$veces = $ofertaSA->vecesAplicable($oferta, $carrito);

$tituloPagina = $oferta->getNombre();
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [RAIZ_APP . "/js/script.js"];

$nombre = htmlspecialchars($oferta->getNombre());
$idOferta = (int)$oferta->getId();
$descripcion = htmlspecialchars($oferta->getDescripcion());
$descuento = number_format($oferta->getDescuento(), 2);
$precioPack = number_format($oferta->getPrecioPackSinDescuento(), 2);
$precioFinal = number_format($oferta->getPrecioPackConDescuento(), 2);
$ahorro = number_format($oferta->getAhorroDescuento(), 2);
$fechaInicio = date('d/m/Y', strtotime($oferta->getFechaInicio()));
$fechaFin = date('d/m/Y', strtotime($oferta->getFechaFin()));

$productosHtml = "";
foreach ($oferta->getProductos() as $producto) {
    $idProducto = (int)$producto['id_producto'];
    $cantidadNecesaria = (int)$producto['cantidad'];
    $cantidadCarrito = isset($carrito[$idProducto]) ? (int)$carrito[$idProducto] : 0;
    $nombreProducto = htmlspecialchars($producto['nombre']);
    $precio = number_format($producto['precio_con_iva'], 2);
    $estadoProducto = $cantidadCarrito >= $cantidadNecesaria
        ? "<span class='badge text-bg-success'>En carrito: {$cantidadCarrito}</span>"
        : "<span class='badge text-bg-secondary'>En carrito: {$cantidadCarrito}</span>";

    $productosHtml .= <<<EOF
    <tr>
        <td>{$nombreProducto}</td>
        <td>{$cantidadNecesaria}</td>
        <td>{$estadoProducto}</td>
        <td>{$precio} €</td>
        <td><a href="../productos/productos_detalle.php?id={$idProducto}" class="btn btn-sm btn-outline-primary">Ver producto</a></td>
    </tr>
EOF;
}

$accion = "";
if ($yaAplicada) {
    $accion = <<<EOF
    <form class="form-accion-oferta" method="POST" action="../pedidos/apoyo/procesar_oferta.php">
        <input type="hidden" name="accion" value="quitar">
        <input type="hidden" name="id_oferta" value="{$idOferta}">
        <button type="submit" class="btn btn-outline-danger">Quitar oferta</button>
    </form>
EOF;
} elseif ($veces > 0) {
    $accion = <<<EOF
    <form class="form-accion-oferta" method="POST" action="../pedidos/apoyo/procesar_oferta.php">
        <input type="hidden" name="accion" value="aplicar">
        <input type="hidden" name="id_oferta" value="{$idOferta}">
        <button type="submit" class="btn btn-success">Aplicar al pedido</button>
    </form>
EOF;
} else {
    $accion = "<a href='../productos/productos_cliente.php' class='btn btn-outline-primary'>Añadir productos necesarios</a>";
}

$aplicableTexto = $veces > 0
    ? "<span class='badge text-bg-success'>Aplicable automáticamente x{$veces}</span>"
    : "<span class='badge text-bg-secondary'>Todavía no aplicable</span>";

$contenidoPrincipal = <<<EOF
<section>
    <article class="card shadow-sm">
        <div class="card-body p-4">
        <header class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-start mb-4">
            <div>
                <h1 class="h2">{$nombre}</h1>
                <p class="text-secondary mb-0">{$descripcion}</p>
            </div>
            {$aplicableTexto}
        </header>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><small class="text-secondary">Precio pack</small><br><strong>{$precioPack} €</strong></div></div>
            <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><small class="text-secondary">Descuento</small><br><strong class="text-danger">-{$descuento}%</strong></div></div>
            <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><small class="text-secondary">Precio final</small><br><strong class="text-success">{$precioFinal} €</strong></div></div>
            <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><small class="text-secondary">Ahorro</small><br><strong>{$ahorro} €</strong></div></div>
        </div>

        <p class="text-secondary"><strong>Disponible:</strong> {$fechaInicio} - {$fechaFin}</p>

        <h2 class="h4">Productos necesarios</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Necesitas</th>
                    <th>Tu carrito</th>
                    <th>Precio unidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>{$productosHtml}</tbody>
        </table>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            {$accion}
            <a href="ofertas_cliente.php" class="btn btn-outline-secondary">Volver a ofertas</a>
            <a href="../pedidos/carrito.php" class="btn btn-primary">Ir al carrito</a>
        </div>
        </div>
    </article>
</section>
EOF;

require("../comun/plantilla.php");
?>
