<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {

    // GET: listar vehículos
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "
            SELECT 
                v.id,
                v.cliente_id,
                v.marca,
                v.modelo,
                v.anio,
                v.placas,
                c.nombre AS cliente_nombre
            FROM vehiculos v
            INNER JOIN clientes c ON v.cliente_id = c.id
            ORDER BY v.id ASC
        ";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // POST: create, update, delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);

        if (!$data) {
            $data = $_POST;
        }

        $action = $data['action'] ?? '';

        if ($action === 'create') {
            $cliente_id = $data['cliente_id'] ?? null;
            $marca = trim($data['marca'] ?? '');
            $modelo = trim($data['modelo'] ?? '');
            $anio = $data['anio'] ?? null;
            $placas = trim($data['placas'] ?? '');

            if (!$cliente_id || $marca === '' || $modelo === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "cliente_id, marca y modelo son obligatorios"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO vehiculos (cliente_id, marca, modelo, anio, placas)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $cliente_id,
                $marca,
                $modelo,
                $anio !== '' ? $anio : null,
                $placas
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Vehículo creado",
                "id" => $pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'update') {
            $id = $data['id'] ?? null;
            $cliente_id = $data['cliente_id'] ?? null;
            $marca = trim($data['marca'] ?? '');
            $modelo = trim($data['modelo'] ?? '');
            $anio = $data['anio'] ?? null;
            $placas = trim($data['placas'] ?? '');

            if (!$id || !$cliente_id || $marca === '' || $modelo === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "id, cliente_id, marca y modelo son obligatorios"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE vehiculos
                SET cliente_id = ?, marca = ?, modelo = ?, anio = ?, placas = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $cliente_id,
                $marca,
                $modelo,
                $anio !== '' ? $anio : null,
                $placas,
                $id
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Vehículo actualizado"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'delete') {
            $id = $data['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "ID requerido"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Vehículo eliminado"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Acción no válida"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error en servidor",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>