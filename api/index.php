<?php
// Cabeceras para CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Resto del enrutamiento
$request = $_SERVER['REQUEST_URI'];
if (strpos($request, '/api/users') === 0) {
    require_once 'users.php';
} elseif (strpos($request, '/api/shifts') === 0) {
    require_once 'shifts.php';
} elseif (strpos($request, '/api/config') === 0) {
    require_once 'config.php';
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint no encontrado."]);
}
?>
