<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener configuración
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->query("SELECT cleaning_interval, last_shift_date FROM config LIMIT 1");
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($config);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener configuración: " . $e->getMessage()]);
    }
    exit;
}

// Actualizar configuración
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['cleaning_interval'])) {
        try {
            $sql = "UPDATE config SET cleaning_interval = :cleaning_interval WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':cleaning_interval' => $data['cleaning_interval']]);

            echo json_encode(["message" => "Configuración actualizada exitosamente."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar configuración: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Datos inválidos."]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["message" => "Método no permitido."]);
?>
