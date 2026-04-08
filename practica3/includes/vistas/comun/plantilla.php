<!-- DEFINIR tituloPagina, css, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html>
    <head>
        <title><?= $tituloPagina ?></title>
        <meta charset="utf-8"> 
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/movil.css" media="only screen and (max-width: 600px)" />
        <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/tablet.css" media="only screen and (min-width: 601px) and (max-width: 1023px)" />
        <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/pc.css" media="only screen and (min-width: 1024px)" />
    </head>
    <body>
        <?php
            include($header);
        ?>

        <main id="contenedor" class="<?= $claseMain ?? '' ?>">
            <?= $contenidoPrincipal ?>
        </main>
        <?php if(isset($contenidoAdicional)){echo $contenidoAdicional;}?>
        <?php
            include(__DIR__ . '/footer.php');
            if(isset($scirptManual)){echo $scirptManual;}
            foreach ($js as $ruta):?>
                <script src="<?=$ruta?>"></script>
        <?php endforeach; ?>
    </body>
</html>