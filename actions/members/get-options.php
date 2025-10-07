<?php
header('Content-Type: application/json');
require_once '../../conexion/conexion.php';

try {
    if (!isset($_GET['member_id'])) {
        throw new Exception('ID de member no proporcionado');
    }
    
    $member_id = (int)$_GET['member_id'];
    
    $sql = "SELECT id, member_id, opcion_numero, opcion_texto 
            FROM member_options 
            WHERE member_id = ? 
            ORDER BY opcion_numero ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $opciones,
        'message' => 'Opciones obtenidas correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>