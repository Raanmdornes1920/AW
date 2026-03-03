<?php
session_start();
require_once '../static/config.php';

// Comprobación básica: usuario logueado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../");
    exit();
}
?>
<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Perfil- BISTRO FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/modales.css">
    </head>
    <body>
        <!-- Header -->
        <?php include '../static/header.php'; ?>
        <!-- Header -->
        
        <!-- Contenido -->
        <main class="contenedor-centro">    
            <div class="perfil-container">
                <h1 id="titulo-perfil">Perfil</h1>
                <div class="imagen-usarname-container">
                    <figure id="contenedor-avatar">
                        <img id="Logo-Usuario" src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['foto_perfil']; ?>" alt="Logo de Usuario">
                        <div class="capa-editar">
                            <a onclick="abrirModalAvatar()"><img src="<?php echo RUTA_IMG; ?>/iconos/lapiz_blanco.png" class="icono-lapiz-img" alt="Editar"></a>
                        </div>
                        
                    </figure>
                    <h2 id="nombre-usuario"><?php echo $_SESSION['usuario']; ?></h2>
                    <img onclick="abrirModal('Usuario', '<?php echo base64_encode($_SESSION['usuario']); ?>')"src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" id="boton-editar-usuario" alt="Editar">
                </div>
                <br>
                <article>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Nombre:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['nombre']; ?></h2>
                        <img onclick="abrirModal('Nombre', '<?php echo base64_encode($_SESSION['nombre']); ?>')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-nombre" alt="Editar">
                    </div>
                    <br>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Apellidos:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['apellidos']; ?></h2>
                        <img onclick="abrirModal('Apellidos', '<?php echo base64_encode($_SESSION['apellidos']); ?>')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-apellidos" alt="Editar">
                    </div>
                    <br>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Email:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['email']; ?></h2>
                        <img onclick="abrirModal('Email', '<?php echo base64_encode($_SESSION['email']); ?>')" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-email" alt="Editar">
                    </div>
                    <br>
                    <div class="centrado">
                        <button id="boton_aceptar" onclick="abrirModalPassword()">Cambiar Contraseña</button>
                        <br><br>
                        <button id="boton_cancelar" onclick="window.location.href='<?php echo RAIZ_APP; ?>/'">Volver</button>
                    </div>
                </article>
            </div>
        </main>
        
        <div id="modalEditarAvatar" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal-avatar">&times;</span>
                <h3>Editar Avatar</h3>
                <form action="../static/editar_avatar.php" id="formEditar" method="POST" enctype="multipart/form-data">
                
                    <div class="seleccion-avatares">
                        <?php if($_SESSION['rol'] === 'cliente'){
                                foreach (AVATARES_INICIALES as $indice => $archivo): ?>
                                <label class="opcion-avatar">
                                    <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                                    <input type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                                </label>
                            <?php endforeach;
                        }
                        elseif($_SESSION['rol'] === 'camarero'){
                                foreach (AVATARES_CAMARERO as $indice => $archivo): ?>
                                <label class="opcion-avatar">
                                    <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                                    <input type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                                </label>
                            <?php endforeach;
                        }elseif($_SESSION['rol'] === 'cocinero'){
                                foreach (AVATARES_COCINERO as $indice => $archivo): ?>
                                <label class="opcion-avatar">
                                    <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                                    <input type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                                </label>
                            <?php endforeach;
                        }else{
                                foreach (IMAGENES_BASE as $indice => $archivo): ?>
                                <label class="opcion-avatar">
                                    <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                                    <input type="radio" name="foto_perfil" value="<?= $archivo; ?>">
                                </label>
                            <?php endforeach;
                        }?>
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
        <div id="modalEditar" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal">&times;</span>
                <h3>Editar <span id="campo-a-editar"></span></h3>
                <form action="../static/editar_dato.php" id="formEditar" method="POST">
                    <input type="hidden" id="campo-editar" name="campo-editar" value="error">
                    <label id="label-nuevo-valor"></label>
                    <input type="text" id="nuevo-valor" name="nuevo-valor" required>
                    <button type="submit" class="boton-guardar">Guardar cambios</button>
                </form>
            </div>
        </div>
        <div id="modalEditarPassword" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal-pass">&times;</span>
                <h3>Editar Contraseña</h3>
                <form action="../static/editar_password.php" id="formEditarPassword" method="POST">
                    <label>Contraseña Actual:</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña actual" required>
                    <label>Nueva Contraseña:</label>
                    <input type="password" id="nueva-contrasena" name="nueva-contrasena" placeholder="Nueva contraseña"required>
                    <label>Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar-contrasena" name="confirmar-contrasena" placeholder="Confirmar nueva contraseña"required><br>
                    <button type="submit" class="boton-guardar">Guardar cambios</button>
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
            if (isset($_SESSION['cambio']) && $_SESSION['cambio'] === 'Password') {
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
        <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
        <script src="<?php echo RAIZ_APP; ?>/js/editar_perfil.js"></script>
    </body>
</html>