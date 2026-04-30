<!-- DEFINIR tituloPagina, header, contenidoPrincipal y js antes del include y opcionalmente definir claseMain, contenidoAdicional y scriptManual -->
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= htmlspecialchars($tituloPagina ?? 'Bistro FDI', ENT_QUOTES, 'UTF-8') ?></title>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (opcional, por si los usamos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <?php
    // Incluimos los archivos js
    foreach (($js ?? []) as $ruta):
        echo '<script src="' . $ruta . '" defer></script>';
    endforeach;
    ?>
    <style>
        .brand-logo { height: 42px; width: auto; }
        .avatar-img { width: 42px; height: 42px; object-fit: cover; }
        .card-img-fixed { height: 190px; object-fit: cover; }
        .table-img { width: 64px; height: 64px; object-fit: cover; }
        .touch-action { min-height: 48px; }
        .img-carrusel { display: none; width: 100%; max-height: 360px; object-fit: cover; border-radius: .5rem; }
        .img-carrusel.active { display: block; }
        .btn-carrusel { position: absolute; top: 50%; transform: translateY(-50%); z-index: 2; border: 0; border-radius: 999px; background: rgba(0,0,0,.45); color: #fff; width: 2.5rem; height: 2.5rem; }
        .btn-carrusel.prev { left: .75rem; }
        .btn-carrusel.next { right: .75rem; }
        .modal[style*="block"], #contenedor-centro-edit-admin[style*="flex"], #modalAdminEliminarusuario[style*="block"] { background: rgba(0,0,0,.45); align-items: center; justify-content: center; padding: 1rem; }
        .modal[style*="block"] { display: flex !important; }
        .modal-contenido, .perfil-container-edit-admin { background: #fff; border-radius: .75rem; box-shadow: 0 1rem 3rem rgba(0,0,0,.175); width: min(720px, 100%); max-height: 90vh; overflow: auto; padding: 1.5rem; position: relative; }
        .cerrar-modal, .cerrar-modal-avatar, .cerrar-modal-pass, .cerrar-modal-error, .cerrar-modal-edit-admin, .cerrar-modal-rol, .cerrar-modal-del { position: absolute; right: 1rem; top: .75rem; border: 0; background: transparent; font-size: 1.75rem; cursor: pointer; }
        #contenedor-centro-edit-admin, #modalAdminEliminarusuario { display: none; position: fixed; inset: 0; z-index: 1055; }
        #Logo-Usuario { width: 96px; height: 96px; object-fit: cover; border-radius: 999px; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <?php
    include($header);
    ?>

    <?php
    $clasesMainExtra = $claseMain ?? '';
    $contenedorBootstrap = str_contains($clasesMainExtra, 'contenedor-fullwidth') ? 'container-fluid' : 'container';
    ?>
    <main id="contenedor" class="flex-grow-1 <?= $contenedorBootstrap ?> <?= htmlspecialchars($clasesMainExtra, ENT_QUOTES, 'UTF-8') ?> py-4">
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
