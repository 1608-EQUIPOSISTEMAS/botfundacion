<?php
header('Content-Type: application/json');
require_once '../../conexion/conexion.php'; // Aquí se carga $pdo desde conexion.php

try {
    if (!isset($_GET['member_id'])) {
        throw new Exception('ID de member no proporcionado');
    }

    $member_id = (int)$_GET['member_id'];

    $sql = "SELECT id, member_id, opcion_numero, tipo_respuesta, mensaje 
            FROM option_responses 
            WHERE member_id = :member_id 
            ORDER BY opcion_numero ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->execute();

    $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $respuestas,
        'message' => 'Respuestas obtenidas correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}