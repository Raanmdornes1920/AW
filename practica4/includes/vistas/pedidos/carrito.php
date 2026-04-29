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
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlLineas = "";

if (isset($_GET['error'])) {
    $htmlLineas .= "<div class='alerta-error' style='background:#fff3e0;border:1px solid #ffe0b2;padding:12px;border-radius:8px;margin-bottom:20px;color:#e65100;'>No se pudo confirmar el pedido. Revisa el carrito e inténtalo de nuevo.</div>";
}

if (empty($carrito)) {
    $htmlLineas .= "<p>Tu carrito está vacío. ¡Anímate a pedir algo rico!</p>";
    $htmlLineas .= '<a href="../productos/productos_cliente.php" class="boton-nuevo" style="display:inline-block; margin-top:20px;">Ver carta</a>';
} else {
    $htmlLineas .= '<table class="tabla-gestion">
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
                    <form action='apoyo/procesar_carrito.php' method='POST' style='display:flex; gap:10px; align-items:center; justify-content:center;'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='{$idSeguro}'>
                        <input type='number' name='cantidad' value='{$cantidadSeguro}' min='1' style='width: 60px; padding:5px;'>
                        <button type='submit' class='boton-editar' style='padding: 5px 10px;'>↻</button>
                    </form>
                </td>
                <td data-label='Subtotal'>" . number_format($subtotal, 2) . " €</td>
                <td data-label='Acciones'>
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto={$idSeguro}' class='boton-borrar'>Eliminar</a>
                </td>
            </tr>";
        }
    }
    $htmlLineas .= "</tbody></table>";

    $ofertasHtml = "";
    if (!empty($resumenOfertas['ofertas_aplicadas'])) {
        $ofertasHtml .= "<ul style='list-style:none; padding:0; margin:10px 0;'>";
        foreach ($resumenOfertas['ofertas_aplicadas'] as $oferta) {
            $nombreOferta = htmlspecialchars($oferta['nombre']);
            $veces = (int)$oferta['veces'];
            $ahorro = number_format($oferta['ahorro_total'], 2);
            $ofertasHtml .= "<li style='padding:6px 0;'>✓ {$nombreOferta} x{$veces}: <strong>-{$ahorro} €</strong></li>";
        }
        $ofertasHtml .= "</ul>";
    }

    if (!empty($resumenOfertas['ofertas_no_aplicables'])) {
        $ofertasHtml .= "<div style='background:#fff3e0; border:1px solid #ffe0b2; padding:10px; border-radius:8px; margin-top:10px; color:#e65100;'>";
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
    <section class="form-estilizado" style="margin-top:25px;">
        <h2>Ofertas del pedido</h2>
        <p style="color:#666;">Puedes activar varias ofertas disponibles de forma secuencial. Si una oferta se cumple varias veces, se aplica automáticamente.</p>
        {$ofertasHtml}
        <div class="acciones" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:15px;">
            <a href="../ofertas/ofertas_cliente.php" class="boton-editar">Ver ofertas disponibles</a>
            <form action="apoyo/procesar_oferta.php" method="POST">
                <input type="hidden" name="accion" value="clear">
                <button type="submit" class="boton-borrar">Quitar ofertas</button>
            </form>
        </div>
    </section>

    <div class="precio-final-destacado" style="text-align:right; margin-top:20px; font-size:1.2em;">
        <div>Subtotal: <strong>{$totalSin} €</strong></div>
        <div style="color:#27ae60;">Descuento: <strong>-{$descuento} €</strong></div>
        <div style="font-size:1.3em;">Total a pagar: <strong>{$totalFinal} €</strong></div>
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
    <form action="apoyo/procesar_carrito.php" method="POST" class="form-estilizado" style="margin-top:40px; max-width:600px; margin-left:auto; margin-right:auto;">
        <input type="hidden" name="accion" value="confirmar">

        <h2>1. Detalles del Pedido</h2>
        <div class="grupo-checkbox" style="margin-bottom:20px; border:1px solid #ddd; padding:15px; border-radius:8px;">
            <label><input type="radio" name="tipo_pedido" value="local" {$checkLocal}> Para consumir en el local</label>
            <label><input type="radio" name="tipo_pedido" value="llevar" {$checkLlevar}> Para llevar</label>
        </div>

        <h2>2. Forma de Pago</h2>
        <div class="grupo-checkbox" style="margin-bottom:20px; border:1px solid #ddd; padding:15px; border-radius:8px;">
            <label><input type="radio" name="metodo_pago" value="tarjeta" checked onchange="togglePago(this.value)"> Pago con Tarjeta</label>
            <label><input type="radio" name="metodo_pago" value="camarero" onchange="togglePago(this.value)"> Pagar al camarero</label>
        </div>

        <div id="seccion-tarjeta" style="background:#f1f1f1; padding:15px; border-radius:8px; margin-bottom:20px;">
            <label>Número de Tarjeta:</label>
            <input type="text" id="input-tarjeta" placeholder="1234 5678 9101 1121" pattern="\d{16}" title="Debe contener 16 dígitos numéricos" required>
        </div>

        <div class="acciones" style="margin-top:30px;">
            <button type="submit" class="boton-nuevo" style="width:100%;">Confirmar y Pagar</button>
            <a href="apoyo/procesar_carrito.php?accion=clear" class="boton-borrar" style="display:block; text-align:center; margin-top:10px;">Vaciar Carrito</a>
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

$contenidoPrincipal = "<h1 style='text-align:center;'>Revisar Pedido</h1>" . $htmlLineas;
require("../comun/plantilla.php");
?>
