<?php
// view_all_orders.php - Visualització de totes les comandes (Funcionalitat 7)
// Basat en l'exemple de classe llegixTotRegist.php

$filename = __DIR__ . "/../../orders/orders.json";

// Si el fitxer no existeix, generem resposta 404 (Requisit PDF 7.b)
if (!file_exists($filename)) {
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
        <p>The orders storage file (orders.json) could not be found or opened.</p>
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

// Llegim les comandes del fitxer JSON 
$dades = json_decode(file_get_contents($filename), true);
if (!is_array($dades)) {
    $dades = [];
}

// Resposta 200 OK 
http_response_code(200);

// Funció per formatar les flors comprades
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
    <title>All Orders</title>
    <link rel="stylesheet" href="../client/css/style.css">
</head>
<body>
    <div class="wide-container">
        <h1>All Floral Orders</h1>

        <?php if (empty($dades)): ?>
            <p>No orders recorded yet.</p>
        <?php else: ?>
            <table border="1">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Flowers Ordered</th>
                    <th>Subtotal</th>
                    <th>VAT (21%)</th>
                    <th>Total with VAT</th>
                </tr>
                <?php foreach ($dades as $comanda): ?>
                <tr>
                    <td><?= htmlspecialchars($comanda['order_id']) ?></td>
                    <td><?= htmlspecialchars($comanda['customer_name']) ?></td>
                    <td><?= htmlspecialchars($comanda['customer_address']) ?></td>
                    <td><?= htmlspecialchars($comanda['customer_email']) ?></td>
                    <td><?= htmlspecialchars($comanda['customer_phone']) ?></td>
                    <td><?= htmlspecialchars(formatarFlors($comanda['items'])) ?></td>
                    <td><?= htmlspecialchars($comanda['subtotal']) ?> €</td>
                    <td><?= htmlspecialchars($comanda['vat_amount']) ?> €</td>
                    <td><strong><?= htmlspecialchars($comanda['total_with_tax']) ?> €</strong></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <div class="button-group" style="max-width: 320px; margin: 20px auto 0 auto;">
            <a href="../client/view_menu.html" class="btn btn-secondary">Back to Orders View Menu</a>
            <a href="../client/operations_menu.html" class="btn btn-secondary">Back to Operations Menu</a>
            <a href="../client/index.html" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>
