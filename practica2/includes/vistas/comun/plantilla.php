<!-- DEFINIR tituloPagina, css, header, contenidoPrincipal y js  antes del include -->

<!DOCTYPE html>
<html>
    <head>
        <title><?= $tituloPagina ?></title>
        <meta charset="utf-8"> 
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <?php foreach ($css as $ruta): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $ruta; ?>" />
        <?php endforeach; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_CSS . '/estilo.css' ?>" />
    </head>
    <body>
        <div id="contenedor">
            <?php
                include($header);
            ?>

            <main>
                <?= $contenidoPrincipal ?>
            </main>

            <?php
                //include(RUTA_COMUN . '/footer.php');
                foreach ($js as $ruta):?>
                    <script src="<?php echo $ruta; ?>"></script>
            <?php endforeach; ?>
        </div> 
    </body>
</html>