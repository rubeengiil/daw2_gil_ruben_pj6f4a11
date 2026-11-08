<?php
// login.php
header('Content-Type: application/json');

// 1. Recollim usuari i contrasenya enviats pel formulari
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// 2. Llegim el fitxer users.json
$fitxer = __DIR__ . "/../../users/users.json";
$usuaris = [];

if (file_exists($fitxer)) {
    $contingut = file_get_contents($fitxer);
    $usuaris = json_decode($contingut, true);
    if (!is_array($usuaris)) {
        $usuaris = [];
    }
}

// 3. Funció per validar l'usuari amb password_verify
function validarCredencials($user, $pass, $llista) {
    foreach ($llista as $u) {
        if ($u['user'] === $user) {
            return password_verify($pass, $u['password']);
        }
    }
    return false;
}

// 4. Comprovem i enviem la resposta en format JSON
if (validarCredencials($username, $password, $usuaris)) {
    echo json_encode([
        'success' => true,
        'message' => 'Login correcte'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Usuari o contrasenya incorrectes'
    ]);
}
?>
