<?php
    session_start();
    require_once '../../conexion/conexion.php';

    header('Content-Type: application/json');

    try {
        if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
            exit;
        }

        if (!isset($_POST['program_id']) || empty($_POST['program_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }

        $program_id = (int)$_POST['program_id'];

        // Verificar que existe
        $sql = "SELECT program_id, brochure_url, voice_url, video_url, img_url FROM programs WHERE program_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $program_id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            echo json_encode(['success' => false, 'message' => 'Programa no encontrado']);
            exit;
        }

        // Directorio de uploads
        $upload_dir = '../../uploads/programs/' . $program_id . '/';
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validar JSON
        $json_references = trim($_POST['json_references'] ?? '');
        if ($json_references) {
            $decoded = json_decode($json_references);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'JSON de sinónimos inválido: ' . json_last_error_msg()
                ]);
                exit;
            }
        }

        // Función para subir archivos
        function uploadFile($file, $upload_dir, $allowed_types, $max_size = 10485760) {
            if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
                return null; // No hay archivo
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Error al subir el archivo: ' . $file['error']);
            }

            if ($file['size'] > $max_size) {
                throw new Exception('El archivo excede el tamaño máximo permitido (10MB)');
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowed_types)) {
                throw new Exception('Tipo de archivo no permitido: ' . $extension);
            }

            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Error al mover el archivo');
            }

            // Retornar ruta relativa desde la raíz del proyecto
            return 'uploads/programs/' . basename(dirname($filepath)) . '/' . $filename;
        }

        // Procesar archivos
        $brochure_url = $current['brochure_url'];
        $voice_url = $current['voice_url'];
        $video_url = $current['video_url'];
        $img_url = $current['img_url'];

        // Brochure (PDF)
        if (isset($_FILES['brochure_file'])) {
            $new_brochure = uploadFile($_FILES['brochure_file'], $upload_dir, ['pdf'], 20971520); // 20MB
            if ($new_brochure) {
                // Eliminar archivo anterior si existe
                if ($brochure_url && file_exists('../../' . $brochure_url)) {
                    unlink('../../' . $brochure_url);
                }
                $brochure_url = $new_brochure;
            }
        }

        // Audio (MP3)
        if (isset($_FILES['voice_file'])) {
            $new_voice = uploadFile($_FILES['voice_file'], $upload_dir, ['mp3','ogg'], 20971520); // 20MB
            if ($new_voice) {
                if ($voice_url && file_exists('../../' . $voice_url)) {
                    unlink('../../' . $voice_url);
                }
                $voice_url = $new_voice;
            }
        }

        // Video (MP4)
        if (isset($_FILES['video_file'])) {
            $new_video = uploadFile($_FILES['video_file'], $upload_dir, ['mp4'], 52428800); // 50MB
            if ($new_video) {
                if ($video_url && file_exists('../../' . $video_url)) {
                    unlink('../../' . $video_url);
                }
                $video_url = $new_video;
            }
        }

        // Imagen
        if (isset($_FILES['img_file'])) {
            $new_img = uploadFile($_FILES['img_file'], $upload_dir, ['jpg', 'jpeg', 'png', 'gif', 'webp'], 5242880); // 5MB
            if ($new_img) {
                if ($img_url && file_exists('../../' . $img_url)) {
                    unlink('../../' . $img_url);
                }
                $img_url = $new_img;
            }
        }

        // Actualizar base de datos
        $sql = "UPDATE programs SET
                    json_references = :json_references,
                    initial_greeting = :initial_greeting,
                    brochure_url = :brochure_url,
                    voice_url = :voice_url,
                    benefits = :benefits,
                    video_url = :video_url,
                    img_url = :img_url,
                    sales_page_url = :sales_page_url
                WHERE program_id = :program_id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':json_references' => $json_references ?: null,
            ':initial_greeting' => trim($_POST['initial_greeting'] ?? '') ?: null,
            ':brochure_url' => $brochure_url ?: null,
            ':voice_url' => $voice_url ?: null,
            ':benefits' => trim($_POST['benefits'] ?? '') ?: null,
            ':video_url' => $video_url ?: null,
            ':img_url' => $img_url ?: null,
            ':sales_page_url' => trim($_POST['sales_page_url'] ?? '') ?: null,
            ':program_id' => $program_id
        ]);

        if (!$result) {
            throw new Exception('Error al actualizar en la base de datos');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Información comercial actualizada correctamente',
            'uploaded_files' => [
                'brochure' => $brochure_url,
                'voice' => $voice_url,
                'video' => $video_url,
                'img' => $img_url
            ]
        ]);

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_details' => $e->getMessage()
        ]);
    }
?>