<!-- DEFINIR tituloPagina, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $tituloPagina ?></title>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (opcional, por si los usamos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <?php
    // Incluimos los archivos js
    foreach ($js as $ruta):
        echo '<script src="' . $ruta . '" defer></script>';
    endforeach;
    ?>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <?php
    include($header);
    ?>

    <main id="contenedor" class="flex-grow-1 container py-4 mt-5 <?= $claseMain ?? '' ?>">
        <?= $contenidoPrincipal ?>
    </main>

    <?php if (isset($contenidoAdicional)) {
        echo $contenidoAdicional;
    } ?>

    <?php
    include(__DIR__ . '/footer.php');
    if (isset($scirptManual)) {
        echo $scirptManual;
    }
    ?>

    <!-- Bootstrap 5.3 (JS bundle con Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
