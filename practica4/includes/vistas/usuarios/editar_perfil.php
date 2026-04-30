<?php
require_once '../../config.php';
session_start();

// Comprobación básica: usuario logueado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: " .  RAIZ_APP);
    exit();
}

$tituloPagina = "Perfil- BISTRO FDI";
$css = [];
$header = (__DIR__ . "/../comun/header.php");
$claseMain = "contenedor-centro";
ob_start(); ?>

<div class="card shadow-sm mx-auto" style="max-width: 760px;">
    <div class="card-body p-4">
    <h1 class="h3 mb-4">Perfil</h1>
    <div class="text-center mb-4">
        <img id="Logo-Usuario" src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['usuario']->avatar(); ?>" alt="Logo de Usuario">
        <h2 class="h4 mt-3"><?php echo htmlspecialchars($_SESSION['usuario']->usuario()); ?></h2>
        <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAvatar()">Cambiar avatar</button>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModal('Usuario', '<?php echo base64_encode($_SESSION['usuario']->usuario()); ?>')">Editar usuario</button>
        </div>
    </div>
    <article>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario']->nombre()); ?></div>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModal('Nombre', '<?php echo base64_encode($_SESSION['usuario']->nombre()); ?>')">Editar</button>
        </div>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div><strong>Apellidos:</strong> <?php echo htmlspecialchars($_SESSION['usuario']->apellidos()); ?></div>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModal('Apellidos', '<?php echo base64_encode($_SESSION['usuario']->apellidos()); ?>')">Editar</button>
        </div>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario']->email()); ?></div>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModal('Email', '<?php echo base64_encode($_SESSION['usuario']->email()); ?>')">Editar</button>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button class="btn btn-warning" onclick="abrirModalPassword()">Cambiar contraseña</button>
            <button class="btn btn-outline-secondary" onclick="window.location.href='<?php echo RAIZ_APP; ?>/'">Volver</button>
        </div>
    </article>
    </div>
</div>
<?php
$contenidoPrincipal = ob_get_clean();
ob_start(); ?>

<div id="modalEditarAvatar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-avatar">&times;</span>
        <h3 class="h4 mb-3">Editar Avatar</h3>
        <form action="apoyo/editar_avatar.php" id="formEditar" method="POST" enctype="multipart/form-data">

            <div class="row row-cols-3 row-cols-sm-4 g-3 mb-3">
                <?php if($_SESSION['usuario']->rol() === 'cliente'){
                        foreach (AVATARES_INICIALES as $indice => $archivo): ?>
                        <label class="opcion-avatar col text-center">
                            <input class="btn-check" type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                            <span class="btn btn-outline-secondary w-100 p-2"><img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG . '/perfiles/' . $archivo; ?>" alt="Avatar <?= $indice; ?>"></span>
                        </label>
                    <?php endforeach;
                }
                elseif($_SESSION['usuario']->rol() === 'camarero'){
                        foreach (AVATARES_CAMARERO as $indice => $archivo): ?>
                        <label class="opcion-avatar col text-center">
                            <input class="btn-check" type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                            <span class="btn btn-outline-secondary w-100 p-2"><img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG . '/perfiles/' . $archivo; ?>" alt="Avatar <?= $indice; ?>"></span>
                        </label>
                    <?php endforeach;
                }elseif($_SESSION['usuario']->rol() === 'cocinero'){
                        foreach (AVATARES_COCINERO as $indice => $archivo): ?>
                        <label class="opcion-avatar col text-center">
                            <input class="btn-check" type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                            <span class="btn btn-outline-secondary w-100 p-2"><img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG . '/perfiles/' . $archivo; ?>" alt="Avatar <?= $indice; ?>"></span>
                        </label>
                    <?php endforeach;
                }else{
                        foreach (IMAGENES_BASE as $indice => $archivo): ?>
                        <label class="opcion-avatar col text-center">
                            <input class="btn-check" type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                            <span class="btn btn-outline-secondary w-100 p-2"><img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG . '/perfiles/' . $archivo; ?>" alt="Avatar <?= $indice; ?>"></span>
                        </label>
                    <?php endforeach;
                }?>
                <label class="opcion-avatar col text-center">
                    <input class="btn-check" type="radio" name="foto_perfil" value="custom" id="radio-custom">
                    <span class="btn btn-outline-secondary w-100 p-3">Subir archivo</span>
                </label>
            </div>

            <div id="archivo-avatar">
                <input class="form-control mb-3" type="file" id="avatar-nuevo" name="foto_perfil" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<div id="modalEditar" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <h3 class="h4 mb-3">Editar <span id="campo-a-editar"></span></h3>
        <form action="apoyo/editar_dato.php" id="formEditar" method="POST">
            <input type="hidden" id="campo-editar" autocomplete="off" name="campo-editar" value="error">
            <label class="form-label" id="label-nuevo-valor"></label>
            <input class="form-control mb-3" type="text" id="nuevo-valor" name="nuevo-valor" required>
            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<div id="modalEditarPassword" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal-pass">&times;</span>
        <h3 class="h4 mb-3">Editar Contraseña</h3>
        <form action="apoyo/editar_password.php" id="formEditarPassword" method="POST">
            <input type="text" autocomplete="username" name="usuario" value="<?php echo $_SESSION['usuario']->usuario(); ?>" hidden>
            <label class="form-label">Contraseña actual</label>
            <input class="form-control mb-3" type="password" id="contrasena" autocomplete="current-password" name="contrasena" placeholder="Contraseña actual" required>
            <label class="form-label">Nueva contraseña</label>
            <input class="form-control mb-3" type="password" id="nueva-contrasena" autocomplete="new-password" name="nueva-contrasena" placeholder="Nueva contraseña" required>
            <label class="form-label">Confirmar nueva contraseña</label>
            <input class="form-control mb-3" type="password" id="confirmar-contrasena" autocomplete="new-password" name="confirmar-contrasena" placeholder="Confirmar nueva contraseña" required>
            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </form>
    </div>
</div>
<!-- Contenido -->
<?php

if (isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] !== "Ninguno"){
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    echo '<h3>Error al editar perfil</h3>';
    echo '<p>'.$_SESSION['error_editar_perfil'].'</p>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_editar_perfil']);
}
else if(isset($_SESSION['error_editar_perfil']) && $_SESSION['error_editar_perfil'] === "Ninguno"){
    echo '<div id="modalError" class="modal">';
    echo '<div class="modal-contenido">';
    echo '<span class="cerrar-modal-error">&times;</span>';
    if ($_SESSION['cambio'] === 'Password') {
        echo '<h3>Contraseña actualizada correctamente</h3>';
    } else {
        echo '<h3>' . $_SESSION['cambio'] . ' actualizado correctamente</h3>';
    }
    echo '</div>';
    echo '</div>';
    unset($_SESSION['cambio']);
    unset($_SESSION['error_editar_perfil']);
}
?>

<?php
$contenidoAdicional = ob_get_clean();
$js = [(RAIZ_APP . "/js/script.js"), (RAIZ_APP . "/js/editar_perfil.js")];

require("../comun/plantilla.php");
?>
