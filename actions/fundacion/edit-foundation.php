<?php
// procesar/bot/editar.php
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once '../../conexion/conexion.php';

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Validar datos requeridos
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$welcome = isset($_POST['welcome']) ? trim($_POST['welcome']) : '';
$sesion = isset($_POST['sesion']) ? trim($_POST['sesion']) : '';
$key_words = isset($_POST['key_words']) ? trim($_POST['key_words']) : '';
$final_text = isset($_POST['final_text']) ? trim($_POST['final_text']) : '';

if (empty($id)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
    exit;
}

try {
    // Obtener rutas actuales
    $sqlCurrent = "SELECT presentation_route, brochure_route, modality_first_route, modality_second_route, inversion_route FROM bot_foundation WHERE id = :id";
    $stmtCurrent = $pdo->prepare($sqlCurrent);
    $stmtCurrent->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCurrent->execute();
    $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC);
    
    $presentation_route = $current['presentation_route'] ?? '';
    $brochure_route = $current['brochure_route'] ?? '';
    $modality_first_route = $current['modality_first_route'] ?? '';
    $modality_second_route = $current['modality_second_route'] ?? '';
    $inversion_route = $current['inversion_route'] ?? '';
    
    // Procesar imagen de presentación
    if (isset($_FILES['presentation_image']) && $_FILES['presentation_image']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['presentation_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($imagen['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }
        
        if ($imagen['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
            exit;
        }
        
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'presentation_' . time() . '.' . $extension;
        $rutaDestino = '../../post/bot/' . $nombreArchivo;
        
        if (!file_exists('../../post/bot/')) {
            mkdir('../../post/bot/', 0777, true);
        }
        
        if (!empty($presentation_route) && file_exists('../../' . $presentation_route)) {
            unlink('../../' . $presentation_route);
        }
        
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            $presentation_route = 'post/bot/' . $nombreArchivo;
        }
    }
    
    // Procesar imagen modalidad 1
    if (isset($_FILES['modality_first_image']) && $_FILES['modality_first_image']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['modality_first_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024;
        
        if (!in_array($imagen['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }
        
        if ($imagen['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
            exit;
        }
        
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'modality1_' . time() . '.' . $extension;
        $rutaDestino = '../../post/bot/' . $nombreArchivo;
        
        if (!file_exists('../../post/bot/')) {
            mkdir('../../post/bot/', 0777, true);
        }
        
        if (!empty($modality_first_route) && file_exists('../../' . $modality_first_route)) {
            unlink('../../' . $modality_first_route);
        }
        
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            $modality_first_route = 'post/bot/' . $nombreArchivo;
        }
    }
    
    // Procesar imagen modalidad 2
    if (isset($_FILES['modality_second_image']) && $_FILES['modality_second_image']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['modality_second_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024;
        
        if (!in_array($imagen['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }
        
        if ($imagen['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
            exit;
        }
        
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'modality2_' . time() . '.' . $extension;
        $rutaDestino = '../../post/bot/' . $nombreArchivo;
        
        if (!file_exists('../../post/bot/')) {
            mkdir('../../post/bot/', 0777, true);
        }
        
        if (!empty($modality_second_route) && file_exists('../../' . $modality_second_route)) {
            unlink('../../' . $modality_second_route);
        }
        
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            $modality_second_route = 'post/bot/' . $nombreArchivo;
        }
    }
    
    // Procesar PDF Brochure
    if (isset($_FILES['brochure_pdf']) && $_FILES['brochure_pdf']['error'] === UPLOAD_ERR_OK) {
        $pdf = $_FILES['brochure_pdf'];
        $maxSizePdf = 10 * 1024 * 1024;
        
        if ($pdf['type'] !== 'application/pdf') {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
            exit;
        }
        
        if ($pdf['size'] > $maxSizePdf) {
            echo json_encode(['success' => false, 'message' => 'El PDF no debe superar los 10MB']);
            exit;
        }
        
        $nombreArchivoPdf = 'brochure_' . time() . '.pdf';
        $rutaDestinoPdf = '../../brochure/bot/' . $nombreArchivoPdf;
        
        if (!file_exists('../../brochure/bot/')) {
            mkdir('../../brochure/bot/', 0777, true);
        }
        
        if (!empty($brochure_route) && file_exists('../../' . $brochure_route)) {
            unlink('../../' . $brochure_route);
        }
        
        if (move_uploaded_file($pdf['tmp_name'], $rutaDestinoPdf)) {
            $brochure_route = 'brochure/bot/' . $nombreArchivoPdf;
        }
    }
    
    // Procesar PDF Inversión
    if (isset($_FILES['inversion_pdf']) && $_FILES['inversion_pdf']['error'] === UPLOAD_ERR_OK) {
        $pdf = $_FILES['inversion_pdf'];
        $maxSizePdf = 10 * 1024 * 1024;
        
        if ($pdf['type'] !== 'application/pdf') {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
            exit;
        }
        
        if ($pdf['size'] > $maxSizePdf) {
            echo json_encode(['success' => false, 'message' => 'El PDF no debe superar los 10MB']);
            exit;
        }
        
        $nombreArchivoPdf = 'inversion_' . time() . '.pdf';
        $rutaDestinoPdf = '../../brochure/bot/' . $nombreArchivoPdf;
        
        if (!file_exists('../../brochure/bot/')) {
            mkdir('../../brochure/bot/', 0777, true);
        }
        
        if (!empty($inversion_route) && file_exists('../../' . $inversion_route)) {
            unlink('../../' . $inversion_route);
        }
        
        if (move_uploaded_file($pdf['tmp_name'], $rutaDestinoPdf)) {
            $inversion_route = 'brochure/bot/' . $nombreArchivoPdf;
        }
    }
    
    // Actualizar base de datos
    $sql = "UPDATE bot_foundation SET 
            welcome = :welcome, 
            presentation_route = :presentation_route, 
            brochure_route = :brochure_route, 
            modality_first_route = :modality_first_route, 
            modality_second_route = :modality_second_route, 
            sesion = :sesion, 
            inversion_route = :inversion_route, 
            key_words = :key_words, 
            final_text = :final_text 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':welcome', $welcome, PDO::PARAM_STR);
    $stmt->bindParam(':presentation_route', $presentation_route, PDO::PARAM_STR);
    $stmt->bindParam(':brochure_route', $brochure_route, PDO::PARAM_STR);
    $stmt->bindParam(':modality_first_route', $modality_first_route, PDO::PARAM_STR);
    $stmt->bindParam(':modality_second_route', $modality_second_route, PDO::PARAM_STR);
    $stmt->bindParam(':sesion', $sesion, PDO::PARAM_STR);
    $stmt->bindParam(':inversion_route', $inversion_route, PDO::PARAM_STR);
    $stmt->bindParam(':key_words', $key_words, PDO::PARAM_STR);
    $stmt->bindParam(':final_text', $final_text, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Configuración actualizada correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la configuración'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>