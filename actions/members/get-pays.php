<?php
session_start();
require_once '../../conexion/conexion.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['member_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de membresía no proporcionado']);
        exit;
    }
    
    $member_id = intval($_GET['member_id']);
    
    // Query corregida - asegurando el filtro correcto
    $sql = "SELECT pm.id, pm.metodo, pm.orden, pm.tipo, pm.contenido
            FROM payment_methods pm
            INNER JOIN option_responses ore ON pm.response_id = ore.id
            WHERE ore.member_id = :member_id
            ORDER BY pm.orden ASC, pm.metodo ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':member_id' => $member_id]);
    
    $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $payment_methods,
        'count' => count($payment_methods)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}