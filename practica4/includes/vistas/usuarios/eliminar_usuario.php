<?php
require_once (__DIR__ . '/../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}
// <!-- DEFINIR tituloPagina, css, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->

$tituloPagina = "Gestionar Usuarios - BISTRO FDI";
$css = [];
$header = (DIR_RAIZ . "/includes/vistas/comun/header.php");
$js = [(RAIZ_APP . "/js/script.js"), (RAIZ_APP . "/js/editar_perfil.js")];
$claseMain = "contenedor-centro-index";

ob_start(); // Capturamos el contenido del include
?>

<div class="d-flex flex-column flex-sm-row justify-content-between gap-3 align-items-sm-center mb-4">
    <h1 class="h2 mb-0">Eliminar Usuarios</h1>
    <a href="<?php echo RUTA_VISTAS . "/usuarios/ajustes_admin.php";?>" class="btn btn-outline-secondary">Volver</a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white"><h2 class="h5 mb-0">Empleados</h2></div>
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
        <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $lista_usuarios = $SA->getListaUsuarios();
            if ($lista_usuarios) {
                foreach ($lista_usuarios as $usuario){
                    $rolActual = $usuario->rol();

                    if($rolActual !== 'cliente'){
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->email()); ?></td>
                    <td><span class="badge text-bg-secondary"><?php echo htmlspecialchars($rolActual); ?></span></td>
                    <td>
                        <button onclick="abrirConfirmacionDelete(<?php echo $usuario->id() . ', \'' . $usuario->usuario() . '\'' . ', \'' . $_SESSION['usuario']->usuario() . '\'';?>)" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </td>
                </tr>
            <?php
                    }
                }
                $lista_usuarios->rewind();
            }
        ?>
        </tbody>
    </table>
    </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white"><h2 class="h5 mb-0">Clientes</h2></div>
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
        <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
            if ($lista_usuarios) {
                foreach ($lista_usuarios as $usuario){
                    $rolActual = $usuario->rol();

                    if($rolActual === 'cliente'){
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                    <td><?php echo htmlspecialchars($usuario->email()); ?></td>
                    <td><span class="badge text-bg-secondary"><?php echo htmlspecialchars($rolActual); ?></span></td>
                    <td>
                        <button onclick="abrirConfirmacionDelete(<?php echo $usuario->id() . ', \'' . $usuario->usuario() . '\'' . ', \'' . $_SESSION['usuario']->usuario() . '\'';?>)" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </td>
                </tr>
            <?php
                    }
                }
                $lista_usuarios->rewind();
            }
        ?>
        </tbody>
    </table>
    </div>
    </div>
</div>

<?php
$contenidoPrincipal = ob_get_clean(); // Guardamos el contenido del include en la variable $contenidoPrincipal
ob_start(); // Capturamos el contenido del include
?>
<!-- Confirmacion Eliminar Usuario -->
<div id="modalAdminEliminarusuario" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-del" onclick="CerrarEliminarUsuario()">&times;</span>
        <h3 class="h4 mb-3">¿Seguro que deseas eliminar al usuario <span id="span-nombre-usuario"></span>?</h3>
        <div id="advertencia-propio-usuario" class="alert alert-danger">
            <strong>¡CUIDADO!</strong> Estás eliminando tu propio usuario.
        </div>
        <form action="<?php echo 'apoyo/procesarEliminarUsuario.php';?>" id="formEliminarUsuario" method="POST">
            <input type="hidden" id="input-id-eliminar" name="id-usuario" value="">
            <input type="hidden" name="volver" value="<?php echo htmlspecialchars(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : RAIZ_APP . "/"); ?>">
            <input type="hidden" name="modo-admin" value="Verdadero">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <button type="submit" class="btn btn-danger">Sí</button>
                <button type="button" onclick="CerrarEliminarUsuario()" class="btn btn-outline-secondary">No</button>
            </div>
        </form>
    </div>
</div>
<!-- Confirmacion Eliminar Usuario -->
<!-- Errores -->
<?php
if (isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] !== "Ninguno"){
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    echo '<h3>Error al eliminar al usuario</h3>';
    echo '<p>'.$_SESSION['error_editar_perfil'].'</p>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_editar_perfil']);
}?>
<?php
if (isset($_SESSION['error_crear_perfil']) && $_SESSION['error_crear_perfil'] !== "Ninguno"){
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    echo '<h3>Error al crear perfil</h3>';
    echo '<p>'.$_SESSION['error_crear_perfil'].'</p>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_crear_perfil']);
}?>
<!-- Errores -->
<!-- Confirmaciones -->
<?php
if(isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] === "Ninguno"){
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    if (isset($_SESSION['cambio'])) {
        echo '<h3>Se ha eliminado al usuario '. $_SESSION['cambio'] .'</h3>';
    }
    echo '</div>';
    echo '</div>';
    unset($_SESSION['cambio']);
    unset($_SESSION['error_editar_perfil']);
}

$contenidoAdicional = ob_get_clean(); // Guardamos el contenido del include en la variable $contenidoAdicional

require("../comun/plantilla.php");
?>
