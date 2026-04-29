<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$prodSA = new ProductoSA($db_connection);
$carrito = $_SESSION['carrito'] ?? [];

$tituloPagina = "Tu Carrito";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlLineas = "";
$total = 0;

if (empty($carrito)) {
    $htmlLineas = "<p>Tu carrito está vacío. ¡Anímate a pedir algo rico!</p>";
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
            $total += $subtotal;
            $nombre = htmlspecialchars($producto->getNombre());
            
            $htmlLineas .= "<tr>
                <td data-label='Producto'>{$nombre}</td>
                <td data-label='Precio'>" . number_format($precio, 2) . " €</td>
                <td data-label='Cantidad'>
                    <form action='apoyo/procesar_carrito.php' method='POST' style='display:flex; gap:10px; align-items:center; justify-content:center;'>
                        <input type='hidden' name='accion' value='update'>
                        <input type='hidden' name='id_producto' value='$id_prod'>
                        <input type='number' name='cantidad' value='$cantidad' min='1' style='width: 60px; padding:5px;'>
                        <button type='submit' class='boton-editar' style='padding: 5px 10px;'>↻</button>
                    </form>
                </td>
                <td data-label='Subtotal'>" . number_format($subtotal, 2) . " €</td>
                <td data-label='Acciones'>
                    <a href='apoyo/procesar_carrito.php?accion=remove&id_producto=$id_prod' class='boton-borrar'>Eliminar</a>
                </td>
            </tr>";
        }
    }
    $htmlLineas .= "</tbody></table>";
    
    $htmlLineas .= "<div class='precio-final-destacado' style='text-align: right; margin-top: 20px; font-size: 1.2em;'>
                        Total a pagar: <strong>" . number_format($total, 2) . " €</strong>
                    </div>";
    
    // RECUPERADO: Lógica para recordar si el cliente había elegido Local o Llevar
    $checkLocal = "";
    $checkLlevar = "";
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'llevar') {
        $checkLlevar = "checked";
    } else {
        $checkLocal = "checked";
    }

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