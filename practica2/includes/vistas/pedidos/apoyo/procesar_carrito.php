<?php
require_once '../../../config.php';
session_start();

// Validamos que sea un cliente
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id_producto = $_POST['id_producto'] ?? $_GET['id_producto'] ?? null;
$cantidad = (int)($_POST['cantidad'] ?? 1);

// Inicializamos el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$variables = "";
if(isset($_GET['id_categoria']) && isset($_GET['tipo'])){
    $variables = "?id_categoria=" . $_GET['id_categoria'] . "&tipo=" . $_GET['tipo'];
} elseif (isset($_GET['id_categoria'])){
    $variables = "?id_categoria=" . $_GET['id_categoria'];
}elseif (isset($_GET['tipo'])){
    $variables = "?tipo=" . $_GET['tipo'];
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
        // Redirigimos de vuelta a los productos para que siga comprando
        header("Location: ../../productos/productos_cliente.php" . $variables);
        break;

    case 'update':
        if ($id_producto && $cantidad > 0) {
            $_SESSION['carrito'][$id_producto] = $cantidad;
        } elseif ($cantidad <= 0) {
            unset($_SESSION['carrito'][$id_producto]);
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
        header("Location: ../carrito.php");
        break;

    case 'confirmar':
        $tipo = $_POST['tipo_pedido'] ?? $_GET['tipo'] ?? 'local';
        $id_usuario = $_SESSION['usuario']->id(); // Asegúrate de que este método exista en tu clase Usuario
        
        $pedidoSA = new PedidoSA($db_connection);
        $id_pedido = $pedidoSA->procesarCompra($id_usuario, $tipo, $_SESSION['carrito']);
        
        if ($id_pedido) {
            $_SESSION['carrito'] = []; // Vaciamos el carrito tras comprar
            header("Location: ../pedido_confirmado.php?id=" . $id_pedido);
        } else {
            // Si algo falla, lo devolvemos al carrito con un error
            header("Location: ../carrito.php?error=1");
        }
        break;
        
    default:
        header("Location: ../carrito.php");
        break;
}

?>