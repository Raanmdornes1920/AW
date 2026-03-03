<?php
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
define('RUTA_IMG', RAIZ_APP . '/img');
define('RUTA_VISTAS', RAIZ_APP . '/vistas');
define('RUTA_STATIC', RAIZ_APP . '/static');
define('IMAGENES_BASE', ['default.png', 'admin.png', 'camarero.png', 'cocinero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_CAMARERO', ['default.png', 'camarero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_COCINERO', ['default.png', 'cocinero.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);
define('AVATARES_INICIALES', ['default.png', 'base/base1.png', 'base/base2.png', 'base/base3.png', 'base/base4.png']);


$user = "root";
$db   = "database";

$db_connection = mysqli_connect($host, $user, $pass, $db);
?>