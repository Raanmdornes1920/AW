<?php
require_once '../../../config.php';
session_start();
echo "<pre>";
print_r($_FILES);
print_r($_POST);
echo "</pre>";
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$sa = new ProductoSA($db_connection);
$accion = $_REQUEST['accion'] ?? '';

if ($accion === 'crear' || $accion === 'actualizar') {
    $datos = $_POST;
    
    // IMPORTANTE: Asegurarnos de que el ID llegue desde el formulario
    // Si es 'actualizar', el $_POST['id'] debe estar presente.
    
    $imagenesSubidas = [];
    if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                $nombreNuevo = uniqid('prod_') . '_' . $key . '.' . $ext;
                $rutaDestino = __DIR__ . "/../../../../img/productos/" . $nombreNuevo;
                
                if (move_uploaded_file($tmp_name, $rutaDestino)) {
                    $imagenesSubidas[] = $nombreNuevo;
                }
            }
        }
    }
    
    $datos['imagenes'] = $imagenesSubidas; 
    
    // Llamamos a guardar. Si $datos['id'] existe, el SA y DAO deben actualizar.
    $sa->guardarProducto($datos);

} elseif ($accion === 'borrar') {
    $id = $_POST['id'] ?? $_GET['id'] ?? 0;
    $sa->toggleOferta($id);
}

header("Location: ../productos_gerente.php");
exit;