<?php
session_start();
header('Content-Type: application/json');

// Log para debug
error_log("=== INICIO REORDER ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Raw input: " . file_get_contents('php://input'));

try {
    require_once '../../conexion/conexioninmobiliaria.php';
    
    // Obtener datos JSON
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    error_log("Input decodificado: " . print_r($input, true));
    
    if (!$input) {
        throw new Exception('No se recibieron datos JSON válidos');
    }
    
    if (!isset($input['messages']) || !is_array($input['messages'])) {
        throw new Exception('Formato de datos inválido - se esperaba array de messages');
    }

    if (empty($input['messages'])) {
        throw new Exception('No hay mensajes para actualizar');
    }

    $campaign_id = isset($input['campaign_id']) ? (int)$input['campaign_id'] : 1;
    $updated_by = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Sistema';
    
    error_log("Campaign ID: $campaign_id");
    error_log("Updated by: $updated_by");
    
    // Iniciar transacción
    $pdoInmobiliaria->beginTransaction();
    
    $updateCount = 0;
    
    foreach ($input['messages'] as $message) {
        if (!isset($message['id']) || !isset($message['order'])) {
            error_log("Mensaje sin ID u orden: " . print_r($message, true));
            continue;
        }
        
        $messageId = (int)$message['id'];
        $newOrder = (int)$message['order'];
        
        error_log("Actualizando mensaje ID: $messageId a orden: $newOrder");
        
        $sql = "UPDATE messages 
                SET sort_order = :order,
                    updated_at = NOW(),
                    updated_by = :user
                WHERE id = :id 
                AND campaign_id = :campaign_id
                AND deleted_at IS NULL";
        
        $stmt = $pdoInmobiliaria->prepare($sql);
        $result = $stmt->execute([
            'order' => $newOrder,
            'id' => $messageId,
            'campaign_id' => $campaign_id,
            'user' => $updated_by
        ]);
        
        if ($result) {
            $rowsAffected = $stmt->rowCount();
            error_log("Filas afectadas: $rowsAffected");
            if ($rowsAffected > 0) {
                $updateCount++;
            }
        }
    }
    
    // Confirmar transacción
    $pdoInmobiliaria->commit();
    
    error_log("Total actualizado: $updateCount mensajes");
    error_log("=== FIN REORDER EXITOSO ===");
    
    echo json_encode([
        'success' => true,
        'message' => "Se actualizaron $updateCount mensajes correctamente",
        'updated_count' => $updateCount
    ]);

} catch (Exception $e) {
    if (isset($pdoInmobiliaria) && $pdoInmobiliaria->inTransaction()) {
        $pdoInmobiliaria->rollBack();
    }
    
    error_log("ERROR en reorder: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    error_log("=== FIN REORDER CON ERROR ===");
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => $e->getTraceAsString()
    ]);
}