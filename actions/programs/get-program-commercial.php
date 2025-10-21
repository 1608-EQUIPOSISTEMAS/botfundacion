<?php
    session_start();
    require_once '../../conexion/conexion.php';

    header('Content-Type: application/json');

    try {
        if (!isset($_SESSION['rol_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }

        $program_id = (int)$_GET['id'];

        $sql = "SELECT 
                    program_id,
                    initial_greeting,
                    voice_url,
                    benefits,
                    video_url,
                    img_url,
                    sales_page_url
                FROM programs
                WHERE program_id = :program_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':program_id' => $program_id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            echo json_encode(['success' => false, 'message' => 'Programa no encontrado']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $program
        ]);

    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener datos',
            'error_details' => $e->getMessage()
        ]);
    }
?>