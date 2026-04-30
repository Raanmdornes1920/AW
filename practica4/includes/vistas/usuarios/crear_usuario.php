<?php
require_once (__DIR__ . '/../../config.php');
require_once (DIR_RAIZ . '/includes/vistas/usuarios/apoyo/formularioCrearUsuario.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}

$tituloPagina = "Gestionar Usuarios - Bistro FDI";
$css = [];
$header = (DIR_RAIZ . "/includes/vistas/comun/header.php");
$claseMain = "contenedor-centro";

$form = new FormularioCrearUsuario();
$htmlForm = $form->gestiona();
$contenidoPrincipal = <<<EOF
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-4">Nuevo Usuario</h1>
                $htmlForm
            </div>
        </div>
    </div>
</div>
EOF;

$js = [(RAIZ_APP . "/js/script.js")];

require("../comun/plantilla.php");

?>
