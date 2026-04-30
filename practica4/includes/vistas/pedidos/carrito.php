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
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlLineas = "";

if (isset($_GET['error'])) {
    $htmlLineas .= "<div class='alert alert-danger'>No se pudo confirmar el pedido. Revisa el carrito e inténtalo de nuevo.</div>";
}

if (empty($carrito)) {
    $htmlLineas .= "<section class='card shadow-sm'><div class='card-body text-center p-5'><p class='lead'>Tu carrito está vacío. ¡Anímate a pedir algo rico!</p>";
    $htmlLineas .= '<a href="../productos/productos_cliente.php" class="btn btn-primary">Ver carta</a></div></section>';
} else {
    $htmlLineas .= '<div class="card shadow-sm mb-4"><div class="card-body"><div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
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
                <td>{$nombre}</td>
                <td>" . number_format($precio, 2) . " €</td>
                <td>
                    <form action='apoyo/procesar_carrito.php' method='POST' class='d-flex gap-2 align-items-center'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='{$idSeguro}'>
                        <input class='form-control form-control-sm' style='max-width: 90px;' type='number' name='cantidad' value='{$cantidadSeguro}' min='1'>
                        <button type='submit' class='btn btn-sm btn-outline-primary'>Actualizar</button>
                    </form>
                </td>
                <td>" . number_format($subtotal, 2) . " €</td>
                <td>
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto={$idSeguro}' class='btn btn-sm btn-outline-danger'>Eliminar</a>
                </td>
            </tr>";
        }
    }
    $htmlLineas .= "</tbody></table></div></div></div>";

    $ofertasHtml = "";
    if (!empty($resumenOfertas['ofertas_aplicadas'])) {
        $ofertasHtml .= "<ul class='list-group mb-3'>";
        foreach ($resumenOfertas['ofertas_aplicadas'] as $oferta) {
            $nombreOferta = htmlspecialchars($oferta['nombre']);
            $veces = (int)$oferta['veces'];
            $ahorro = number_format($oferta['ahorro_total'], 2);
            $ofertasHtml .= "<li class='list-group-item d-flex justify-content-between'><span>{$nombreOferta} x{$veces}</span><strong class='text-danger'>-{$ahorro} €</strong></li>";
        }
        $ofertasHtml .= "</ul>";
    }

    if (!empty($resumenOfertas['ofertas_no_aplicables'])) {
        $ofertasHtml .= "<div class='alert alert-warning'>";
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
    <section class="card shadow-sm mb-4">
        <div class="card-body">
        <h2 class="h4">Ofertas del pedido</h2>
        {$ofertasHtml}
        <div class="d-flex flex-wrap gap-2">
            <a href="../ofertas/ofertas_cliente.php" class="btn btn-outline-primary">Ver ofertas disponibles</a>
            <form action="apoyo/procesar_oferta.php" method="POST">
                <input type="hidden" name="accion" value="clear">
                <button type="submit" class="btn btn-outline-danger">Quitar ofertas</button>
            </form>
        </div>
        </div>
    </section>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">Subtotal: <strong>{$totalSin} €</strong></div>
                <div class="col-12 col-md-4 text-danger">Descuento: <strong>-{$descuento} €</strong></div>
                <div class="col-12 col-md-4 fs-5 text-success">Total a pagar: <strong>{$totalFinal} €</strong></div>
            </div>
        </div>
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
    <form action="apoyo/procesar_carrito.php" method="POST" class="card shadow-sm">
        <div class="card-body">
        <input type="hidden" name="accion" value="confirmar">

        <h2 class="h4">Detalles del pedido</h2>
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" id="tipo-local" name="tipo_pedido" value="local" {$checkLocal}>
                <label class="form-check-label" for="tipo-local">Para consumir en el local</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="tipo-llevar" name="tipo_pedido" value="llevar" {$checkLlevar}>
                <label class="form-check-label" for="tipo-llevar">Para llevar</label>
            </div>
        </div>

        <h2 class="h4">Forma de pago</h2>
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" id="pago-tarjeta" name="metodo_pago" value="tarjeta" checked onchange="togglePago(this.value)">
                <label class="form-check-label" for="pago-tarjeta">Pago con tarjeta</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="pago-camarero" name="metodo_pago" value="camarero" onchange="togglePago(this.value)">
                <label class="form-check-label" for="pago-camarero">Pagar al camarero</label>
            </div>
        </div>

        <div id="seccion-tarjeta" class="mb-4">
            <label class="form-label" for="input-tarjeta">Número de tarjeta</label>
            <input class="form-control" type="text" id="input-tarjeta" placeholder="1234 5678 9101 1121" pattern="\d{16}" title="Debe contener 16 dígitos numéricos" required>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success btn-lg">Confirmar y pagar</button>
            <a href="apoyo/procesar_carrito.php?accion=clear" class="btn btn-outline-danger btn-lg">Vaciar carrito</a>
        </div>
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

$contenidoPrincipal = "<section><header class='mb-4'><h1 class='h2'>Revisar pedido</h1></header>{$htmlLineas}</section>";
require("../comun/plantilla.php");
?>
