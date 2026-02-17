<?php 
public function login(str $username, str $password) {
    $_SESSION['login'] = true;
    $_SESSION['nombre'] = $username;
}?>
<html lang="es">
    <!-- Header -->
    <head>
        <meta charset="utf-8">
        <title>Login - BISTRO FDI</title>
        <link rel="icon" type="image/png" href="LogosBistroFDI/logo1.svg">
        <link rel="stylesheet" href="default.css">
    </head>
    <!-- Header -->
    <body>
        
        <!-- Contenido -->
        <main class="contenedor-centro-index">
            
            <article id="contenedor-descripcion">
                <h1 id="titulo-descripcion">Bienvenidos a Bistro FDI</h1>
                <?php login('')?>
                
            </article>
        </main>
        <!-- Contenido -->
    </body>
</html>