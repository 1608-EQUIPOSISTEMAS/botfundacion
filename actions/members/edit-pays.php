<?php
session_start();
require_once '../../conexion/conexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }
    
    if (!isset($_POST['id']) || !isset($_POST['tipo'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    $id = intval($_POST['id']);
    $tipo = trim($_POST['tipo']);
    
    if (!in_array($tipo, ['texto', 'imagen'])) {
        echo json_encode(['success' => false, 'message' => 'Tipo de contenido inválido']);
        exit;
    }
    
    $contenido = '';
    
    if ($tipo === 'texto') {
        if (!isset($_POST['contenido']) || empty(trim($_POST['contenido']))) {
            echo json_encode(['success' => false, 'message' => 'El contenido no puede estar vacío']);
            exit;
        }
        $contenido = trim($_POST['contenido']);
        
    } else {
        // Subir imagen
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
            exit;
        }
        
        $file = $_FILES['imagen'];
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        
        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
            exit;
        }
        
        $uploadDir = '../../post/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = 'payment_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $newFileName;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen']);
            exit;
        }
        
        $contenido = 'post/' . $newFileName;
    }
    
    // Actualizar en la base de datos
    $sql = "UPDATE payment_methods 
            SET tipo = :tipo, contenido = :contenido 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo,
        ':contenido' => $contenido,
        ':id' => $id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Método de pago actualizado correctamente'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}