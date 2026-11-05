<?php
//Funcions de càlcul IVA
function calcularIVA($subtotal) {
    return round($subtotal * 0.21, 2);
}

function calcularTotalComanda($quantities) {
    $preus = ['roses' => 25.00, 'tulips' => 15.00, 'orchids' => 30.00, 'sunflowers' => 20.00];
    $subtotal = 0;
    foreach ($preus as $flor => $preu) {
        $subtotal += ((int)($quantities[$flor] ?? 0) * $preu);
    }
    $iva = calcularIVA($subtotal); // Crida a la funció calcularIVA
    return [
        'subtotal' => number_format($subtotal, 2, '.', ''),
        'iva'      => number_format($iva, 2, '.', ''),
        'total'    => number_format($subtotal + $iva, 2, '.', '')
    ];
}

//Recollir dades per POST i fer càlculs al servidor
$order_id   = trim($_POST['order_id'] ?? '');
$quantities = $_POST['quantities'] ?? [];
$calculs    = calcularTotalComanda($quantities);

// Array associatiu multidimensional
$nova_comanda = [
    'order_id'         => $order_id,
    'customer_name'    => trim($_POST['customer_name'] ?? ''),
    'customer_address' => trim($_POST['customer_address'] ?? ''),
    'customer_email'   => trim($_POST['customer_email'] ?? ''),
    'customer_phone'   => trim($_POST['customer_phone'] ?? ''),
    'items' => [
        'roses'      => (int)($quantities['roses'] ?? 0),
        'tulips'     => (int)($quantities['tulips'] ?? 0),
        'orchids'    => (int)($quantities['orchids'] ?? 0),
        'sunflowers' => (int)($quantities['sunflowers'] ?? 0)
    ],
    'subtotal'       => $calculs['subtotal'],
    'vat_rate'       => '21%',
    'vat_amount'     => $calculs['iva'],
    'total_with_tax' => $calculs['total'],
    'date'           => date('Y-m-d H:i:s')
];

//Desar a orders/orders.json
$filename = __DIR__ . "/../../orders/orders.json";
if (!file_exists(dirname($filename))) {
    @mkdir(dirname($filename), 0777, true);
}

$comandes = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];
if (!is_array($comandes)) $comandes = [];
$comandes[] = $nova_comanda;

$guardat = @file_put_contents($filename, json_encode($comandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

// Si falla el fitxer, error 404 amb document HTML 
if ($guardat === false) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Storage Error</title>
    <link rel="stylesheet" href="../client/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Storage Error (404 Not Found)</h1>
        <div class="button-group">
            <a href="../client/view_menu.html" class="btn btn-secondary">Back to Orders View Menu</a>
            <a href="../client/operations_menu.html" class="btn btn-secondary">Back to Operations Menu</a>
            <a href="../client/index.html" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// 4. Resposta JSON únicament amb el resultat
header('Content-Type: application/json');
echo json_encode([
    'success'        => true,
    'order_id'       => $order_id,
    'subtotal'       => $calculs['subtotal'],
    'vat_rate'       => '21%',
    'vat_amount'     => $calculs['iva'],
    'total_with_tax' => $calculs['total']
]);
?>