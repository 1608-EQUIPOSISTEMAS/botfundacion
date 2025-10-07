<?php
header('Content-Type: application/json');
require_once '../../conexion/conexion.php';

try {
    if (!isset($_POST['member_id']) || !isset($_POST['opciones'])) {
        throw new Exception('Datos incompletos');
    }
    
    $member_id = (int)$_POST['member_id'];
    $opciones = $_POST['opciones'];
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    foreach ($opciones as $opcion) {
        $id = isset($opcion['id']) && !empty($opcion['id']) ? (int)$opcion['id'] : null;
        $numero = (int)$opcion['numero'];
        $texto = trim($opcion['texto']);
        
        if (empty($texto)) {
            throw new Exception("La opción {$numero} no puede estar vacía");
        }
        
        if ($id) {
            // Actualizar existente
            $sql = "UPDATE member_options 
                    SET opcion_texto = ? 
                    WHERE id = ? AND member_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$texto, $id, $member_id]);
        } else {
            // Insertar nuevo
            $sql = "INSERT INTO member_options (member_id, opcion_numero, opcion_texto) 
                    VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$member_id, $numero, $texto]);
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Opciones guardadas correctamente'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>