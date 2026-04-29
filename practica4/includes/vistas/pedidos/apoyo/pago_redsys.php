<?php
require_once '../../../config.php';
session_start();

// 1. Recoger el importe del formulario
$amount = $_POST['amount'] ?? '0';

// 2. Datos que ya tenemos
$fuc = "263100000";
$terminal = "84";
$moneda = "978";        // 978 es Euros
$transaccion = "0";     // 0 es Pago estándar
$urlTienda = "http:/" . RUTA_VISTAS . "/pedidos/apoyo/procesar_carrito.php"; // URL donde Redsys avisará del éxito
$claveSecreta = "sq7HjrUOBfKmC576ILgskD5srU870gJ7"; // LA CLAVE QUE TE DIERON

// 3. Datos del pedido (de tu base de datos)
$orderId = "BI" . time(); // Debe ser único (máx 12 caracteres)

// 4. Crear el array de parámetros
$params = [
    "DS_MERCHANT_AMOUNT" => $amount,
    "DS_MERCHANT_ORDER" => $orderId,
    "DS_MERCHANT_MERCHANTCODE" => $fuc,
    "DS_MERCHANT_CURRENCY" => $moneda,
    "DS_MERCHANT_TRANSACTIONTYPE" => $transaccion,
    "DS_MERCHANT_TERMINAL" => $terminal,
    "DS_MERCHANT_MERCHANTURL" => $urlTienda,
    "DS_MERCHANT_URLOK" => $es_local?"http://localhost/AW/practica4/includes/vistas/pedidos/apoyo/procesar_carrito.php?accion=confirmar&metodo_pago=tarjeta":"https://vm016.containers.fdi.ucm.es/includes/vistas/pagos/ok.php",
    "DS_MERCHANT_URLKO" => $es_local?"http://localhost/AW/practica4/includes/vistas/pedidos/apoyo/procesar_carrito.php?accion=confirmar&&metodo_pago=tarjeta":"https://vm016.containers.fdi.ucm.es/includes/vistas/pagos/ko.php",
    "DS_MERCHANT_MERCHANTDATA" => $_POST['tipo_pedido']
];

// 5. Convertir a JSON y Base64
$paramsBase64 = base64_encode(json_encode($params));

// 6. Generar la firma (esto es lo más crítico)
// Redsys usa SHA-256 HMAC. Te recomiendo bajar la librería oficial 'apiRedsys.php' 
// desde su web, pero el proceso manual implica encriptar el OrderId con tu clave 
// y luego firmar el JSON con ese resultado.
function getSignature($dataBase64, $orderId, $key) {
    // Decodificar la clave secreta (Redsys la da en Base64 en el panel, pero aquí parece texto plano)
    $key = base64_decode($key);
    
    // 1. Cifrar el ID del pedido con la clave usando TripleDES (paso interno de Redsys)
    $l = ceil(strlen($orderId) / 8) * 8;
    $orderIdPadded = str_pad($orderId, $l, "\0");
    $resKey = openssl_encrypt($orderIdPadded, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, "\0\0\0\0\0\0\0\0");
    
    // 2. Generar HMAC SHA256 de los parámetros usando esa clave resultante
    $res = hash_hmac('sha256', $dataBase64, $resKey, true);
    return base64_encode($res);
}

// 7. Firmar y procesar (usando la función getSignature que pusimos antes)
$paramsBase64 = base64_encode(json_encode($params));
$firmaGenerada = getSignature($paramsBase64, $orderId, $claveSecreta);

// 8. Redirigir al usuario a la pasarela de pago con los parámetros necesarios
?>
<!DOCTYPE html>
<html>
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/movil.css" media="only screen and (max-width: 600px)" />
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/tablet.css" media="only screen and (min-width: 601px) and (max-width: 1023px)" />
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/pc.css" media="only screen and (min-width: 1024px)" />

<body onload="document.forms[0].submit()">
    <div class="contenedor-centro">
        <p>Redirigiendo a la pasarela de pago segura...</p>
    </div>
    <form action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST">
        <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1" />
        <input type="hidden" name="Ds_MerchantParameters" value="<?=$paramsBase64?>" />
        <input type="hidden" name="Ds_Signature" value="<?=$firmaGenerada?>" />
    </form>
</body>
</html>