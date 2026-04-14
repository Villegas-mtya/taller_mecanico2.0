<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Método no permitido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID de cita requerido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "
        SELECT
            c.id,
            c.cliente_id,
            c.vehiculo_id,
            c.fecha,
            c.hora,
            c.estado,
            c.mecanico_id,
            c.tipo_servicio,
            c.notas,
            c.created_at,
            c.updated_at,
            cl.nombre AS cliente_nombre,
            cl.telefono AS cliente_telefono,
            cl.correo AS cliente_correo,
            v.marca AS vehiculo_marca,
            v.modelo AS vehiculo_modelo,
            v.anio AS vehiculo_anio,
            v.placas AS vehiculo_placas,
            m.nombre AS mecanico_nombre,
            m.activo AS mecanico_activo
        FROM citas c
        INNER JOIN clientes cl ON c.cliente_id = cl.id
        INNER JOIN vehiculos v ON c.vehiculo_id = v.id
        LEFT JOIN mecanicos m ON c.mecanico_id = m.id
        WHERE c.id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Cita no encontrada"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmtItems = $pdo->prepare("
        SELECT
            id,
            cita_id,
            tipo_item,
            pieza_servicio_id,
            descripcion,
            cantidad,
            precio_unitario,
            importe,
            created_at,
            updated_at
        FROM presupuesto_items
        WHERE cita_id = ?
        ORDER BY id ASC
    ");
    $stmtItems->execute([$id]);
    $presupuesto_items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    $stmtOrden = $pdo->prepare("
        SELECT
            id,
            cliente_id,
            vehiculo_id,
            cita_id,
            descripcion,
            estado,
            total,
            created_at,
            updated_at
        FROM ordenes
        WHERE cita_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmtOrden->execute([$id]);
    $orden = $stmtOrden->fetch(PDO::FETCH_ASSOC);

    $stmtTotal = $pdo->prepare("
        SELECT COALESCE(SUM(importe), 0) AS total_presupuesto
        FROM presupuesto_items
        WHERE cita_id = ?
    ");
    $stmtTotal->execute([$id]);
    $totalPresupuesto = $stmtTotal->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => [
            "cita" => $cita,
            "presupuesto_items" => $presupuesto_items,
            "total_presupuesto" => (float) $totalPresupuesto['total_presupuesto'],
            "orden_relacionada" => $orden ?: null
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error en servidor"
    ], JSON_UNESCAPED_UNICODE);
}
?>