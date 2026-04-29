<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
<<<<<<< HEAD
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$prodSA = new ProductoSA($db_connection);
$carrito = $_SESSION['carrito'] ?? [];
=======
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$prodSA = new ProductoSA($db_connection);
$ofertaSA = new OfertaSA($db_connection);
$carrito = $_SESSION['carrito'] ?? [];
$ofertasActivadas = $_SESSION['ofertas_aplicadas'] ?? [];
$resumenOfertas = $ofertaSA->aplicarOfertasACarrito($carrito, $ofertasActivadas);
>>>>>>> angela

$tituloPagina = "Tu Carrito";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
<<<<<<< HEAD
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlLineas = "";
$total = 0;

if (empty($carrito)) {
    $htmlLineas = "<p>Tu carrito está vacío. ¡Anímate a pedir algo rico!</p>";
    $htmlLineas .= '<a href="../productos/productos_cliente.php" class="boton-nuevo" style="display:inline-block; margin-top:20px;">Ver carta</a>';
} else {
    $htmlLineas .= '<table class="tabla-gestion">
=======
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
>>>>>>> angela
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
<<<<<<< HEAD
        
=======

>>>>>>> angela
    foreach ($carrito as $id_prod => $cantidad) {
        $producto = $prodSA->obtenerPorId($id_prod);
        if ($producto) {
            $precio = $producto->getPrecioFinal();
            $subtotal = $precio * $cantidad;
<<<<<<< HEAD
            $total += $subtotal;
            $nombre = htmlspecialchars($producto->getNombre());
            
=======
            $nombre = htmlspecialchars($producto->getNombre());
            $idSeguro = (int)$id_prod;
            $cantidadSeguro = (int)$cantidad;

>>>>>>> angela
            $htmlLineas .= "<tr>
                <td data-label='Producto'>{$nombre}</td>
                <td data-label='Precio'>" . number_format($precio, 2) . " €</td>
                <td data-label='Cantidad'>
<<<<<<< HEAD
                    <form action='apoyo/procesar_carrito.php' method='POST' style='display:flex; gap:10px; align-items:center; justify-content:center;'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='$id_prod'>
                        <input type='number' name='cantidad' value='$cantidad' min='1' style='width: 60px; padding:5px;'>
                        <button type='submit' class='boton-editar' style='padding: 5px 10px;'>↻</button>
=======
                    <form action='apoyo/procesar_carrito.php' method='POST' class='form-cantidad'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='{$idSeguro}'>
                        <input type='number' name='cantidad' value='{$cantidadSeguro}' min='1'>
                        <button type='submit' class='boton-editar'>Actualizar</button>
>>>>>>> angela
                    </form>
                </td>
                <td data-label='Subtotal'>" . number_format($subtotal, 2) . " €</td>
                <td data-label='Acciones'>
<<<<<<< HEAD
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto=$id_prod' class='boton-borrar'>Eliminar</a>
=======
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto={$idSeguro}' class='boton-borrar'>Eliminar</a>
>>>>>>> angela
                </td>
            </tr>";
        }
    }
<<<<<<< HEAD
    $htmlLineas .= "</tbody></table>";
    
    $htmlLineas .= "<div class='precio-final-destacado' style='text-align: right; margin-top: 20px; font-size: 1.2em;'>
                        Total a pagar: <strong>" . number_format($total, 2) . " €</strong>
                    </div>";
    
    // RECUPERADO: Lógica para recordar si el cliente había elegido Local o Llevar
=======
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

>>>>>>> angela
    $checkLocal = "";
    $checkLlevar = "";
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'llevar') {
        $checkLlevar = "checked";
    } else {
        $checkLocal = "checked";
    }

<<<<<<< HEAD
    $importeUsuario = number_format($total, 2);
    $amount = (string)round((float)$importeUsuario * 100);
    
    $error = $_GET['error'] ?? null;
    $htmlLineas .= <<<EOF
        <form id="formulario-pago" action="apoyo/pago_redsys.php" method="POST" class="form-estilizado" ...>
            <input type="hidden" name="amount" value="$amount">
            <input type="hidden" name="accion" value="confirmar">

            <span class="form-field-error">Ha habido un error al procesar el pago.</span>
            <h2>1. Detalles del Pedido</h2>
            <div class="grupo-checkbox" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                <label><input type="radio" name="tipo_pedido" value="local" $checkLocal> Para consumir en el local</label>
                <label><input type="radio" name="tipo_pedido" value="llevar" $checkLlevar> Para llevar</label>
            </div>
            
            <h2>2. Forma de Pago</h2>
            <div class="grupo-checkbox" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                <label><input type="radio" name="metodo_pago" value="tarjeta" checked onchange="togglePago(this.value)"> Pago con Tarjeta</label>
                <label><input type="radio" name="metodo_pago" value="camarero" onchange="togglePago(this.value)"> Pagar al camarero</label>
            </div>
            
            <div class="acciones" style="margin-top: 30px;">
                <button type="submit" class="boton-nuevo" style="width: 100%;">Confirmar y Pagar</button>
                <a href="apoyo/procesar_carrito.php?accion=clear" class="boton-borrar" style="display: block; text-align: center; margin-top: 10px;">Vaciar Carrito</a>
            </div>
        </form>

        <script>
        if ('$error' === '1') { 
            document.querySelector('.form-field-error').style.display = 'block';
        } else {
            document.querySelector('.form-field-error').style.display = 'none';
        }
        
        function togglePago(metodo) {
            var formulario = document.getElementById('formulario-pago');
            formulario.action = (metodo === 'tarjeta') ? "apoyo/pago_redsys.php" : "apoyo/procesar_carrito.php";
        }
        </script>
    EOF;

}

$contenidoPrincipal = "<h1 style='text-align:center;'>Revisar Pedido</h1>" . $htmlLineas;
require("../comun/plantilla.php");
?>
=======
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
>>>>>>> angela
