<?php
// procesar/bot/obtener.php
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once '../../conexion/conexion.php';

// Verificar que se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
    exit;
}

$id = intval($_GET['id']);

try {
    // Obtener datos del bot
    $sql = "SELECT id, welcome, presentation_route, brochure_route, modality_first_route, modality_second_route, sesion, inversion_route, key_words, final_text 
            FROM bot_foundation WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $bot = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($bot) {
        echo json_encode([
            'success' => true,
            'data' => $bot
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Configuración no encontrada'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos: ' . $e->getMessage()
    ]);
}
?>