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
    /**
     * Normaliza el tipo de item para evitar comparaciones frágiles.
     * Acepta variaciones comunes enviadas por frontend.
     */
    $normalizarTipoItem = static function ($tipoRaw) {
        $tipo = mb_strtolower(trim((string) $tipoRaw), 'UTF-8');

        if (in_array($tipo, ['inventario', 'pieza', 'refaccion', 'refacción'], true)) {
            return 'inventario';
        }

        if (in_array($tipo, ['servicio', 'mano_obra', 'mano de obra'], true)) {
            return 'servicio';
        }

        return $tipo;
    };

    /**
     * Resuelve el precio unitario base según catálogo:
     * - inventario => costo directo (regla de negocio solicitada)
     * - servicio   => precio del servicio
     *
     * Retorna null cuando no aplica o no se puede resolver.
     */
    $resolverPrecioDesdeCatalogo = static function ($pdo, $tipoItem, $piezaServicioId) {
        if (!$piezaServicioId) {
            return null;
        }

        if ($tipoItem === 'inventario') {
            $stmt = $pdo->prepare("SELECT costo FROM inventario WHERE id = ? LIMIT 1");
            $stmt->execute([$piezaServicioId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (float) $row['costo'] : null;
        }

        if ($tipoItem === 'servicio') {
            $stmt = $pdo->prepare("SELECT precio FROM servicios WHERE id = ? LIMIT 1");
            $stmt->execute([$piezaServicioId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (float) $row['precio'] : null;
        }

        return null;
    };


    // GET: listar items de presupuesto
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "
            SELECT
                p.id,
                p.cita_id,
                p.tipo_item,
                p.pieza_servicio_id,
                p.descripcion,
                p.cantidad,
                p.precio_unitario,
                p.importe,
                p.created_at,
                p.updated_at
            FROM presupuesto_items p
            ORDER BY p.id ASC
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
            $cita_id = $data['cita_id'] ?? null;
            $tipo_item = $normalizarTipoItem($data['tipo_item'] ?? '');
            $pieza_servicio_id = $data['pieza_servicio_id'] ?? null;
            $descripcion = trim($data['descripcion'] ?? '');
            $cantidad = $data['cantidad'] ?? 1;
            $precio_unitario = $data['precio_unitario'] ?? 0;
            $importe = $data['importe'] ?? null;

            if (!$cita_id || $tipo_item === '' || $descripcion === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "cita_id, tipo_item y descripcion son obligatorios"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Regla solicitada: inventario usa costo directo.
            // Si viene id de catálogo, priorizamos precio resuelto desde DB.
            $precioCatalogo = $resolverPrecioDesdeCatalogo($pdo, $tipo_item, $pieza_servicio_id);
            if ($precioCatalogo !== null) {
                $precio_unitario = $precioCatalogo;
            }

            if ($importe === null || $importe === '') {
                $importe = (float)$cantidad * (float)$precio_unitario;
            }

            $stmt = $pdo->prepare("
                INSERT INTO presupuesto_items
                (cita_id, tipo_item, pieza_servicio_id, descripcion, cantidad, precio_unitario, importe)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $cita_id,
                $tipo_item,
                $pieza_servicio_id !== '' ? $pieza_servicio_id : null,
                $descripcion,
                $cantidad,
                $precio_unitario,
                $importe
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Item de presupuesto creado correctamente",
                "id" => $pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'update') {
            $id = $data['id'] ?? null;
            $cita_id = $data['cita_id'] ?? null;
            $tipo_item = $normalizarTipoItem($data['tipo_item'] ?? '');
            $pieza_servicio_id = $data['pieza_servicio_id'] ?? null;
            $descripcion = trim($data['descripcion'] ?? '');
            $cantidad = $data['cantidad'] ?? 1;
            $precio_unitario = $data['precio_unitario'] ?? 0;
            $importe = $data['importe'] ?? null;

            if (!$id || !$cita_id || $tipo_item === '' || $descripcion === '') {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "id, cita_id, tipo_item y descripcion son obligatorios"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Mantener consistencia del presupuesto con catálogo en edición.
            $precioCatalogo = $resolverPrecioDesdeCatalogo($pdo, $tipo_item, $pieza_servicio_id);
            if ($precioCatalogo !== null) {
                $precio_unitario = $precioCatalogo;
            }

            if ($importe === null || $importe === '') {
                $importe = (float)$cantidad * (float)$precio_unitario;
            }

            $stmt = $pdo->prepare("
                UPDATE presupuesto_items
                SET cita_id = ?, tipo_item = ?, pieza_servicio_id = ?, descripcion = ?, cantidad = ?, precio_unitario = ?, importe = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $cita_id,
                $tipo_item,
                $pieza_servicio_id !== '' ? $pieza_servicio_id : null,
                $descripcion,
                $cantidad,
                $precio_unitario,
                $importe,
                $id
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Item de presupuesto actualizado correctamente"
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

            $stmt = $pdo->prepare("DELETE FROM presupuesto_items WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Item de presupuesto eliminado correctamente"
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
