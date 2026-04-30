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

// Detectar si es HTTP o HTTPS
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Obtener el dominio (localhost, vm016.containers.fdi.ucm.es, etc.)
$dominio = $_SERVER['HTTP_HOST'];

$urlActual = $protocolo . $dominio . dirname($_SERVER['PHP_SELF']);

$params = [
    "DS_MERCHANT_AMOUNT" => $amount,
    "DS_MERCHANT_ORDER" => $orderId,
    "DS_MERCHANT_MERCHANTCODE" => $fuc,
    "DS_MERCHANT_CURRENCY" => $moneda,
    "DS_MERCHANT_TRANSACTIONTYPE" => $transaccion,
    "DS_MERCHANT_TERMINAL" => $terminal,
    "DS_MERCHANT_MERCHANTURL" => $urlTienda,
    "DS_MERCHANT_URLOK" => $urlActual . "/procesar_carrito.php?accion=confirmar&metodo_pago=tarjeta",
    "DS_MERCHANT_URLKO" => $urlActual . "/procesar_carrito.php?accion=confirmar&metodo_pago=tarjeta",
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
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirigiendo a Redsys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center" onload="document.forms[0].submit()">
    <div class="container">
        <div class="card shadow-sm mx-auto text-center" style="max-width: 520px;">
            <div class="card-body p-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h1 class="h4">Redirigiendo a la pasarela de pago segura...</h1>
                <p class="text-secondary mb-0">No cierres esta ventana.</p>
            </div>
        </div>
    </div>
    <form action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST">
        <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1" />
        <input type="hidden" name="Ds_MerchantParameters" value="<?=$paramsBase64?>" />
        <input type="hidden" name="Ds_Signature" value="<?=$firmaGenerada?>" />
    </form>
</body>
</html>
