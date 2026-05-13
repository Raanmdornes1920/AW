<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_SPECIAL_CHARS)
    ?: filter_input(INPUT_GET, 'accion', FILTER_SANITIZE_SPECIAL_CHARS)
    ?: '';
$id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_SANITIZE_NUMBER_INT)
    ?: filter_input(INPUT_GET, 'id_producto', FILTER_SANITIZE_NUMBER_INT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT) ?: 1;
$cantidad = max(1, (int) $cantidad);

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['ofertas_aplicadas']) || !is_array($_SESSION['ofertas_aplicadas'])) {
    $_SESSION['ofertas_aplicadas'] = [];
}

$variables = "";
if (isset($_GET['id_producto']) || isset($_GET['id_categoria']) || isset($_GET['tipo']) || isset($_GET['id_oferta'])) {
    $variables = "?"
        . (isset($_GET['id_producto']) ? "id=" .
            filter_input(INPUT_GET, 'id_producto', FILTER_SANITIZE_NUMBER_INT) : "")
        . (isset($_GET['id_categoria']) ? "&id_categoria=" .
            filter_input(INPUT_GET, 'id_categoria', FILTER_SANITIZE_NUMBER_INT) : "")
        . (isset($_GET['id_oferta']) ? "&id_oferta=" .
            filter_input(INPUT_GET, 'id_oferta', FILTER_SANITIZE_NUMBER_INT) : "")
        . (isset($_GET['tipo']) ? "&tipo=" .
            urlencode(filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS)) : "");
    $variables = str_replace("?&", "?", $variables);
}

switch ($accion) {
    case 'add':
        if ($id_producto) {
            if (isset($_SESSION['carrito'][$id_producto])) {
                $_SESSION['carrito'][$id_producto] += $cantidad;
            } else {
                $_SESSION['carrito'][$id_producto] = $cantidad;
            }
        }

        if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
            header('content-Type: application/json');
            //Que hace esta linea? -> Establece que la respuesta será en formato JSON.
            //1.Preparar variable por defceto
            $veces_aplicable = 0;
            //2.comporbar si se han enviado el id de la oferta
            $id_oferta = filter_input(INPUT_GET, 'id_oferta', FILTER_SANITIZE_NUMBER_INT);
            //3. SI HAY OFERTA CALCULAMOS LAS VECES QUE SE PUEDE APLICAR
            if ($id_oferta) {
                $ofertaSA = new OfertaSA($db_connection);
                $oferta = $ofertaSA->buscarPorId($id_oferta);
                if ($oferta) {
                    $veces_aplicable = $ofertaSA->vecesAplicable($oferta, $_SESSION['carrito']);
                }
            }

            // Calcular num_items_carrito
            $num_items_carrito = 0;
            if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
                $num_items_carrito = array_sum($_SESSION['carrito']);
            }

            //4. Enviar el JSON añadiendo el nuevo dato
            echo json_encode([
                'status' => 'success',
                'veces_aplicable' => $veces_aplicable,
                'num_items_carrito' => $num_items_carrito
            ]);
            exit;
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $url_limpia = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) . $variables;
            header("Location: " . $url_limpia);
        } else {
            header("Location: ../../productos/productos_cliente.php" . $variables);
        }
        break;

    case 'update':
        if ($id_producto && $cantidad > 0) {
            $_SESSION['carrito'][$id_producto] = $cantidad;
        }
        header("Location: ../carrito.php");
        break;

    case 'remove':
        if ($id_producto) {
            unset($_SESSION['carrito'][$id_producto]);
        }
        header("Location: ../carrito.php");
        break;

    case 'clear':
        $_SESSION['carrito'] = [];
        $_SESSION['ofertas_aplicadas'] = [];
        header("Location: ../carrito.php");
        break;

    case 'confirmar':
        $tipo = filter_input(INPUT_POST, 'tipo_pedido', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'local';
        $metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'tarjeta';
        $id_usuario = $_SESSION['usuario']->id();

        $pedidoSA = new PedidoSA($db_connection);
        $id_pedido = $pedidoSA->procesarCompra(
            $id_usuario,
            $tipo,
            $_SESSION['carrito'],
            $metodo_pago,
            $_SESSION['ofertas_aplicadas']
        );

        if ($id_pedido) {
            $_SESSION['carrito'] = [];
            $_SESSION['ofertas_aplicadas'] = [];
            header("Location: ../pedido_confirmado.php?id=" . $id_pedido);
        } else {
            header("Location: ../carrito.php?error=1");
        }
        break;

    default:
        header("Location: ../carrito.php");
        break;
}
exit;
?>