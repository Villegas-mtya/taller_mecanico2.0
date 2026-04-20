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
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("\n            SELECT
                id,
                nombre,
                precio,
                activo,
                created_at,
                updated_at
            FROM servicios
            ORDER BY id DESC
        ");

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data) {
            $data = $_POST;
        }

        $action = $data['action'] ?? '';

        if ($action === 'create') {
            $nombre = trim($data['nombre'] ?? '');
            $precio = isset($data['precio']) ? (float)$data['precio'] : 0;
            $activo = isset($data['activo']) ? (int)$data['activo'] : 1;

            if ($nombre === '') {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nombre requerido'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("\n                INSERT INTO servicios
                (nombre, precio, activo)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$nombre, $precio, $activo]);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio creado correctamente',
                'id' => $pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'update') {
            $id = $data['id'] ?? null;
            $nombre = trim($data['nombre'] ?? '');
            $precio = isset($data['precio']) ? (float)$data['precio'] : 0;
            $activo = isset($data['activo']) ? (int)$data['activo'] : 1;

            if (!$id || $nombre === '') {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'id y nombre son obligatorios'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("\n                UPDATE servicios
                SET
                    nombre = ?,
                    precio = ?,
                    activo = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$nombre, $precio, $activo, $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio actualizado correctamente'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'delete') {
            $id = $data['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID requerido'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare('DELETE FROM servicios WHERE id = ?');
            $stmt->execute([$id]);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio eliminado correctamente'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en servidor',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
