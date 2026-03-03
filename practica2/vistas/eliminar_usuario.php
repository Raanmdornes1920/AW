<?php 
session_start(); 
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['rol'] !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}

$sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios WHERE rol != 'cliente' ORDER BY rol DESC, id";
$resultado_empleados = mysqli_query($db_connection, $sql);

$sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios WHERE rol = 'cliente' ORDER BY rol DESC, id";
$resultado_clientes = mysqli_query($db_connection, $sql);
?>

<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Gestionar Usuarios - BISTRO FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/modales.css">
    </head>
    <body>
        <!-- Header -->
        <?php include '../static/header.php'; ?>
        <!-- Header -->
        
        <!-- Contenido -->
        <main class="contenedor-centro-index">
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
                    <?php if ($resultado_empleados) { ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado_empleados)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                                <td class="columna-boton-editar">
                                    <button onclick="abrirConfirmacionDelete(<?php echo $fila['id'] . ', \'' . $fila['nombre_usuario'] . '\'' . ', \'' . $_SESSION['usuario'] . '\'';?>)" class="boton-taba-usuarios" >Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
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
                    <?php if ($resultado_clientes) { ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado_clientes)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                                <td class="columna-boton-editar">
                                    <button onclick="abrirConfirmacionDelete(<?php echo $fila['id'] . ', \'' . $fila['nombre_usuario'] . '\'' . ', \'' . $_SESSION['usuario'] . '\'';?>)" class="boton-taba-usuarios" >Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div>
            <br><br>
            <div>
                <a href="<?php echo RUTA_VISTAS . "/ajustes_admin.php";?>"><button  class="botones-gestion-usuarios">Volver</button></a>
            </div>
        </main>
        <!-- Confirmacion Eliminar Usuario -->
        <div id="modalAdminEliminarusuario" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal-del" onclick="CerrarEliminarUsuario()">&times;</span>
                <h3>¿Estás seguro que deseas eliminar al usuario <span id="span-nombre-usuario"></span>?</h3>
                <div id="advertencia-propio-usuario">
                    <h3>¡CUIDADO! Estás eliminando tu propio usuario.</h3>
                </div>
                <br>
                <form action="<?php echo RUTA_STATIC . '/procesarEliminarUsuario.php';?>" id="formEliminarUsuario" method="POST">
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
        ?>
        <!-- Confirmaciones -->
        <!-- Contenido -->
        <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
        <script src="<?php echo RAIZ_APP; ?>/js/editar_perfil.js"></script>
    </body>
</html>