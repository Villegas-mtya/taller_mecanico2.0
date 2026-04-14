<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "../config/db.php";

try {
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $sql = "
            SELECT 
                o.id,
                o.cliente_id,
                o.vehiculo_id,
                o.descripcion,
                o.estado,
                o.total,
                o.created_at,
                c.nombre AS clientenombre,
                CONCAT(v.marca, ' ', v.modelo, ' - ', v.placas) AS vehiculonombre
            FROM ordenes o
            INNER JOIN clientes c ON o.cliente_id = c.id
            INNER JOIN vehiculos v ON o.vehiculo_id = v.id
            ORDER BY o.id DESC
        ";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);

        if (!$data) {
            $data = $_POST;
        }

        $action = $data["action"] ?? "";

        if ($action === "create") {
            $stmt = $pdo->prepare("
                INSERT INTO ordenes (cliente_id, vehiculo_id, descripcion, estado, total)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data["cliente_id"] ?? null,
                $data["vehiculo_id"] ?? null,
                $data["descripcion"] ?? "",
                $data["estado"] ?? "Pendiente",
                $data["total"] ?? 0
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Orden creada correctamente"
            ]);
            exit;
        }

        if ($action === "update") {
            $stmt = $pdo->prepare("
                UPDATE ordenes
                SET cliente_id = ?, vehiculo_id = ?, descripcion = ?, estado = ?, total = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data["cliente_id"] ?? null,
                $data["vehiculo_id"] ?? null,
                $data["descripcion"] ?? "",
                $data["estado"] ?? "Pendiente",
                $data["total"] ?? 0,
                $data["id"] ?? null
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Orden actualizada correctamente"
            ]);
            exit;
        }

        if ($action === "delete") {
            $stmt = $pdo->prepare("DELETE FROM ordenes WHERE id = ?");
            $stmt->execute([
                $data["id"] ?? null
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Orden eliminada correctamente"
            ]);
            exit;
        }

        echo json_encode([
            "success" => false,
            "message" => "Acción no válida"
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error en el servidor",
        "error" => $e->getMessage()
    ]);
}