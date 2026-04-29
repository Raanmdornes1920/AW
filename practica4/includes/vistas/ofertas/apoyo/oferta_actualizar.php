<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/formularioActualizarOferta.php';

session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$sa = new OfertaSA($db_connection);
$oferta = $sa->buscarPorId($id);

if (!$oferta) {
    header("Location: ../ofertas_gerente.php?error=oferta_no_encontrada");
    exit;
}

$tituloPagina = "Actualizar: " . htmlspecialchars($oferta->getNombre());
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$form = new FormularioActualizarOferta($db_connection, $oferta);
$htmlForm = $form->gestiona();

$contenidoPrincipal = <<<EOF
    <section class="contenedor-centro" id="contenido">
        {$htmlForm}
    </section>
EOF;

$js = [
    RAIZ_APP . "/js/ofertas.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");
