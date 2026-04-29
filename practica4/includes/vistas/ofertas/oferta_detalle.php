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
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";
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
        ? "<span class='badge badge-success'>En carrito: {$cantidadCarrito}</span>"
        : "<span class='badge'>En carrito: {$cantidadCarrito}</span>";

    $productosHtml .= <<<EOF
    <tr>
        <td data-label="Producto">{$nombreProducto}</td>
        <td data-label="Necesitas">{$cantidadNecesaria}</td>
        <td data-label="Tu carrito">{$estadoProducto}</td>
        <td data-label="Precio unidad">{$precio} €</td>
        <td data-label="Acción"><a href="../productos/productos_detalle.php?id={$idProducto}" class="boton-editar">Ver producto</a></td>
    </tr>
EOF;
}

$accion = "";
if ($yaAplicada) {
    $accion = <<<EOF
    <form method="POST" action="../pedidos/apoyo/procesar_oferta.php">
        <input type="hidden" name="accion" value="quitar">
        <input type="hidden" name="id_oferta" value="{$idOferta}">
        <button type="submit" class="boton-borrar">Quitar oferta</button>
    </form>
EOF;
} elseif ($veces > 0) {
    $accion = <<<EOF
    <form method="POST" action="../pedidos/apoyo/procesar_oferta.php">
        <input type="hidden" name="accion" value="aplicar">
        <input type="hidden" name="id_oferta" value="{$idOferta}">
        <button type="submit" class="boton-nuevo">Aplicar al pedido</button>
    </form>
EOF;
} else {
    $accion = "<a href='../productos/productos_cliente.php' class='boton-editar'>Añadir productos necesarios</a>";
}

$aplicableTexto = $veces > 0
    ? "<span class='badge badge-success'>Aplicable automáticamente x{$veces}</span>"
    : "<span class='badge'>Todavía no aplicable</span>";

$contenidoPrincipal = <<<EOF
<section class="form-estilizado">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
        <h1 style="margin:0;">{$nombre}</h1>
        {$aplicableTexto}
    </div>
    <p style="color:#555;">{$descripcion}</p>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; background:#f9f9f9; padding:15px; border-radius:8px; margin:20px 0;">
        <div><small>Precio pack</small><br><strong>{$precioPack} €</strong></div>
        <div><small>Descuento</small><br><strong style="color:#e74c3c;">-{$descuento}%</strong></div>
        <div><small>Precio final</small><br><strong style="color:#2e7d32;">{$precioFinal} €</strong></div>
        <div><small>Ahorro</small><br><strong style="color:#2e7d32;">{$ahorro} €</strong></div>
    </div>

    <p><strong>Disponible:</strong> {$fechaInicio} - {$fechaFin}</p>

    <h2>Productos necesarios</h2>
    <div class="contenedor-tabla-scroll">
        <table class="tabla-gestion">
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

    <div class="acciones" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:25px;">
        {$accion}
        <a href="ofertas_cliente.php" class="boton-editar">Volver a ofertas</a>
        <a href="../pedidos/carrito.php" class="boton-nuevo">Ir al carrito</a>
    </div>
</section>
EOF;

require("../comun/plantilla.php");
?>
