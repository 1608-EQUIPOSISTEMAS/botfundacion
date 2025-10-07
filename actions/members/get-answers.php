<?php
header('Content-Type: application/json');

try {
    require_once '../../conexion/conexion.php';
    
    if (!isset($_GET['member_id'])) {
        throw new Exception('Falta el ID del plan');
    }
    
    $member_id = (int)$_GET['member_id'];
    
    $sql = "SELECT id, member_id, opcion_numero, tipo_respuesta, mensaje 
            FROM option_responses 
            WHERE member_id = :member_id 
            ORDER BY opcion_numero ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':member_id' => $member_id]);
    $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $respuestas
    ]);
    
} catch (Exception $e) {
    error_log("Error en get-answers.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>