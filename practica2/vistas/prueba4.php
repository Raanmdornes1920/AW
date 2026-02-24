<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva categoría</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
<!-- Header -->
<?php include '../static/header.php'; ?>
<!-- Header -->
</body>
</html>