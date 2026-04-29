<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$prodSA = new ProductoSA($db_connection);
$ofertaSA = new OfertaSA($db_connection);
$carrito = $_SESSION['carrito'] ?? [];
$ofertasActivadas = $_SESSION['ofertas_aplicadas'] ?? [];
$resumenOfertas = $ofertaSA->aplicarOfertasACarrito($carrito, $ofertasActivadas);

$tituloPagina = "Tu Carrito";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlLineas = "";

if (isset($_GET['error'])) {
    $htmlLineas .= "<div class='mensaje mensaje-error'>No se pudo confirmar el pedido. Revisa el carrito e inténtalo de nuevo.</div>";
}

if (empty($carrito)) {
    $htmlLineas .= "<section class='panel-cliente estado-vacio'><p>Tu carrito está vacío. ¡Anímate a pedir algo rico!</p>";
    $htmlLineas .= '<a href="../productos/productos_cliente.php" class="boton-nuevo">Ver carta</a></section>';
} else {
    $htmlLineas .= '<div class="panel-cliente panel-tabla-carrito">
        <table class="tabla-detalle tabla-carrito">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($carrito as $id_prod => $cantidad) {
        $producto = $prodSA->obtenerPorId($id_prod);
        if ($producto) {
            $precio = $producto->getPrecioFinal();
            $subtotal = $precio * $cantidad;
            $nombre = htmlspecialchars($producto->getNombre());
            $idSeguro = (int)$id_prod;
            $cantidadSeguro = (int)$cantidad;

            $htmlLineas .= "<tr>
                <td data-label='Producto'>{$nombre}</td>
                <td data-label='Precio'>" . number_format($precio, 2) . " €</td>
                <td data-label='Cantidad'>
                    <form action='apoyo/procesar_carrito.php' method='POST' class='form-cantidad'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='{$idSeguro}'>
                        <input type='number' name='cantidad' value='{$cantidadSeguro}' min='1'>
                        <button type='submit' class='boton-editar'>Actualizar</button>
                    </form>
                </td>
                <td data-label='Subtotal'>" . number_format($subtotal, 2) . " €</td>
                <td data-label='Acciones'>
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto={$idSeguro}' class='boton-borrar'>Eliminar</a>
                </td>
            </tr>";
        }
    }
    $htmlLineas .= "</tbody></table></div>";

    $ofertasHtml = "";
    if (!empty($resumenOfertas['ofertas_aplicadas'])) {
        $ofertasHtml .= "<ul class='lista-ofertas-aplicadas'>";
        foreach ($resumenOfertas['ofertas_aplicadas'] as $oferta) {
            $nombreOferta = htmlspecialchars($oferta['nombre']);
            $veces = (int)$oferta['veces'];
            $ahorro = number_format($oferta['ahorro_total'], 2);
            $ofertasHtml .= "<li>{$nombreOferta} x{$veces}: <strong>-{$ahorro} €</strong></li>";
        }
        $ofertasHtml .= "</ul>";
    }

    if (!empty($resumenOfertas['ofertas_no_aplicables'])) {
        $ofertasHtml .= "<div class='mensaje mensaje-error'>";
        foreach ($resumenOfertas['ofertas_no_aplicables'] as $oferta) {
            $nombreOferta = htmlspecialchars($oferta['nombre']);
            $motivo = htmlspecialchars($oferta['motivo']);
            $ofertasHtml .= "<div><strong>{$nombreOferta}:</strong> {$motivo}</div>";
        }
        $ofertasHtml .= "</div>";
    }

    $totalSin = number_format($resumenOfertas['total_sin_descuento'], 2);
    $descuento = number_format($resumenOfertas['descuento_total'], 2);
    $totalFinal = number_format($resumenOfertas['total_final'], 2);

    $htmlLineas .= <<<EOF
    <section class="panel-cliente panel-carrito-ofertas">
        <h2>Ofertas del pedido</h2>
        {$ofertasHtml}
        <div class="acciones">
            <a href="../ofertas/ofertas_cliente.php" class="boton-editar">Ver ofertas disponibles</a>
            <form class="form-accion-oferta" action="apoyo/procesar_oferta.php" method="POST">
                <input type="hidden" name="accion" value="clear">
                <button type="submit" class="boton-borrar">Quitar ofertas</button>
            </form>
        </div>
    </section>

    <div class="panel-cliente resumen-total-carrito">
        <div>Subtotal: <strong>{$totalSin} €</strong></div>
        <div class="linea-descuento">Descuento: <strong>-{$descuento} €</strong></div>
        <div class="linea-total">Total a pagar: <strong>{$totalFinal} €</strong></div>
    </div>
EOF;

    $checkLocal = "";
    $checkLlevar = "";
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'llevar') {
        $checkLlevar = "checked";
    } else {
        $checkLocal = "checked";
    }

    $htmlLineas .= <<<EOF
    <form action="apoyo/procesar_carrito.php" method="POST" class="panel-cliente formulario-checkout">
        <input type="hidden" name="accion" value="confirmar">

        <h2>Detalles del pedido</h2>
        <div class="grupo-opciones">
            <label><input type="radio" name="tipo_pedido" value="local" {$checkLocal}> Para consumir en el local</label>
            <label><input type="radio" name="tipo_pedido" value="llevar" {$checkLlevar}> Para llevar</label>
        </div>

        <h2>Forma de pago</h2>
        <div class="grupo-opciones">
            <label><input type="radio" name="metodo_pago" value="tarjeta" checked onchange="togglePago(this.value)"> Pago con Tarjeta</label>
            <label><input type="radio" name="metodo_pago" value="camarero" onchange="togglePago(this.value)"> Pagar al camarero</label>
        </div>

        <div id="seccion-tarjeta" class="bloque-tarjeta">
            <label>Número de Tarjeta:</label>
            <input type="text" id="input-tarjeta" placeholder="1234 5678 9101 1121" pattern="\d{16}" title="Debe contener 16 dígitos numéricos" required>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-nuevo">Confirmar y Pagar</button>
            <a href="apoyo/procesar_carrito.php?accion=clear" class="boton-borrar">Vaciar Carrito</a>
        </div>
    </form>

    <script>
    function togglePago(metodo) {
        var seccionTarjeta = document.getElementById('seccion-tarjeta');
        var inputTarjeta = document.getElementById('input-tarjeta');
        if (metodo === 'camarero') {
            seccionTarjeta.style.display = 'none';
            inputTarjeta.removeAttribute('required');
        } else {
            seccionTarjeta.style.display = 'block';
            inputTarjeta.setAttribute('required', 'required');
        }
    }
    </script>
EOF;
}

$contenidoPrincipal = "<section class='pagina-cliente pagina-carrito'><header class='cabecera-pagina'><h1>Revisar Pedido</h1></header>{$htmlLineas}</section>";
require("../comun/plantilla.php");
?>
