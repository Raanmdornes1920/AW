<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/formularioActualizarProducto.php');
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($_GET['id'] ?? 0);

if (!$producto) { header("Location: ../productos_gerente.php"); exit; }

$tituloPagina = "Actualizar Producto";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// Instanciamos el formulario pasándole el objeto producto encontrado
$form = new FormularioActualizarProducto($db_connection, $producto);
$htmlForm = $form->gestiona();

$contenidoPrincipal = <<<EOF
    <section class="contenedor-centro" id="contenido">
        $htmlForm
    </section>
EOF;

$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");