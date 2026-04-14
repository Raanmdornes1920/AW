<?php
require_once(__DIR__ . '/../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/");
    exit();
}

$tituloPagina = "Gestionar Usuarios - BISTRO FDI";
$css = [(RUTA_CSS . "/default.css"), (RUTA_CSS . "/modales.css")];
$js = [(RAIZ_APP . "/js/script.js"), (RAIZ_APP . "/js/editar_perfil.js")];
$claseMain = "contenedor-centro-index";
$header = (__DIR__ . "/../comun/header.php");

ob_start(); // Capturamos el contenido del include 
?>

<h1 id="titulo-descripcion">Gestión de Usuarios</h1>

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
        $usuarios_por_id = [];
        $lista_usuarios = $SA->getListaUsuarios();
        if ($lista_usuarios) {
            foreach ($lista_usuarios as $usuario) {
                $rolActual = $usuario->rol();

                if ($rolActual !== 'cliente') {
                    $usuarios_por_id[$usuario->id()] = $usuario;
        ?>
                    <tr>
                        <td data-label="Usuario"><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                        <td data-label="Nombre"><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                        <td data-label="Apellidos"><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($usuario->email()); ?></td>
                        <td data-label="Rol"><?php echo htmlspecialchars($rolActual); ?></td>
                        <td class="columna-boton-editar">
                            <button class="boton-taba-usuarios" onclick='abrirModalEditarUsuario(<?php echo $usuario->id() . ", " . json_encode($usuario); ?>)'>Editar</button>
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
            foreach ($lista_usuarios as $usuario) {
                $rolActual = $usuario->rol();

                if ($rolActual === 'cliente') {
                    $usuarios_por_id[$usuario->id()] = $usuario;
        ?>
                    <tr>
                        <td data-label="Usuario"><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                        <td data-label="Nombre"><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                        <td data-label="Apellidos"><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($usuario->email()); ?></td>
                        <td data-label="Rol"><?php echo htmlspecialchars($rolActual); ?></td>
                        <td class="columna-boton-editar">
                            <button class="boton-taba-usuarios" onclick='abrirModalEditarUsuario(<?php echo $usuario->id() . ", " . json_encode($usuario); ?>)'>Editar</button>
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
    <a href="<?php echo RUTA_VISTAS . "/usuarios/crear_usuario.php"; ?>"><button class="botones-gestion-usuarios">Crear Usuario</button></a>
    <a href="<?php echo RUTA_VISTAS . "/usuarios/eliminar_usuario.php"; ?>"><button class="botones-gestion-usuarios">Eliminar Usuario</button></a>
</div>

<?php
$contenidoPrincipal = ob_get_clean(); // Guardamos contenido del include

ob_start();
?>
<!-- Interfaz para editar usuarios -->
<section id="contenedor-centro-edit-admin">
    <div class="perfil-container-edit-admin">
        <span class="cerrar-modal-edit-admin">&times;</span>
        <h1 id="titulo-perfil">Editar Perfil</h1>
        <div class="imagen-usarname-container">
            <figure id="contenedor-avatar">
                <img id="Logo-Usuario" src="" alt="Logo de Usuario">
                <div class="capa-editar">
                    <a onclick="abrirModalAdminAvatar()"><img src="<?php echo RUTA_IMG; ?>/iconos/lapiz_blanco.png" class="icono-lapiz-img" alt="Editar"></a>
                </div>

            </figure>
            <h2 id="nombre-usuario"></h2>
            <img onclick="abrirModalAdmin('Usuario')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" id="boton-editar-usuario" alt="Editar">
        </div>
        <br>
        <div>
            <div class="fila-dato">
                <h2 class="tipo-dato-usuario">Nombre:</h2>
                <h2 id="nombre-usuario-edit" class="datos-usuario"></h2>
                <img onclick="abrirModalAdmin('Nombre')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-nombre" alt="Editar">
            </div>
            <br>
            <div class="fila-dato">
                <h2 class="tipo-dato-usuario">Apellidos:</h2>
                <h2 id="apellidos-usuario-edit" class="datos-usuario"></h2>
                <img onclick="abrirModalAdmin('Apellidos')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-apellidos" alt="Editar">
            </div>
            <br>
            <div class="fila-dato">
                <h2 class="tipo-dato-usuario">Email:</h2>
                <h2 id="email-usuario-edit" class="datos-usuario"></h2>
                <img onclick="abrirModalAdmin('Email')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-email" alt="Editar">
            </div>
            <br>
            <div class="fila-dato">
                <h2 class="tipo-dato-usuario">Rol:</h2>
                <h2 id="rol-usuario-edit" class="datos-usuario"></h2>
                <img onclick="abrirModalAdminRol()" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-rol" alt="Editar">
            </div>
            <br>
            <div class="centrado">
                <button id="boton_aceptar" onclick="abrirModalAdminPassword()">Resetear Contraseña</button>
                <br>
            </div>
        </div>
    </div>
</section>
<!-- Interfaz para editar usuarios -->
<!-- Editar Avatar -->
<div id="modalAdminEditarAvatar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-avatar">&times;</span>
        <h3>Editar Avatar</h3>
        <form action="apoyo/admin_edit.php" class="formEditar" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Avatar">
            <div class="seleccion-avatares">
                <?php foreach (IMAGENES_BASE as $indice => $archivo): ?>
                    <label class="opcion-avatar">
                        <img class="opcion-imagen-avatar" src="<?php echo RUTA_IMG; ?>/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                        <input type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                    </label>
                <?php endforeach; ?>

                <label class="opcion-avatar">
                    <div class="cuadro-subir-archivo">
                        <p>Elegir<br>Archivo</p>
                    </div>
                    <input type="radio" name="foto_perfil" value="custom" id="radio-custom">
                </label>
            </div>

            <div id="archivo-avatar">
                <br>
                <input type="file" id="avatar-nuevo" name="foto_perfil" accept="image/*">
                <br>
                <br>
                <br>
            </div>

            <button type="submit" class="boton-guardar">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Avatar -->
<!-- Editar Datos -->
<div id="modalAdminEditar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <h3>Editar <span id="campo-a-editar"></span></h3>
        <form action="apoyo/admin_edit.php" class="formEditar" method="POST">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" id="campo-editar" name="campo-editar" value="error">
            <label id="label-nuevo-valor"></label>
            <input type="text" id="nuevo-valor" name="nuevo-valor" required>
            <button type="submit" class="boton-guardar">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Datos -->
<!-- Editar Rol -->
<div id="modalAdminEditarRol" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-rol">&times;</span>
        <h3>Editar Rol</h3>
        <form action="apoyo/admin_edit.php" class="formEditar" method="POST" novalidate>
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Rol">
            <label>Rol:</label>
            <select name="nuevo-valor" id="select-rol-usuario">
                <option value="gerente">Gerente</option>
                <option value="cocinero">Cocinero</option>
                <option value="camarero">Camarero</option>
                <option value="cliente">Cliente</option>
            </select>
            <button type="submit" class="boton-guardar">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Rol -->
<!-- Resetear Password -->
<div id="modalAdminEditarPassword" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-pass">&times;</span>
        <h3>Estas seguro que deseas resetear la contraseña de <span id="usuario-reset-contrasena"></span>?</h3>
        <br>
        <form action="apoyo/admin_edit.php" id="formEditarPassword" method="POST">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Password">
            <div class="contenedor-botones">
                <button type="submit" class="boton-guardar">Si</button>
                <button type="button" onclick="btnCerrarPassword.click()" class="boton-cancelar">No</button>
            </div>
        </form>
    </div>
</div>
<!-- Resetear Password -->
<!-- Errores -->
<?php
if (isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] !== "Ninguno") {
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    echo '<h3>Error al editar perfil</h3>';
    echo '<p>' . $_SESSION['error_editar_perfil'] . '</p>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_editar_perfil']);
} ?>
<?php
if (isset($_SESSION['error_crear_perfil']) && $_SESSION['error_crear_perfil'] !== "Ninguno") {
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    echo '<h3>Error al crear perfil</h3>';
    echo '<p>' . $_SESSION['error_crear_perfil'] . '</p>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_crear_perfil']);
} ?>
<!-- Errores -->
<!-- Confirmaciones -->
<?php
if (isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] === "Ninguno") {
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    if (isset($_SESSION['cambio']) && $_SESSION['cambio'] === 'Password') {
        echo '<h3>Se ha realizado la modificacion correctamente</h3>';
    } elseif (isset($_SESSION['cambio']) && $_SESSION['cambio'] === 'Crear Usuario') {
        echo '<h3>Usuario creado correctamente</h3>';
    } else {
        echo '<h3>' . $_SESSION['cambio'] . ' actualizado correctamente</h3>';
    }
    echo '</div>';
    echo '</div>';
    unset($_SESSION['cambio']);
    unset($_SESSION['error_editar_perfil']);
}
?>
<!-- Confirmaciones -->
<?php
$contenidoAdicional = ob_get_clean();

ob_start();
?><script>
    var diccionario_usuarios = <?php echo json_encode($usuarios_por_id); ?>;
</script><?php
            $scirptManual = ob_get_clean();

            require("../comun/plantilla.php");
            ?>