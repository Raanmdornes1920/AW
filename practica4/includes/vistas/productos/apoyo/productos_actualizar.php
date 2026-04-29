<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/formularioActualizarProducto.php';

session_start();

// Control de acceso
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

// 1. Validamos que el producto exista antes de hacer nada
$id = $_GET['id'] ?? 0;
$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($id);

if (!$producto) {
    // Si no existe el ID, volvemos al listado con un aviso
    header("Location: ../productos_gerente.php?error=producto_no_encontrado");
    exit;
}

// Configuración de la página
$tituloPagina = "Actualizar: " . htmlspecialchars($producto->getNombre());
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// 2. Instanciamos el formulario pasándole el objeto producto recuperado
$form = new FormularioActualizarProducto($db_connection, $producto);

// 3. Ejecutamos la gestión del formulario
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