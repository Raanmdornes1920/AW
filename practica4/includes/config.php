<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
$es_local = ($remoteAddr == '127.0.0.1' || str_starts_with($httpHost, 'localhost') || str_starts_with($httpHost, '127.0.0.1'));

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = str_replace('\\', '/', dirname($scriptName));
$posIncludes = strpos($basePath, '/includes');
if ($posIncludes !== false) {
    $basePath = substr($basePath, 0, $posIncludes);
}
$basePath = rtrim($basePath, '/');

if ($es_local) {
    define('RAIZ_APP', $basePath);
    define('DIR_RAIZ', dirname(__DIR__));

    $host = "127.0.0.1";
    $pass = "";
} else {
    define('RAIZ_APP', '');
    define('DIR_RAIZ', dirname(__DIR__));

    $host = "vm016.db.swarm.test";
    $pass = "d5J40AQKX1qVwwSGwr05";
}

define('RUTA_CSS', RAIZ_APP . '/css');
define('RUTA_JS', RAIZ_APP . '/js');
define('RUTA_IMG', RAIZ_APP . '/img');
define('RUTA_INCLUDES', RAIZ_APP . '/includes');

define('RUTA_CLASES', RUTA_INCLUDES . '/clases');
define('RUTA_VISTAS', RUTA_INCLUDES . '/vistas');

define('RUTA_DAO', RUTA_CLASES . '/DAO');
define('RUTA_DTO', RUTA_CLASES . '/DTO');
define('RUTA_SA', RUTA_CLASES . '/SA');
define('RUTA_COMUN', RUTA_VISTAS . '/comun');

define('IMAGENES_BASE', ['default.png', 'admin.png', 'camarero.png', 'cocinero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_CAMARERO', ['default.png', 'camarero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_COCINERO', ['default.png', 'cocinero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_INICIALES', ['default.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);

$user = "root";
$db = "database";

$db_connection = mysqli_connect($host, $user, $pass, $db, 3308);

// Incluimos todo dentro de carpeta clases
define('DIR_CLASES', __DIR__ . '/clases');
foreach (['DTO', 'DAO', 'SA'] as $carpeta) {
    foreach (glob(DIR_CLASES . "/" . $carpeta . "/*.php") as $archivo) {
        require_once $archivo;
    }
}
?>