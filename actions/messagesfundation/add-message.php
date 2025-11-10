<?php
    session_start();
    require_once '../../conexion/conexionfundation.php';

    header('Content-Type: application/json');

    try {
        // Obtener datos del formulario
        $campaign_id = filter_input(INPUT_POST, 'campaign_id', FILTER_VALIDATE_INT);
        $message_type_id = filter_input(INPUT_POST, 'message_type_id', FILTER_VALIDATE_INT);
        $content = trim($_POST['content'] ?? '');
        $delay_seconds = filter_input(INPUT_POST, 'delay_seconds', FILTER_VALIDATE_INT);
        $is_active = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_INT);
        $notes = trim($_POST['notes'] ?? '');
        $created_by = $_SESSION['nombre'] ?? 'admin';

        // Validaciones
        if (!$campaign_id) {
            throw new Exception('ID de campaña inválido');
        }

        if (!$message_type_id) {
            throw new Exception('Debe seleccionar un tipo de mensaje');
        }

        if ($delay_seconds === false || $delay_seconds < 0) {
            throw new Exception('El delay debe ser un número válido mayor o igual a 0');
        }

        if ($is_active === false || !in_array($is_active, [0, 1])) {
            throw new Exception('Estado inválido');
        }

        // Verificar que la campaña existe
        $stmt = $pdoInmobiliaria->prepare("SELECT id FROM campaigns WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$campaign_id]);
        if (!$stmt->fetch()) {
            throw new Exception('La campaña no existe');
        }

        // Verificar que el tipo de mensaje existe y obtener sus propiedades
        $stmt = $pdoInmobiliaria->prepare("SELECT allows_content, requires_media FROM message_types WHERE id = ?");
        $stmt->execute([$message_type_id]);
        $messageType = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$messageType) {
            throw new Exception('Tipo de mensaje inválido');
        }

        // Si el tipo no permite contenido, limpiarlo
        if ($messageType['allows_content'] == 0) {
            $content = null;
        }

        // Obtener el siguiente sort_order disponible
        $stmt = $pdoInmobiliaria->prepare(
            "SELECT COALESCE(MAX(sort_order), 0) + 10 as next_order 
            FROM messages 
            WHERE campaign_id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$campaign_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $sort_order = $result['next_order'];

        // Insertar el mensaje
        $sql = "INSERT INTO messages 
                (campaign_id, message_type_id, content, sort_order, is_active, delay_seconds, notes, created_by, created_at, updated_at) 
                VALUES 
                (:campaign_id, :message_type_id, :content, :sort_order, :is_active, :delay_seconds, :notes, :created_by, NOW(), NOW())";
        
        $stmt = $pdoInmobiliaria->prepare($sql);
        $stmt->execute([
            'campaign_id' => $campaign_id,
            'message_type_id' => $message_type_id,
            'content' => $content,
            'sort_order' => $sort_order,
            'is_active' => $is_active,
            'delay_seconds' => $delay_seconds,
            'notes' => $notes,
            'created_by' => $created_by
        ]);

        $message_id = $pdoInmobiliaria->lastInsertId();

        // Preparar mensaje de respuesta
        $responseMessage = 'Mensaje creado exitosamente';
        if ($messageType['requires_media'] == 1) {
            $responseMessage .= '. Ahora puedes agregar los archivos multimedia requeridos.';
        }

        echo json_encode([
            'success' => true,
            'message' => $responseMessage,
            'data' => [
                'message_id' => $message_id,
                'sort_order' => $sort_order,
                'requires_media' => $messageType['requires_media'] == 1
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Error en add-message.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
?>