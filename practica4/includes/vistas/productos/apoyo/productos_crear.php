<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/formularioCrearProducto.php';

session_start();

// Control de acceso: Solo el gerente puede crear productos
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

// Configuración de la página para la plantilla
$tituloPagina = "Nuevo Producto";
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// 1. Instanciamos el formulario (inyectamos la conexión a la BD)
$form = new FormularioCrearProducto($db_connection);

// 2. Ejecutamos el ciclo de vida del formulario (Muestra / Procesa / Redirige)
$htmlForm = $form->gestiona();

// 3. Preparamos el contenido para la plantilla
$contenidoPrincipal = <<<EOF
    <section class="contenedor-centro" id="contenido">
        $htmlForm
    </section>
EOF;

// Scripts necesarios (producto.js contiene la función recalcular() del IVA)
$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");