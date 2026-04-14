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
        $sql = "
            SELECT
                c.id,
                c.cliente_id,
                c.vehiculo_id,
                c.fecha,
                c.hora,
                c.estado,
                c.created_at,
                c.mecanico_id,
                c.tipo_servicio,
                c.notas,
                c.observaciones_internas,
                c.motivo_cancelacion,
                c.motivo_reprogramacion,
                c.updated_at,
                cl.nombre AS cliente_nombre,
                cl.telefono AS cliente_telefono,
                cl.correo AS cliente_correo,
                v.marca,
                v.modelo,
                v.anio,
                v.placas,
                m.nombre AS mecanico_nombre,
                m.activo AS mecanico_activo
            FROM citas c
            INNER JOIN clientes cl ON c.cliente_id = cl.id
            INNER JOIN vehiculos v ON c.vehiculo_id = v.id
            LEFT JOIN mecanicos m ON c.mecanico_id = m.id
            ORDER BY c.id ASC
        ";

        $stmt = $pdo->query($sql);
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
            $cliente_id = $data['cliente_id'] ?? null;
            $vehiculo_id = $data['vehiculo_id'] ?? null;
            $fecha = $data['fecha'] ?? null;
            $hora = $data['hora'] ?? null;
            $estado = trim($data['estado'] ?? 'Pendiente');
            $mecanico_id = $data['mecanico_id'] ?? null;
            $tipo_servicio = trim($data['tipo_servicio'] ?? '');
            $notas = trim($data['notas'] ?? '');
            $observaciones_internas = trim($data['observaciones_internas'] ?? '');
            $motivo_cancelacion = trim($data['motivo_cancelacion'] ?? '');
            $motivo_reprogramacion = trim($data['motivo_reprogramacion'] ?? '');

            if (!$cliente_id || !$vehiculo_id || !$fecha || !$hora) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'cliente_id, vehiculo_id, fecha y hora son obligatorios'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO citas (
                    cliente_id,
                    vehiculo_id,
                    fecha,
                    hora,
                    estado,
                    mecanico_id,
                    tipo_servicio,
                    notas,
                    observaciones_internas,
                    motivo_cancelacion,
                    motivo_reprogramacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $cliente_id,
                $vehiculo_id,
                $fecha,
                $hora,
                $estado,
                $mecanico_id !== '' ? $mecanico_id : null,
                $tipo_servicio !== '' ? $tipo_servicio : null,
                $notas !== '' ? $notas : null,
                $observaciones_internas !== '' ? $observaciones_internas : null,
                $motivo_cancelacion !== '' ? $motivo_cancelacion : null,
                $motivo_reprogramacion !== '' ? $motivo_reprogramacion : null
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Cita creada correctamente',
                'id' => $pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'update') {
            $id = $data['id'] ?? null;
            $cliente_id = $data['cliente_id'] ?? null;
            $vehiculo_id = $data['vehiculo_id'] ?? null;
            $fecha = $data['fecha'] ?? null;
            $hora = $data['hora'] ?? null;
            $estado = trim($data['estado'] ?? 'Pendiente');
            $mecanico_id = $data['mecanico_id'] ?? null;
            $tipo_servicio = trim($data['tipo_servicio'] ?? '');
            $notas = trim($data['notas'] ?? '');
            $observaciones_internas = trim($data['observaciones_internas'] ?? '');
            $motivo_cancelacion = trim($data['motivo_cancelacion'] ?? '');
            $motivo_reprogramacion = trim($data['motivo_reprogramacion'] ?? '');

            if (!$id || !$cliente_id || !$vehiculo_id || !$fecha || !$hora) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'id, cliente_id, vehiculo_id, fecha y hora son obligatorios'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE citas
                SET
                    cliente_id = ?,
                    vehiculo_id = ?,
                    fecha = ?,
                    hora = ?,
                    estado = ?,
                    mecanico_id = ?,
                    tipo_servicio = ?,
                    notas = ?,
                    observaciones_internas = ?,
                    motivo_cancelacion = ?,
                    motivo_reprogramacion = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $cliente_id,
                $vehiculo_id,
                $fecha,
                $hora,
                $estado,
                $mecanico_id !== '' ? $mecanico_id : null,
                $tipo_servicio !== '' ? $tipo_servicio : null,
                $notas !== '' ? $notas : null,
                $observaciones_internas !== '' ? $observaciones_internas : null,
                $motivo_cancelacion !== '' ? $motivo_cancelacion : null,
                $motivo_reprogramacion !== '' ? $motivo_reprogramacion : null,
                $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Cita actualizada correctamente'
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

            $stmt = $pdo->prepare("DELETE FROM citas WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode([
                'success' => true,
                'message' => 'Cita eliminada correctamente'
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