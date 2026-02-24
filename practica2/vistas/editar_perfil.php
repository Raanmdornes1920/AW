<?php
session_start();
require_once '../static/config.php';

// Comprobación básica: usuario logueado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
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
        <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
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
                            <img src="<?php echo RUTA_IMG; ?>/iconos/lapiz_blanco.png" class="icono-lapiz-img" alt="Editar">
                        </div>
                        
                    </figure>
                    <h2 id="nombre-usuario"><?php echo $_SESSION['usuario']; ?></h2>
                    <img onclick="editarUsername()" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" id="boton-editar-usuario" alt="Editar">
                </div>
                <br>
                <article>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Nombre:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['nombre']; ?></h2>
                        <a href="#" class="enlace-editar">
                            <img onclick="editarNombre()" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-nombre" alt="Editar">
                        </a>
                    </div>
                    <br>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Apellidos:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['apellidos']; ?></h2>
                        <a href="#" class="enlace-editar">
                            <img onclick="editarApellidos()" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-apellidos" alt="Editar">
                        </a>
                    </div>
                    <br>
                    <div class="fila-dato">
                        <h2 class="tipo-dato-usuario">Email:</h2>
                        <h2 class="datos-usuario"><?php echo $_SESSION['email']; ?></h2>
                        <a href="#" class="enlace-editar">
                            <img onclick="editarEmail()" src="<?php echo RUTA_IMG; ?>/iconos/lapiz.png" class="lapiz-negro" id="boton-editar-email" alt="Editar">
                        </a>
                    </div>
                    <br>
                    <div class="centrado">
                        <button id="boton_aceptar" onclick="editarPassword()">Cambiar Contraseña</button>
                        <br><br>
                        <button id="boton_cancelar" onclick="window.location.href='<?php echo RAIZ_APP; ?>/'">Volver</button>
                    </div>
                </article>
            </div>
        </main>
        <!-- Contenido -->
    </body>
</html>