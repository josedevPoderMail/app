<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $conn->query("SELECT id, name, email, active FROM users HAVING active = 1");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
        exit;
    }

    http_response_code(405);
    echo json_encode(["message" => "Método no permitido."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
}
?>
