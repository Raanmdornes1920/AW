<?php
require_once 'Usuario.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$es_local = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');

if ($es_local) {
    define('RAIZ_APP', '/AW/practica2');

    $host = "127.0.0.1";
    $pass = "";
} else {
    define('RAIZ_APP', '');
    
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
$db   = "database";

$db_connection = mysqli_connect($host, $user, $pass, $db);
?>