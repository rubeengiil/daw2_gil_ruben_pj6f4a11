<?php

// 1. Funcions per calcular l'IVA del 21% i el preu total
function calcularIVA($subtotal) {
    return round($subtotal * 0.21, 2);
}

function calcularTotalComanda($quantities) {
    $preus = [
        'roses' => 25.00,
        'tulips' => 15.00,
        'orchids' => 30.00,
        'sunflowers' => 20.00
    ];
    $subtotal = 0;
    foreach ($preus as $flor => $preu) {
        $quantitat = isset($quantities[$flor]) ? (int)$quantities[$flor] : 0;
        $subtotal += ($quantitat * $preu);
    }
    $iva = calcularIVA($subtotal); // Crida a la funció calcularIVA
    $total = $subtotal + $iva;

    return [
        'subtotal' => number_format($subtotal, 2, '.', ''),
        'iva' => number_format($iva, 2, '.', ''),
        'total' => number_format($total, 2, '.', '')
    ];
}

// 2. Recollir dades de la comanda enviades per POST
$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$quantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];

// Càlcul del preu amb IVA del 21% a la part servidora
$calculs = calcularTotalComanda($quantities);

// Creació de l'array associatiu multidimensional de la comanda
$nova_comanda = [
    'order_id' => $order_id,
    'customer_name' => $customer_name,
    'customer_address' => $customer_address,
    'customer_email' => $customer_email,
    'customer_phone' => $customer_phone,
    'items' => [
        'roses' => (int)($quantities['roses'] ?? 0),
        'tulips' => (int)($quantities['tulips'] ?? 0),
        'orchids' => (int)($quantities['orchids'] ?? 0),
        'sunflowers' => (int)($quantities['sunflowers'] ?? 0)
    ],
    'subtotal' => $calculs['subtotal'],
    'vat_rate' => '21%',
    'vat_amount' => $calculs['iva'],
    'total_with_tax' => $calculs['total'],
    'date' => date('Y-m-d H:i:s')
];

// 3. Desar la comanda al fitxer orders/orders.json
$filename = __DIR__ . "/../../orders/orders.json";
$comandes = [];

if (file_exists($filename)) {
    $contingut = file_get_contents($filename);
    $comandes = json_decode($contingut, true);
    if (!is_array($comandes)) {
        $comandes = [];
    }
}

// Afegim la nova comanda a la llista
$comandes[] = $nova_comanda;

// Convertim a JSON i guardem amb bloqueig LOCK_EX com als exemples
$json_dades = json_encode($comandes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
$guardat = @file_put_contents($filename, $json_dades, LOCK_EX);

// Si no es pot escriure al fitxer, error 404 amb document HTML
if ($guardat === false) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Storage Error</title>
    <link rel="stylesheet" href="../client/style.css">
</head>
<body>
    <div class="container">
        <h1>Storage Error (404 Not Found)</h1>
        <p>Could not open or write to orders storage file.</p>
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

// 4. Resposta JSON al client únicament amb el resultat (com a sm6ex5.php)
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'order_id' => $order_id,
    'subtotal' => $calculs['subtotal'],
    'vat_rate' => '21%',
    'vat_amount' => $calculs['iva'],
    'total_with_tax' => $calculs['total']
]);
?>
