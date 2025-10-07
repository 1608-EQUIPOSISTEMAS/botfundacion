<?php
// procesar/editar.php
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
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
$beneficio = isset($_POST['beneficio']) ? trim($_POST['beneficio']) : '';

if (empty($id) || empty($nombre) || empty($precio) || empty($beneficio)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);
    exit;
}

try {
    // Obtener rutas actuales
    $sqlCurrent = "SELECT ruta_post, ruta_pdf FROM members WHERE id = :id";
    $stmtCurrent = $pdo->prepare($sqlCurrent);
    $stmtCurrent->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCurrent->execute();
    $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC);
    
    $ruta_post = $current['ruta_post'] ?? '';
    $ruta_pdf = $current['ruta_pdf'] ?? '';
    
    // Procesar imagen si se subió una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validar tipo
        if (!in_array($imagen['type'], $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Formato de imagen no permitido. Solo JPG, PNG, WEBP'
            ]);
            exit;
        }
        
        // Validar tamaño
        if ($imagen['size'] > $maxSize) {
            echo json_encode([
                'success' => false,
                'message' => 'La imagen no debe superar los 5MB'
            ]);
            exit;
        }
        
        // Generar nombre único y guardar
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'plan_' . $id . '_' . time() . '.' . $extension;
        $rutaDestino = '../../post/' . $nombreArchivo; // Usando ../../ como solicitaste
        
        // Crear directorio si no existe
        if (!file_exists('../../post/')) {
            mkdir('../../post/', 0777, true);
        }
        
        // Eliminar imagen anterior si existe
        if (!empty($ruta_post) && file_exists('../../' . $ruta_post)) {
            unlink('../../' . $ruta_post);
        }
        
        // Mover archivo
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            $ruta_post = 'post/' . $nombreArchivo;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir la imagen'
            ]);
            exit;
        }
    }
    
    // Procesar PDF si se subió uno nuevo
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $pdf = $_FILES['pdf'];
        $maxSizePdf = 10 * 1024 * 1024; // 10MB
        
        // Validar tipo
        if ($pdf['type'] !== 'application/pdf') {
            echo json_encode([
                'success' => false,
                'message' => 'Solo se permiten archivos PDF'
            ]);
            exit;
        }
        
        // Validar tamaño
        if ($pdf['size'] > $maxSizePdf) {
            echo json_encode([
                'success' => false,
                'message' => 'El PDF no debe superar los 10MB'
            ]);
            exit;
        }
        
        // Generar nombre único y guardar
        $nombreArchivoPdf = 'brochure_' . $id . '_' . time() . '.pdf';
        $rutaDestinoPdf = '../../brochure/' . $nombreArchivoPdf; // Usando ../../ como solicitaste
        
        // Crear directorio si no existe
        if (!file_exists('../../brochure/')) {
            mkdir('../../brochure/', 0777, true);
        }
        
        // Eliminar PDF anterior si existe
        if (!empty($ruta_pdf) && file_exists('../../' . $ruta_pdf)) {
            unlink('../../' . $ruta_pdf);
        }
        
        // Mover archivo
        if (move_uploaded_file($pdf['tmp_name'], $rutaDestinoPdf)) {
            $ruta_pdf = 'brochure/' . $nombreArchivoPdf;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir el PDF'
            ]);
            exit;
        }
    }
    
    // Actualizar base de datos
    $sql = "UPDATE members SET 
            nombre = :nombre, 
            precio = :precio, 
            beneficio = :beneficio, 
            ruta_post = :ruta_post, 
            ruta_pdf = :ruta_pdf 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
    $stmt->bindParam(':beneficio', $beneficio, PDO::PARAM_STR);
    $stmt->bindParam(':ruta_post', $ruta_post, PDO::PARAM_STR);
    $stmt->bindParam(':ruta_pdf', $ruta_pdf, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Plan actualizado correctamente',
            'data' => [
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $precio,
                'beneficio' => $beneficio,
                'ruta_post' => $ruta_post,
                'ruta_pdf' => $ruta_pdf
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el plan'
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