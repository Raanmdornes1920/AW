<?php
require_once(__DIR__ . '/../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/");
    exit();
}

$tituloPagina = "Gestionar Usuarios - BISTRO FDI";
$css = [];
$js = [(RAIZ_APP . "/js/script.js"), (RAIZ_APP . "/js/editar_perfil.js")];
$claseMain = "contenedor-centro-index";
$header = (__DIR__ . "/../comun/header.php");

ob_start(); // Capturamos el contenido del include
?>

<div class="d-flex flex-column flex-sm-row justify-content-between gap-3 align-items-sm-center mb-4">
    <h1 class="h2 mb-0">Gestión de Usuarios</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo RUTA_VISTAS . "/usuarios/crear_usuario.php"; ?>" class="btn btn-success">Crear usuario</a>
        <a href="<?php echo RUTA_VISTAS . "/usuarios/eliminar_usuario.php"; ?>" class="btn btn-outline-danger">Eliminar usuario</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h2 class="h5 mb-0">Empleados</h2>
    </div>
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
        <tbody id="body-empleados">
        <?php
        $usuarios_por_id = [];
        $lista_usuarios = $SA->getListaUsuarios();
        if ($lista_usuarios) {
            foreach ($lista_usuarios as $usuario) {
                $rolActual = $usuario->rol();

                if ($rolActual !== 'cliente') {
                    $usuarios_por_id[$usuario->id()] = $usuario;
        ?>
                    <tr id="fila-usuario-<?php echo $usuario->id(); ?>" 
                        data-rol="<?php echo htmlspecialchars($rolActual); ?>" 
                        data-id="<?php echo $usuario->id(); ?>">
                        
                        <td class="col-usuario"><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                        <td class="col-nombre"><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                        <td class="col-apellidos"><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                        <td class="col-email"><?php echo htmlspecialchars($usuario->email()); ?></td>
                        <td class="col-rol">
                            <span class="badge text-bg-secondary"><?php echo htmlspecialchars($rolActual); ?></span>
                        </td>
                        <td>
                            <button id="btn-editar-<?php echo $usuario->id(); ?>" 
                                    class="btn btn-sm btn-outline-primary" 
                                    onclick='abrirModalEditarUsuario(<?php echo $usuario->id() . ", " . json_encode($usuario); ?>)'>
                                Editar
                            </button>
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
    <div class="card-header bg-white">
        <h2 class="h5 mb-0">Clientes</h2>
    </div>
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
        <tbody id="body-clientes">
        <?php
        if ($lista_usuarios) {
            foreach ($lista_usuarios as $usuario) {
                $rolActual = $usuario->rol();

                if ($rolActual === 'cliente') {
                    $usuarios_por_id[$usuario->id()] = $usuario;
        ?>
                    <tr id="fila-usuario-<?php echo $usuario->id(); ?>" data-rol="cliente" data-id="<?php echo $usuario->id(); ?>">
                        <td class="col-usuario"><?php echo htmlspecialchars($usuario->usuario()); ?></td>
                        <td class="col-nombre"><?php echo htmlspecialchars($usuario->nombre()); ?></td>
                        <td class="col-apellidos"><?php echo htmlspecialchars($usuario->apellidos()); ?></td>
                        <td class="col-email"><?php echo htmlspecialchars($usuario->email()); ?></td>
                        <td class="col-rol">
                            <span class="badge text-bg-secondary">cliente</span>
                        </td>
                        <td>
                            <button id="btn-editar-<?php echo $usuario->id(); ?>" 
                                    class="btn btn-sm btn-outline-primary" 
                                    onclick='abrirModalEditarUsuario(<?php echo $usuario->id() . ", " . json_encode($usuario); ?>)'>
                                Editar
                            </button>
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
$contenidoPrincipal = ob_get_clean(); // Guardamos contenido del include

ob_start();
?>
<!-- Interfaz para editar usuarios -->
<section id="contenedor-centro-edit-admin">
     <!-- Mostrar mensaje de error o éxito si existe -->
    <div id="alerta-perfil" class="alert alert-dismissible fade mx-auto mb-3 d-none" style="max-width: 760px;" role="alert">
        <span id="alerta-mensaje"></span>
        <button type="button" class="btn-close" onclick="cerrarAlertaBootstrap()"></button>
    </div>
    <div class="perfil-container-edit-admin">
        <span class="cerrar-modal-edit-admin">&times;</span>
        <h1 class="h3 mb-4">Editar Perfil</h1>
        <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
                <figure id="contenedor-avatar" class="position-relative d-inline-block rounded-circle overflow-hidden shadow-sm" style="width: 150px; height: 150px; cursor: pointer;" onclick="abrirModalAdminAvatar()">
                    <img id="Logo-Usuario" class="w-100 h-100 img-fluid object-fit-cover" src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['usuario']->avatar(); ?>" alt="Logo de Usuario">
                    <div class="capa-editar">
                        <i class="bi bi-pencil-fill text-white fs-2"></i>
                    </div>
                </figure>
                
                <div class="d-lg-none position-absolute bottom-0 end-0 border-0 bg-white rounded-circle d-flex align-items-center justify-content-center" 
                    style="margin-bottom: 10px; margin-left: 10px; width: 150px; height: 150px; border: 3px solid white; transform: translate(-5%, -5%); pointer-events: none; --bs-bg-opacity: .40;">
                    <i class="bi bi-pencil-fill text-back" style="font-size: 3rem; margin-left: 10px;"></i>
                </div>
            </div>

            <div class="d-block position-relative d-inline-block mt-3">
                <h2 id="usuario-usuario-edit" class="h4 m-0 d-inline-block"><?php echo htmlspecialchars($_SESSION['usuario']->usuario()); ?></h2>
                <button id="btn-editar-usuario" class="btn btn-sm btn-outline-none position-absolute top-50 translate-middle-y ms-2 btn-editar-usuario" onclick="abrirModalAdmin('Usuario')"><i class="bi bi-pencil-fill fs-5"></i></button>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div><strong>Nombre:</strong> <span id="nombre-usuario-edit"></span></div>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAdmin('Nombre')">Editar</button>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div><strong>Apellidos:</strong> <span id="apellidos-usuario-edit"></span></div>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAdmin('Apellidos')">Editar</button>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div><strong>Email:</strong> <span id="email-usuario-edit"></span></div>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAdmin('Email')">Editar</button>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div><strong>Rol:</strong> <span id="rol-usuario-edit"></span></div>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAdminRol()">Editar</button>
            </div>
            <div class="mt-4">
                <button class="btn btn-warning" onclick="abrirModalAdminPassword()">Resetear contraseña</button>
            </div>
        </div>
    </div>
</section>
<!-- Interfaz para editar usuarios -->
<!-- Editar Avatar -->
<div id="modalAdminEditarAvatar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-avatar">&times;</span>
        <h3 class="h4 mb-3">Editar Avatar</h3>
        <form onsubmit="enviarDatosFormularioAdmin(event)" class="formEditar" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Avatar">
            <div class="row row-cols-3 row-cols-sm-4 g-3 mb-3">
                <?php $primerAvatarSeleccionado = true;
                foreach (IMAGENES_BASE as $indice => $archivo): ?>
                    <label class="opcion-avatar col text-center">
                        <input class="btn-check" type="radio" name="foto_perfil" value="<?= $archivo; ?>" <?= $primerAvatarSeleccionado ? 'checked' : ''; $primerAvatarSeleccionado = false;?>>
                        <span class="btn btn-outline-secondary w-100 p-2">
                            <img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG; ?>/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                        </span>
                    </label>
                <?php endforeach; ?>

                <label class="opcion-avatar col text-center">
                    <input class="btn-check" type="radio" name="foto_perfil" value="custom" id="radio-custom">
                    <span class="btn btn-outline-secondary w-100 p-3">Subir archivo</span>
                </label>
            </div>

            <div id="archivo-avatar" style="display: none;">
                <input class="form-control mb-3" type="file" id="avatar-nuevo" name="foto_perfil" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Avatar -->
<!-- Editar Datos -->
<div id="modalAdminEditar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <h3 class="h4 mb-3">Editar <span id="campo-a-editar"></span></h3>
        <form onsubmit="enviarDatosFormularioAdmin(event)" class="formEditar" method="POST">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" id="campo-editar" name="campo-editar" value="error">
            <label class="form-label" id="label-nuevo-valor"></label>
            <input class="form-control mb-3" type="text" id="nuevo-valor" name="nuevo-valor" required>
            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Datos -->
<!-- Editar Rol -->
<div id="modalAdminEditarRol" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-rol">&times;</span>
        <h3 class="h4 mb-3">Editar Rol</h3>
        <form onsubmit="enviarDatosFormularioAdmin(event)" class="formEditar" method="POST" novalidate>
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Rol">
            <label class="form-label">Rol</label>
            <select class="form-select mb-3" name="nuevo-valor" id="select-rol-usuario">
                <option value="gerente">Gerente</option>
                <option value="cocinero">Cocinero</option>
                <option value="camarero">Camarero</option>
                <option value="cliente">Cliente</option>
            </select>
            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Editar Rol -->
<!-- Resetear Password -->
<div id="modalAdminEditarPassword" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-pass">&times;</span>
        <h3 class="h4 mb-3">¿Seguro que deseas resetear la contraseña de <span id="usuario-reset-contrasena"></span>?</h3>
        <form onsubmit="enviarDatosFormularioAdmin(event)" id="formEditarPassword" method="POST">
            <input type="hidden" class="input-id-usuario" name="id-usuario" value="">
            <input type="hidden" name="campo-editar" value="Password">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <button type="submit" class="btn btn-warning">Sí</button>
                <button type="button" onclick="btnCerrarPassword.click()" class="btn btn-outline-secondary">No</button>
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
