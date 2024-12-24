<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Listar turnos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->query("SELECT s.id, s.date, s.status, u.name AS user_name 
                              FROM shifts s
                              JOIN users u ON s.user_id = u.id
                              ORDER BY s.date ASC");
        $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($shifts);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener turnos: " . $e->getMessage()]);
    }
    exit;
}
 

// Generar turnos automáticamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/generate') !== false) {
    try {
        // Obtener configuración
        $configStmt = $conn->query("SELECT cleaning_interval, last_shift_date FROM config LIMIT 1");
        $config = $configStmt->fetch(PDO::FETCH_ASSOC);

        if (!$config) {
            http_response_code(500);
            echo json_encode(["error" => "No se encontró la configuración."]);
            exit;
        }

        $interval = $config['cleaning_interval'];
        $lastShiftDate = $config['last_shift_date'] ?: date('Y-m-d');

        // Obtener usuarios activos
        $usersStmt = $conn->query("SELECT id FROM users WHERE active = 1");
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$users) {
            http_response_code(500);
            echo json_encode(["error" => "No hay usuarios activos para asignar turnos."]);
            exit;
        }

        $newShiftDate = $lastShiftDate;

        // Crear turnos para los usuarios activos
        $insertStmt = $conn->prepare(" INSERT INTO shifts (user_id, date, status, created_at)
        SELECT 
            :user_id,
            DATE_ADD(date, INTERVAL 2 DAY) AS next_date,
            'pendiente',
            NOW()
        FROM shifts
        WHERE DAYOFWEEK(DATE_ADD(date, INTERVAL 2 DAY)) != 1");

        foreach ($users as $user) {
            $newShiftDate = date('Y-m-d', strtotime("$newShiftDate +$interval days"));
            $insertStmt->execute([
                ':user_id' => $user['id'],
                ':date' => $newShiftDate,
            ]);
        }

        // Actualizar la última fecha de turno en la configuración
        $updateStmt = $conn->prepare("UPDATE config SET last_shift_date = :last_date");
        $updateStmt->execute([':last_date' => $newShiftDate]);

        http_response_code(201);
        echo json_encode(["message" => "Turnos generados exitosamente hasta la fecha: $newShiftDate"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al generar turnos: " . $e->getMessage()]);
    }
    exit;
}

// Marcar turno como cumplido
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        try {
            $sql = "UPDATE shifts SET status = 'cumplido', marked_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $data['id']]);

            echo json_encode(["message" => "Turno marcado como cumplido."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar turno: " . $e->getMessage()]);
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
