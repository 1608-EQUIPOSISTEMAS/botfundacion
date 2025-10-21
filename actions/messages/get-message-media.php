<?php
session_start();
require_once '../../conexion/conexioninmobiliaria.php';

header('Content-Type: application/json');

try {


    $message_id = filter_input(INPUT_GET, 'message_id', FILTER_VALIDATE_INT);
    
    if (!$message_id) {
        throw new Exception('ID de mensaje inválido');
    }

    // Obtener tipo de mensaje
    $stmt = $pdoInmobiliaria->prepare(
        "SELECT mt.type_code 
         FROM messages m
         INNER JOIN message_types mt ON m.message_type_id = mt.id
         WHERE m.id = ? AND m.deleted_at IS NULL"
    );
    $stmt->execute([$message_id]);
    $messageData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$messageData) {
        throw new Exception('Mensaje no encontrado');
    }

    // Obtener archivos multimedia
    $stmt = $pdoInmobiliaria->prepare(
        "SELECT 
            id,
            media_type,
            file_path,
            file_name,
            file_size,
            mime_type,
            sort_order,
            created_at
         FROM message_media
         WHERE message_id = ? AND deleted_at IS NULL
         ORDER BY sort_order ASC, created_at ASC"
    );
    $stmt->execute([$message_id]);
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message_type' => $messageData['type_code'],
        'media' => $media
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>