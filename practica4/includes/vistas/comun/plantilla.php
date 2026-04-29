<<<<<<< HEAD
<!-- DEFINIR tituloPagina, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html lang="es">
=======
<!-- DEFINIR tituloPagina, css, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html>
>>>>>>> angela

<head>
    <title><?= $tituloPagina ?></title>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<<<<<<< HEAD
    <!-- Bootstrap 5.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (opcional, por si los usamos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
=======
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/movil.css" media="only screen and (max-width: 600px)" />
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/tablet.css" media="only screen and (min-width: 601px) and (max-width: 1023px)" />
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/pc.css" media="only screen and (min-width: 1024px)" />
>>>>>>> angela

    <?php
    // Incluimos los archivos js
    foreach ($js as $ruta):
<<<<<<< HEAD
        echo '<script src="' . $ruta . '" defer></script>';
=======
        echo '<script src="'. $ruta . '"></script>';
>>>>>>> angela
    endforeach;
    ?>
</head>

<<<<<<< HEAD
<body class="d-flex flex-column min-vh-100 bg-light">
=======
<body>
>>>>>>> angela
    <?php
    include($header);
    ?>

<<<<<<< HEAD
    <main id="contenedor" class="flex-grow-1 container py-4 mt-5 <?= $claseMain ?? '' ?>">
        <?= $contenidoPrincipal ?>
    </main>

    <?php if (isset($contenidoAdicional)) {
        echo $contenidoAdicional;
    } ?>

=======
    <main id="contenedor" class="<?= $claseMain ?? '' ?>">
        <?= $contenidoPrincipal ?>
    </main>
    <?php if (isset($contenidoAdicional)) {
        echo $contenidoAdicional;
    } ?>
>>>>>>> angela
    <?php
    include(__DIR__ . '/footer.php');
    if (isset($scirptManual)) {
        echo $scirptManual;
    }
    ?>
<<<<<<< HEAD

    <!-- Bootstrap 5.3 (JS bundle con Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
=======
</body>

</html>
>>>>>>> angela
