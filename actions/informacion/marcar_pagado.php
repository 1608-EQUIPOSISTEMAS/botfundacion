<?php
session_start();
require_once '../../conexion/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    // Verificar que el registro existe y que pay = 0
    $sqlCheck = "SELECT pay FROM bot_history WHERE id = :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    $registro = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    if (!$registro) {
        echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
        exit;
    }
    
    if ($registro['pay'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Este registro ya está marcado como pagado']);
        exit;
    }
    
    // Actualizar pay a 1
    $sql = "UPDATE bot_history SET pay = 1 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Registro marcado como pagado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}