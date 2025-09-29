<?php
header('Content-Type: application/json');
require_once '../../conexion/conexion.php'; // Aquí está $pdo

try {
    if (!isset($_POST['member_id']) || !isset($_POST['respuestas'])) {
        throw new Exception('Datos incompletos');
    }

    $member_id = (int)$_POST['member_id'];
    $respuestas = $_POST['respuestas'];

    // Iniciar transacción
    $pdo->beginTransaction();

    foreach ($respuestas as $respuesta) {
        $id = !empty($respuesta['id']) ? (int)$respuesta['id'] : null;
        $opcion_numero = (int)$respuesta['opcion_numero'];
        $tipo_respuesta = trim($respuesta['tipo_respuesta']);
        $mensaje = trim($respuesta['mensaje']);

        if (empty($tipo_respuesta) || empty($mensaje)) {
            throw new Exception("La respuesta {$opcion_numero} está incompleta");
        }

        if (!in_array($tipo_respuesta, ['texto', 'horario', 'submenu'])) {
            throw new Exception("Tipo de respuesta inválido en opción {$opcion_numero}");
        }

        if ($id) {
            // UPDATE si ya existe
            $sql = "UPDATE option_responses 
                    SET tipo_respuesta = :tipo_respuesta, mensaje = :mensaje 
                    WHERE id = :id AND member_id = :member_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tipo_respuesta', $tipo_respuesta, PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $mensaje, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        } else {
            // INSERT si no existe
            $sql = "INSERT INTO option_responses (member_id, opcion_numero, tipo_respuesta, mensaje) 
                    VALUES (:member_id, :opcion_numero, :tipo_respuesta, :mensaje)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':opcion_numero', $opcion_numero, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_respuesta', $tipo_respuesta, PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $mensaje, PDO::PARAM_STR);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar respuesta {$opcion_numero}");
        }
    }

    // Confirmar transacción
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Respuestas guardadas correctamente'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
