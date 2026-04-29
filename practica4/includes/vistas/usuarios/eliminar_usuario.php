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
$css = [(RAIZ_APP . "/css/default.css"), (RAIZ_APP . "/css/modales.css")];
$header = (DIR_RAIZ . "/includes/vistas/comun/header.php");
$js = [(RAIZ_APP . "/js/script.js"), (RAIZ_APP . "/js/editar_perfil.js")];
$claseMain = "contenedor-centro-index";

ob_start(); // Capturamos el contenido del include
?>

<h1 id="titulo-descripcion">Eliminar Usuarios</h1>
<div class="contenedor-tabla-usuarios">
    <table class="tabla-usuarios" cellpadding="6">
        <tr>
            <th colspan="6" class="titulo-tabla">
                EMPLEADOS
            </th>
        </tr>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Opciones</th>
        </tr>
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
                    <td><?php echo htmlspecialchars($rolActual); ?></td>
                    <td class="columna-boton-editar">
                        <button onclick="abrirConfirmacionDelete(<?php echo $usuario->id() . ', \'' . $usuario->usuario() . '\'' . ', \'' . $_SESSION['usuario']->usuario() . '\'';?>)" class="boton-taba-usuarios" >Eliminar</button>
                    </td>
                </tr>
            <?php
                    }
                }
                $lista_usuarios->rewind();
            }
        ?>
    </table>
</div>
<br><br>
<div class="contenedor-tabla-usuarios">
    <table class="tabla-usuarios" cellpadding="6">
        <tr>
            <th colspan="6" class="titulo-tabla">
                CLIENTES
            </th>
        </tr>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Opciones</th>
        </tr>
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
                    <td><?php echo htmlspecialchars($rolActual); ?></td>
                    <td class="columna-boton-editar">
                        <button onclick="abrirConfirmacionDelete(<?php echo $usuario->id() . ', \'' . $usuario->usuario() . '\'' . ', \'' . $_SESSION['usuario']->usuario() . '\'';?>)" class="boton-taba-usuarios" >Eliminar</button>
                    </td>
                </tr>
            <?php
                    }
                }
                $lista_usuarios->rewind();
            }
        ?>
    </table>
</div>
<br><br>
<div>
    <a href="<?php echo RUTA_VISTAS . "/usuarios/ajustes_admin.php";?>"><button  class="botones-gestion-usuarios">Volver</button></a>
</div>

<?php
$contenidoPrincipal = ob_get_clean(); // Guardamos el contenido del include en la variable $contenidoPrincipal
ob_start(); // Capturamos el contenido del include
?>
<!-- Confirmacion Eliminar Usuario -->
<div id="modalAdminEliminarusuario" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-del" onclick="CerrarEliminarUsuario()">&times;</span>
        <h3>¿Estás seguro que deseas eliminar al usuario <span id="span-nombre-usuario"></span>?</h3>
        <div id="advertencia-propio-usuario">
            <h3>¡CUIDADO! Estás eliminando tu propio usuario.</h3>
        </div>
        <br>
        <form action="<?php echo 'apoyo/procesarEliminarUsuario.php';?>" id="formEliminarUsuario" method="POST">
            <input type="hidden" id="input-id-eliminar" name="id-usuario" value="">
            <input type="hidden" name="volver" value="<?php echo htmlspecialchars(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : RAIZ_APP . "/"); ?>">
            <input type="hidden" name="modo-admin" value="Verdadero">
            <div class="contenedor-botones">
                <button type="submit" class="boton-guardar">Si</button>
                <button type="button" onclick="CerrarEliminarUsuario()" class="boton-cancelar">No</button>
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