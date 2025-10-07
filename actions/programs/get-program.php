<?php
    session_start();
    require_once '../../conexion/conexion.php';

    header('Content-Type: application/json');

    try {
        // Verificar permisos
        if (!isset($_SESSION['rol_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        // Validar ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }

        $program_id = (int)$_GET['id'];

        // Obtener datos del programa
        $sql = "SELECT 
                    p.program_id,
                    p.program_name,
                    p.commercial_name,
                    p.abbreviation_name,
                    p.alias,
                    p.cat_category,
                    p.cat_type_program,
                    p.cat_model_modality,
                    p.certified_hours,
                    p.active
                FROM programs p
                WHERE p.program_id = :program_id";
        
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
        error_log("Error al obtener programa: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los datos',
            'error_details' => $e->getMessage()
        ]);
    }
?>