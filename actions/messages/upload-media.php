<?php
session_start();
require_once '../../conexion/conexioninmobiliaria.php';

header('Content-Type: application/json');

try {

    $message_id = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
    $media_type = $_POST['media_type'] ?? '';
    
    if (!$message_id) {
        throw new Exception('ID de mensaje inválido');
    }

    if (!in_array($media_type, ['IMAGE', 'AUDIO', 'DOCUMENT', 'VIDEO'])) {
        throw new Exception('Tipo de media inválido');
    }

    if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
        throw new Exception('No se recibieron archivos');
    }

    // Configuración por tipo
    $config = [
        'IMAGE' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'max_size' => 5 * 1024 * 1024, // 5MB
            'path' => 'media/campaigns/messages/images/'
        ],
        'AUDIO' => [
            'extensions' => ['mp3', 'wav', 'ogg'],
            'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
            'max_size' => 10 * 1024 * 1024, // 10MB
            'path' => 'media/campaigns/messages/audios/'
        ],
        'DOCUMENT' => [
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'max_size' => 10 * 1024 * 1024, // 10MB
            'path' => 'media/campaigns/messages/documents/'
        ],
        'VIDEO' => [
            'extensions' => ['mp4', 'mov', 'avi'],
            'mime_types' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
            'max_size' => 50 * 1024 * 1024, // 50MB
            'path' => 'media/campaigns/messages/videos/'
        ]
    ];

    $typeConfig = $config[$media_type];
    $uploadPath = '../../' . $typeConfig['path'];

    // Crear directorio si no existe
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    // Obtener el último sort_order
    $stmt = $pdoInmobiliaria->prepare(
        "SELECT COALESCE(MAX(sort_order), 0) as max_order 
         FROM message_media 
         WHERE message_id = ? AND deleted_at IS NULL"
    );
    $stmt->execute([$message_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $sortOrder = $result['max_order'];

    $uploadedCount = 0;
    $errors = [];
    $created_by = $_SESSION['nombre'] ?? 'admin';

    // Procesar cada archivo
    $fileCount = count($_FILES['files']['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Error al subir: " . $_FILES['files']['name'][$i];
            continue;
        }

        $fileName = $_FILES['files']['name'][$i];
        $fileTmpPath = $_FILES['files']['tmp_name'][$i];
        $fileSize = $_FILES['files']['size'][$i];
        $fileMimeType = $_FILES['files']['type'][$i];
        
        // Validar extensión
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $typeConfig['extensions'])) {
            $errors[] = "$fileName: Extensión no permitida";
            continue;
        }

        // Validar tamaño
        if ($fileSize > $typeConfig['max_size']) {
            $maxSizeMB = $typeConfig['max_size'] / (1024 * 1024);
            $errors[] = "$fileName: Tamaño excede el límite de {$maxSizeMB}MB";
            continue;
        }

        // Generar nombre único
        $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
        $destinationPath = $uploadPath . $newFileName;

        // Mover archivo
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            $sortOrder++;
            
            // Guardar en BD
            $stmt = $pdoInmobiliaria->prepare(
                "INSERT INTO message_media 
                (message_id, media_type, file_path, file_name, file_size, mime_type, sort_order, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            
            $relativePath = $typeConfig['path'] . $newFileName;
            
            $stmt->execute([
                $message_id,
                $media_type,
                $relativePath,
                $fileName,
                $fileSize,
                $fileMimeType,
                $sortOrder,
                $created_by
            ]);
            
            $uploadedCount++;
        } else {
            $errors[] = "$fileName: Error al mover el archivo";
        }
    }

    if ($uploadedCount === 0 && !empty($errors)) {
        throw new Exception('No se pudo subir ningún archivo. ' . implode(', ', $errors));
    }

    echo json_encode([
        'success' => true,
        'message' => "$uploadedCount archivo(s) subido(s) correctamente",
        'uploaded_count' => $uploadedCount,
        'errors' => $errors
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>