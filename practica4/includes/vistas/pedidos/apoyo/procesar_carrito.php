<?php
require_once '../../../config.php';
session_start();

<<<<<<< HEAD
// Validamos que sea un cliente
=======
>>>>>>> angela
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

<<<<<<< HEAD
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id_producto = $_POST['id_producto'] ?? $_GET['id_producto'] ?? null;
$cantidad = (int)($_POST['cantidad'] ?? 1);

// Inicializamos el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$variables = "";
if(isset($_GET['id_producto']) || isset($_GET['id_categoria']) || isset($_GET['tipo'])){
    $variables = "?" . (isset($_GET['id_producto'])?"id=".$_GET['id_producto']:"") . (isset($_GET['id_categoria'])?"&id_categoria=" . $_GET['id_categoria']:"") . (isset($_GET['tipo'])?"&tipo=" . $_GET['tipo']:"");
=======
$accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_SPECIAL_CHARS)
    ?: filter_input(INPUT_GET, 'accion', FILTER_SANITIZE_SPECIAL_CHARS)
    ?: '';
$id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_SANITIZE_NUMBER_INT)
    ?: filter_input(INPUT_GET, 'id_producto', FILTER_SANITIZE_NUMBER_INT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT) ?: 1;
$cantidad = max(1, (int)$cantidad);

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['ofertas_aplicadas']) || !is_array($_SESSION['ofertas_aplicadas'])) {
    $_SESSION['ofertas_aplicadas'] = [];
}

$variables = "";
if (isset($_GET['id_producto']) || isset($_GET['id_categoria']) || isset($_GET['tipo'])) {
    $variables = "?"
        . (isset($_GET['id_producto']) ? "id=" . filter_input(INPUT_GET, 'id_producto', FILTER_SANITIZE_NUMBER_INT) : "")
        . (isset($_GET['id_categoria']) ? "&id_categoria=" . filter_input(INPUT_GET, 'id_categoria', FILTER_SANITIZE_NUMBER_INT) : "")
        . (isset($_GET['tipo']) ? "&tipo=" . urlencode(filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS)) : "");
>>>>>>> angela
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
<<<<<<< HEAD
        // Redirigimos de vuelta a los productos para que siga comprando
=======
>>>>>>> angela
        if (isset($_SERVER['HTTP_REFERER'])) {
            $url_limpia = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) . $variables;
            header("Location: " . $url_limpia);
        } else {
            header("Location: ../../productos/productos_cliente.php" . $variables);
        }
        break;
<<<<<<< HEAD
        
=======
>>>>>>> angela

    case 'update':
        if ($id_producto && $cantidad > 0) {
            $_SESSION['carrito'][$id_producto] = $cantidad;
<<<<<<< HEAD
        } elseif ($cantidad <= 0) {
            unset($_SESSION['carrito'][$id_producto]);
=======
>>>>>>> angela
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
<<<<<<< HEAD
=======
        $_SESSION['ofertas_aplicadas'] = [];
>>>>>>> angela
        header("Location: ../carrito.php");
        break;

    case 'confirmar':
<<<<<<< HEAD
        // METER REDSYS
        $dataBase64 = $_GET['Ds_MerchantParameters'] ?? '';
        $tipo = '';
        if($dataBase64 !== '') {
            // 1. Decodificar de Base64 a JSON
            $json = base64_decode(strtr($dataBase64, '-_', '+/')); // Redsys a veces usa Base64URL
            
            // 2. Convertir JSON a Array asociativo de PHP
            $data = json_decode($json, true);

            $tipo = $data['Ds_MerchantData'];
            $codigoAuth = $data['Ds_AuthorisationCode'];

            if ($codigoAuth == '++++++') {
                // Si algo falla, lo devolvemos al carrito con un error
                header("Location: ../carrito.php?tipo=$tipo&error=1");
                break;
            }
            
        }
        else
        {
            $tipo = $_POST['tipo_pedido'] ?? $_GET['tipo'] ?? 'local';
        }
        
        $id_usuario = $_SESSION['usuario']->id(); 
        
        // RECOGEMOS EL MÉTODO DE PAGO DEL FORMULARIO
        $metodo_pago = $_POST['metodo_pago'] ?? 'tarjeta';
        
        $pedidoSA = new PedidoSA($db_connection);
        // LE PASAMOS EL MÉTODO DE PAGO COMO CUARTO PARÁMETRO
        $id_pedido = $pedidoSA->procesarCompra($id_usuario, $tipo, $_SESSION['carrito'], $metodo_pago);
        
        if ($id_pedido) {
            
            $_SESSION['carrito'] = []; // Vaciamos el carrito tras comprar
            header("Location: ../pedido_confirmado.php?id=" . $id_pedido);
        } else {
            // Si algo falla, lo devolvemos al carrito con un error
            header("Location: ../carrito.php?error=1");
        }
        break;
        
=======
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

>>>>>>> angela
    default:
        header("Location: ../carrito.php");
        break;
}
<<<<<<< HEAD
?>
=======
exit;
?>
>>>>>>> angela
