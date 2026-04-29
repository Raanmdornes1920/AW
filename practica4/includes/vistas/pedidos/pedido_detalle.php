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
$css = [RAIZ_APP . "/css/default.css"];
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
    $estadoLinea = $preparado ? "<span class='badge badge-success'>Listo</span>" : "<span class='badge'>Pendiente</span>";

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
            <button type='submit' class='boton-editar'>Listo</button>
        </form>";
    } elseif ($rol === 'camarero' && $estado === 'listo_cocina' && !$cocinable && !$preparado) {
        $accionLinea = "<form class='form-accion-oferta' action='apoyo/procesar_linea.php' method='POST'>
            <input type='hidden' name='id_linea' value='{$idLinea}'>
            <button type='submit' class='boton-editar'>Servir</button>
        </form>";
    }

    $lineasHtml .= <<<EOF
    <tr>
        <td data-label="Producto">{$nombre}</td>
        <td data-label="Cantidad">{$cantidad}</td>
        <td data-label="Tipo">{$tipoLinea}</td>
        <td data-label="Estado">{$estadoLinea}</td>
        <td data-label="Precio">{$precioUnitario} €</td>
        <td data-label="Subtotal">{$subtotal} €</td>
        <td data-label="Acción">{$accionLinea}</td>
    </tr>
EOF;
}

$ofertasHtml = "";
if (!empty($ofertas)) {
    $ofertasHtml = "<section class='panel-cliente panel-ofertas-pedido'><h2>Ofertas aplicadas</h2><ul class='lista-ofertas-aplicadas'>";
    foreach ($ofertas as $oferta) {
        $nombreOferta = htmlspecialchars($oferta['nombre_oferta']);
        $veces = (int)$oferta['veces_aplicada'];
        $descOferta = number_format($oferta['descuento_total'], 2);
        $ofertasHtml .= "<li>{$nombreOferta} x{$veces}: <strong>-{$descOferta} €</strong></li>";
    }
    $ofertasHtml .= "</ul></section>";
}

$accionesRol = "";
if ($rol === 'camarero') {
    if ($estado === 'recibido') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='en_preparacion'>
            <button type='submit' class='boton-nuevo'>Cobrar y enviar a cocina</button>
        </form>";
    } elseif ($estado === 'listo_cocina') {
        if ($todasListas) {
            $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='{$idPedido}'>
                <input type='hidden' name='nuevo_estado' value='terminado'>
                <button type='submit' class='boton-nuevo'>Bandeja lista</button>
            </form>";
        } else {
            $accionesRol = "<p class='mensaje mensaje-error'>Quedan productos de barra por servir antes de terminar la bandeja.</p>";
        }
    } elseif ($estado === 'terminado') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='entregado'>
            <button type='submit' class='boton-nuevo'>Entregar cliente</button>
        </form>";
    }
} elseif ($rol === 'cocinero') {
    if ($estado === 'en_preparacion') {
        $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
            <input type='hidden' name='id_pedido' value='{$idPedido}'>
            <input type='hidden' name='nuevo_estado' value='cocinando'>
            <button type='submit' class='boton-nuevo'>Empezar a cocinar</button>
        </form>";
    } elseif ($estado === 'cocinando') {
        if (!$hayCocinablesPendientes) {
            $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='{$idPedido}'>
                <input type='hidden' name='nuevo_estado' value='listo_cocina'>
                <button type='submit' class='boton-nuevo'>Pedido completado</button>
            </form>";
        } else {
            $accionesRol = "<p class='mensaje mensaje-info'>Marca como listos todos los productos de cocina para finalizar.</p>";
        }
    }
} elseif ($rol === 'gerente' && in_array($estado, ['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado'], true)) {
    $accionesRol = "<form class='form-accion-oferta' action='apoyo/procesar_estado_pedido.php' method='POST' onsubmit='return confirm(\"¿Seguro que quieres cancelar este pedido?\")'>
        <input type='hidden' name='id_pedido' value='{$idPedido}'>
        <input type='hidden' name='nuevo_estado' value='cancelado'>
        <button type='submit' class='boton-borrar'>Cancelar pedido</button>
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
    ? "<div class='bloque-descuento'><p><strong>Subtotal sin descuentos:</strong> {$totalSin} €</p><p><strong>Descuento aplicado:</strong> -{$descuento} €</p></div>"
    : "";

$contenidoPrincipal = <<<EOF
<section class="pagina-cliente pagina-detalle-pedido">
<article class="panel-cliente detalle-pedido">
    <header class="cabecera-panel">
        <h1>Pedido #{$numero}</h1>
        <span class="badge">{$estadoVisual}</span>
    </header>

    <div class="resumen-pedido">
        <p><strong>Fecha</strong><br>{$fecha}</p>
        <p><strong>Tipo</strong><br>{$tipo}</p>
        <p><strong>Cliente</strong><br>ID {$idUsuarioPedido}</p>
        <p><strong>Total</strong><br>{$total} €</p>
    </div>

    {$descuentoHtml}

    <h2>Productos del pedido</h2>
    <div class="contenedor-tabla-scroll">
        <table class="tabla-detalle tabla-pedido-detalle">
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
</article>

{$ofertasHtml}

<div class="acciones acciones-detalle">
    {$accionesRol}
    <a href="{$volver}" class="boton-editar">Volver</a>
</div>
</section>
EOF;

require("../comun/plantilla.php");
?>
