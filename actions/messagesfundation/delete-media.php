<?php
session_start();
require_once '../../conexion/conexionfundation.php';

header('Content-Type: application/json');

try {

    $media_id = filter_input(INPUT_POST, 'media_id', FILTER_VALIDATE_INT);
    
    if (!$media_id) {
        throw new Exception('ID de media inválido');
    }

    // Obtener información del archivo antes de eliminar
    $stmt = $pdoInmobiliaria->prepare(
        "SELECT file_path FROM message_media WHERE id = ? AND deleted_at IS NULL"
    );
    $stmt->execute([$media_id]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$media) {
        throw new Exception('Archivo no encontrado');
    }

    $updated_by = $_SESSION['nombre'] ?? 'admin';

    // Soft delete en BD
    $stmt = $pdoInmobiliaria->prepare(
        "UPDATE message_media 
         SET deleted_at = NOW(), updated_by = ?
         WHERE id = ?"
    );
    $stmt->execute([$updated_by, $media_id]);

    // Eliminar archivo físico
    $filePath = '../../' . $media['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Archivo eliminado correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>