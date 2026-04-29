<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
$id_oferta = filter_input(INPUT_POST, 'id_oferta', FILTER_SANITIZE_NUMBER_INT);
$carrito = $_SESSION['carrito'] ?? [];

if (!isset($_SESSION['ofertas_aplicadas']) || !is_array($_SESSION['ofertas_aplicadas'])) {
    $_SESSION['ofertas_aplicadas'] = [];
}

$redir = RUTA_VISTAS . "/ofertas/ofertas_cliente.php";

if ($accion === 'clear') {
    $_SESSION['ofertas_aplicadas'] = [];
    header("Location: " . $redir . "?ok=" . urlencode("Ofertas quitadas."));
    exit;
}

if (!$id_oferta) {
    header("Location: " . $redir . "?error=" . urlencode("Oferta no válida."));
    exit;
}

$ofertaSA = new OfertaSA($db_connection);
$seleccionadas = array_values(array_unique(array_map('intval', $_SESSION['ofertas_aplicadas'])));

if ($accion === 'quitar') {
    $_SESSION['ofertas_aplicadas'] = array_values(array_filter(
        $seleccionadas,
        function($id) use ($id_oferta) { return (int)$id !== (int)$id_oferta; }
    ));
    header("Location: " . $redir . "?ok=" . urlencode("Oferta quitada del pedido."));
    exit;
}

if ($accion === 'aplicar') {
    if (empty($carrito)) {
        header("Location: " . $redir . "?error=" . urlencode("Añade productos al carrito antes de activar ofertas."));
        exit;
    }

    if (!in_array((int)$id_oferta, $seleccionadas, true)) {
        $seleccionadas[] = (int)$id_oferta;
    }

    $resumen = $ofertaSA->aplicarOfertasACarrito($carrito, $seleccionadas);
    $aplicada = false;
    foreach ($resumen['ofertas_aplicadas'] as $ofertaAplicada) {
        if ((int)$ofertaAplicada['id'] === (int)$id_oferta) {
            $aplicada = true;
            break;
        }
    }

    if (!$aplicada) {
        header("Location: " . $redir . "?error=" . urlencode("La oferta no se puede aplicar con el carrito actual o entra en conflicto con otra oferta."));
        exit;
    }

    $_SESSION['ofertas_aplicadas'] = $seleccionadas;
    header("Location: " . $redir . "?ok=" . urlencode("Oferta aplicada al pedido."));
    exit;
}

header("Location: " . $redir . "?error=" . urlencode("Acción no válida."));
exit;
?>
