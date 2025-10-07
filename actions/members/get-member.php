<?php
// procesar/obtener.php
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
    // Obtener datos del plan
    $sql = "SELECT id, nombre, ruta_post, beneficio, ruta_pdf, precio FROM members WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($plan) {
        echo json_encode([
            'success' => true,
            'data' => $plan
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Plan no encontrado'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos: ' . $e->getMessage()
    ]);
}
?>