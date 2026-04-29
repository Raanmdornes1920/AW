<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$sa = new ProductoSA($db_connection);
$productos = $sa->getGestionAdmin();

$tituloPagina = "Gestión de Productos - Bistro FDI";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filas = "";
foreach($productos as $p) {
    $img = RUTA_IMG . "/productos/" . htmlspecialchars($p->getImagen());
    $nombre = htmlspecialchars($p->getNombre());
    $cat = htmlspecialchars($p->getNombreCategoria());
    $desc = htmlspecialchars($p->getDescripcion());
    $precio = number_format($p->getPrecioFinal(), 2);
    $id = $p->getId();

    $badgeStock = $p->getDisponible()
        ? '<span class="badge badge-success">Disponible</span>'
        : '<span class="badge badge-danger">Sin Stock</span>';

    $estado = $p->getOfertado() ? 'En Carta' : 'Retirado';

    // NUEVO CAMPO: Comprobamos si el producto es cocinable para ponerle un icono
    $esCocina = $p->getCocinable() ? "Sí" : "No";

    $filas .= <<<EOF
    <tr>
        <td><img src="$img" width="50" height="50" style="object-fit: cover; border-radius: 8px;"></td>
        <td>$nombre</td>
        <td>$cat</td>
        <td>$esCocina</td> <td class="col-desc">$desc</td>
        <td>$precio €</td>
        <td>$badgeStock</td>
        <td>$estado</td>
        <td>
            <a href="apoyo/productos_actualizar.php?id=$id" class="boton-editar">Editar</a>
            <a href="apoyo/productos_borrar.php?id=$id" class="boton-borrar">Eliminar</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Productos</h1>
    <a href="apoyo/productos_crear.php" class="boton-nuevo">Nuevo Producto</a>

    <div class="contenedor-tabla-scroll">
        <table class="tabla-gestion-productos">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Cocina</th> <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                $filas
            </tbody>
        </table>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
?>