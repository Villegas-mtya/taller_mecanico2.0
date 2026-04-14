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

    // GET: listar clientes
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM clientes ORDER BY id ASC");
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

        $action = $data['action'] ?? null;

        if ($action === 'create') {
            $nombre = trim($data['nombre'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $correo = trim($data['correo'] ?? '');

            if ($nombre === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Nombre requerido"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO clientes (nombre, telefono, correo)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$nombre, $telefono, $correo]);

            echo json_encode([
                "success" => true,
                "message" => "Cliente creado",
                "id" => $pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'update') {
            $id = $data['id'] ?? null;
            $nombre = trim($data['nombre'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $correo = trim($data['correo'] ?? '');

            if (!$id || $nombre === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Datos inválidos"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE clientes
                SET nombre = ?, telefono = ?, correo = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $telefono, $correo, $id]);

            echo json_encode([
                "success" => true,
                "message" => "Cliente actualizado"
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

            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Cliente eliminado"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Action no válida"
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
        "message" => "Error de base de datos",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>