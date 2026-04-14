<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Método no permitido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!$data) {
        $data = $_POST;
    }

    $cita_id = $data['cita_id'] ?? null;
    $descripcion = trim($data['descripcion'] ?? '');

    if (!$cita_id) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "cita_id es obligatorio"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo->beginTransaction();

    // 1. Buscar cita
    $stmtCita = $pdo->prepare("
        SELECT id, cliente_id, vehiculo_id, tipo_servicio, notas, estado
        FROM citas
        WHERE id = ?
        LIMIT 1
    ");
    $stmtCita->execute([$cita_id]);
    $cita = $stmtCita->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Cita no encontrada"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2. Verificar si ya existe una orden para esa cita
    $stmtExiste = $pdo->prepare("
        SELECT id
        FROM ordenes
        WHERE cita_id = ?
        LIMIT 1
    ");
    $stmtExiste->execute([$cita_id]);
    $ordenExistente = $stmtExiste->fetch(PDO::FETCH_ASSOC);

    if ($ordenExistente) {
        $pdo->rollBack();
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "La cita ya tiene una orden registrada",
            "orden_id" => $ordenExistente['id']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3. Obtener total del presupuesto
    $stmtTotal = $pdo->prepare("
        SELECT COALESCE(SUM(importe), 0) AS total
        FROM presupuesto_items
        WHERE cita_id = ?
    ");
    $stmtTotal->execute([$cita_id]);
    $totalData = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    $total = $totalData['total'] ?? 0;

    // 4. Crear descripción automática si no se envía una
    if ($descripcion === '') {
        $partes = [];

        if (!empty($cita['tipo_servicio'])) {
            $partes[] = $cita['tipo_servicio'];
        }

        if (!empty($cita['notas'])) {
            $partes[] = $cita['notas'];
        }

        $descripcion = implode(' - ', $partes);

        if ($descripcion === '') {
            $descripcion = 'Orden generada desde cita #' . $cita_id;
        }
    }

    // 5. Insertar orden
    $stmtInsert = $pdo->prepare("
        INSERT INTO ordenes (
            cliente_id,
            vehiculo_id,
            cita_id,
            descripcion,
            estado,
            total
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmtInsert->execute([
        $cita['cliente_id'],
        $cita['vehiculo_id'],
        $cita_id,
        $descripcion,
        'Pendiente',
        $total
    ]);

    $orden_id = $pdo->lastInsertId();

    // 6. Opcional: actualizar estado de la cita
    $stmtUpdateCita = $pdo->prepare("
        UPDATE citas
        SET estado = 'Convertida en orden', updated_at = NOW()
        WHERE id = ?
    ");
    $stmtUpdateCita->execute([$cita_id]);

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Orden creada correctamente desde la cita",
        "orden_id" => $orden_id,
        "cita_id" => $cita_id,
        "total" => $total
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error en servidor",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>