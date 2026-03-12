<!-- DEFINIR tituloPagina, css, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html>
    <head>
        <title><?= $tituloPagina ?></title>
        <meta charset="utf-8"> 
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <?php foreach ($css as $ruta): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $ruta; ?>" />
        <?php endforeach; ?>
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