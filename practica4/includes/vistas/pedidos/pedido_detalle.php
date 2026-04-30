<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id_pedido) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedido = $pedidoSA->obtenerPorId($id_pedido);

if (!$pedido) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$rol = $_SESSION['usuario']->rol();
if ($rol === 'cliente' && (int)$pedido->getIdUsuario() !== (int)$_SESSION['usuario']->id()) {
    header("Location: " . RUTA_VISTAS . "/pedidos/mis_pedidos.php");
    exit;
}

$lineas = $pedidoSA->obtenerDetallesPedido($pedido->getId());
$ofertas = $pedidoSA->obtenerOfertasPedido($pedido->getId());

$tituloPagina = "Detalle del Pedido";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$numero = $pedido->getNumeroPedido();
$idPedido = (int)$pedido->getId();
$idUsuarioPedido = (int)$pedido->getIdUsuario();
$estado = $pedido->getEstado();
$estadoVisual = ucfirst(str_replace('_', ' ', $estado));
$tipo = ucfirst($pedido->getTipo());
$fecha = date('d/m/Y H:i', strtotime($pedido->getFecha()));
$total = number_format($pedido->getTotal(), 2);
$totalSin = number_format($pedido->getTotalSinDescuento() ?? $pedido->getTotal(), 2);
$descuento = number_format($pedido->getDescuentoAplicado(), 2);

$lineasHtml = "";
$todasListas = true;
$hayCocinablesPendientes = false;

foreach ($lineas as $linea) {
    $idLinea = (int)$linea['id'];
    $cantidad = (int)$linea['cantidad'];
    $nombre = htmlspecialchars($linea['nombre']);
    $precioUnitario = number_format($linea['precio_unitario'], 2);
    $subtotal = number_format($linea['precio_unitario'] * $linea['cantidad'], 2);
    $preparado = (int)$linea['preparado'] === 1;
    $cocinable = isset($linea['cocinable']) && (int)$linea['cocinable'] === 1;
    $tipoLinea = $cocinable ? 'Cocina' : 'Barra';
    $estadoLinea = $preparado ? "<span class='badge text-bg-success'>Listo</span>" : "<span class='badge text-bg-secondary'>Pendiente</span>";

    if (!$preparado) {
        $todasListas = false;
    }
    if ($cocinable && !$preparado) {
        $hayCocinablesPendientes = true;
    }

    $accionLinea = "";
    if ($rol === 'cocinero' && $estado === 'cocinando' && $cocinable && !$preparado) {
        $accionLinea = "<form class='form-accion-oferta' action='apoyo/procesar_linea.php' method='POST'>
            <input type='hidden' name='id_linea' value='{$idLinea}'>
            <button type='submit' class='btn btn-sm btn-outline-primary'>Listo</button>
        </form>";
    } elseif ($rol === 'camarero' && $estado === 'listo_cocina' && !$cocinable && !$preparado) {
        $accionLinea = "<form class='form-accion-oferta' action='apoyo/procesar_linea.php' method='POST'>
            <input type='hidden' name='id_linea' value='{$idLinea}'>
            <button type='submit' class='btn btn-sm btn-outline-primary'>Servir</button>
        </form>";
    }

    $lineasHtml .= <<<EOF
    <tr>
        <td>{$nombre}</td>
        <td>{$cantidad}</td>
        <td>{$tipoLinea}</td>
        <td>{$estadoLinea}</td>
        <td>{$precioUnitario} €</td>
        <td>{$subtotal} €</td>
        <td>{$accionLinea}</td>
    </tr>
EOF;
}

$ofertasHtml = "";
if (!empty($ofertas)) {
    $ofertasHtml = "<section class='card shadow-sm mt-4'><div class='card-body'><h2 class='h4'>Ofertas aplicadas</h2><ul class='list-group'>";
    foreach ($ofertas as $oferta) {
        $nombreOferta = htmlspecialchars($oferta['nombre_oferta']);
        $veces = (int)$oferta['veces_aplicada'];
        $descOferta = number_format($oferta['descuento_total'], 2);
        $ofertasHtml .= "<li class='list-group-item d-flex justify-content-between'><span>{$nombreOferta} x{$veces}</span><strong class='text-danger'>-{$descOferta} €</strong></li>";
    }
    $ofertasHtml .= "</ul></div></section>";
}

$accionesRol = "";
if ($rol === 'camarero') {
    if ($estado === 'recibido') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='en_preparacion'>
            <button type='submit' class='btn btn-success btn-lg'>Cobrar y enviar a cocina</button>
        </form>";
    } elseif ($estado === 'listo_cocina') {
        if ($todasListas) {
            $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='{$idPedido}'>
                <input type='hidden' name='nuevo_estado' value='terminado'>
                <button type='submit' class='btn btn-success btn-lg'>Bandeja lista</button>
            </form>";
        } else {
            $accionesRol = "<div class='alert alert-warning mb-0'>Quedan productos de barra por servir antes de terminar la bandeja.</div>";
        }
    } elseif ($estado === 'terminado') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='entregado'>
            <button type='submit' class='btn btn-success btn-lg'>Entregar cliente</button>
        </form>";
    }
} elseif ($rol === 'cocinero') {
    if ($estado === 'en_preparacion') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='cocinando'>
            <button type='submit' class='btn btn-primary btn-lg'>Empezar a cocinar</button>
        </form>";
    } elseif ($estado === 'cocinando') {
        if (!$hayCocinablesPendientes) {
            $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='{$idPedido}'>
                <input type='hidden' name='nuevo_estado' value='listo_cocina'>
                <button type='submit' class='btn btn-success btn-lg'>Pedido completado</button>
            </form>";
        } else {
            $accionesRol = "<div class='alert alert-info mb-0'>Marca como listos todos los productos de cocina para finalizar.</div>";
        }
    }
} elseif ($rol === 'gerente' && in_array($estado, ['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado'], true)) {
    $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST' onsubmit='return confirm(\"¿Seguro que quieres cancelar este pedido?\")'>
        <input type='hidden' name='id_pedido' value='{$idPedido}'>
        <input type='hidden' name='nuevo_estado' value='cancelado'>
        <button type='submit' class='btn btn-outline-danger btn-lg'>Cancelar pedido</button>
    </form>";
}

$volver = RUTA_VISTAS . "/pedidos/mis_pedidos.php";
if ($rol === 'camarero') {
    $volver = RUTA_VISTAS . "/pedidos/pedidos_camarero.php";
} elseif ($rol === 'cocinero') {
    $volver = RUTA_VISTAS . "/pedidos/pedidos_cocinero.php";
} elseif ($rol === 'gerente') {
    $volver = RUTA_VISTAS . "/pedidos/pedidos_gerente.php";
}

$descuentoHtml = $pedido->getDescuentoAplicado() > 0
    ? "<div class='alert alert-success'><p class='mb-1'><strong>Subtotal sin descuentos:</strong> {$totalSin} €</p><p class='mb-0'><strong>Descuento aplicado:</strong> -{$descuento} €</p></div>"
    : "";

$contenidoPrincipal = <<<EOF
<section>
<article class="card shadow-sm">
    <div class="card-body p-4">
    <header class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-start mb-4">
        <h1 class="h2 mb-0">Pedido #{$numero}</h1>
        <span class="badge text-bg-secondary fs-6">{$estadoVisual}</span>
    </header>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><strong>Fecha</strong><br>{$fecha}</div></div>
        <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><strong>Tipo</strong><br>{$tipo}</div></div>
        <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><strong>Cliente</strong><br>ID {$idUsuarioPedido}</div></div>
        <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><strong>Total</strong><br>{$total} €</div></div>
    </div>

    {$descuentoHtml}

    <h2 class="h4">Productos del pedido</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>{$lineasHtml}</tbody>
        </table>
    </div>
    </div>
</article>

{$ofertasHtml}

<div class="d-flex flex-wrap gap-2 mt-4">
    {$accionesRol}
    <a href="{$volver}" class="btn btn-outline-secondary btn-lg">Volver</a>
</div>
</section>
EOF;

require("../comun/plantilla.php");
?>
