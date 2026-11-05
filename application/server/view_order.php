<?php

$filename = __DIR__ . "/../../orders/orders.json";
$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';

// Comprovació de fitxer existent
if (!file_exists($filename)) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Error</title>
    <link rel="stylesheet" href="../client/css/style.css">
</head>
<body>
    <div class="container">
        <h1>File Error (404 Not Found)</h1>
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

// Llegir dades i cercar comanda pel seu codi 
$dades = json_decode(file_get_contents($filename), true);
$trobat = null;

if (is_array($dades)) {
    foreach ($dades as $comanda) {
        if (isset($comanda['order_id']) && (string)$comanda['order_id'] === $order_id) {
            $trobat = $comanda;
            break;
        }
    }
}

// 3. Si no es troba la comanda, resposta 404 (Requisit PDF 8.c)
if ($trobat === null) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Not Found</title>
    <link rel="stylesheet" href="../client/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Order Not Found (404 Not Found)</h1>
        <p>No floral order was found with Order ID: "<?= htmlspecialchars($order_id) ?>".</p>
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

// 200 OK
http_response_code(200);

// Funció per formatar les flors
function formatarFlors($items) {
    if (!is_array($items)) return "None";
    $resultat = [];
    foreach ($items as $flor => $quantitat) {
        if ($quantitat > 0) {
            $resultat[] = ucfirst($flor) . ": " . $quantitat;
        }
    }
    return !empty($resultat) ? implode(", ", $resultat) : "None";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="../client/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Order Details</h1>
        <p class="subtitle">Information for Order ID: <strong><?= htmlspecialchars($trobat['order_id']) ?></strong></p>

        <table border="1">
            <tr><th>Order ID</th><td><?= htmlspecialchars($trobat['order_id']) ?></td></tr>
            <tr><th>Customer Name</th><td><?= htmlspecialchars($trobat['customer_name']) ?></td></tr>
            <tr><th>Address</th><td><?= htmlspecialchars($trobat['customer_address']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($trobat['customer_email']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($trobat['customer_phone']) ?></td></tr>
            <tr><th>Flowers Ordered</th><td><?= htmlspecialchars(formatarFlors($trobat['items'])) ?></td></tr>
            <tr><th>Subtotal</th><td><?= htmlspecialchars($trobat['subtotal']) ?> €</td></tr>
            <tr><th>VAT (21%)</th><td><?= htmlspecialchars($trobat['vat_amount']) ?> €</td></tr>
            <tr><th>Total with VAT</th><td><strong><?= htmlspecialchars($trobat['total_with_tax']) ?> €</strong></td></tr>
            <?php if (!empty($trobat['date'])): ?>
            <tr><th>Date</th><td><?= htmlspecialchars($trobat['date']) ?></td></tr>
            <?php endif; ?>
        </table>

        <div class="button-group">
            <a href="../client/view_menu.html" class="btn btn-secondary">Back to Orders View Menu</a>
            <a href="../client/operations_menu.html" class="btn btn-secondary">Back to Operations Menu</a>
            <a href="../client/index.html" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body> 
</html>
