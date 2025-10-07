<?php
    session_start();
    require_once '../../conexion/conexion.php';

    header('Content-Type: application/json');

    try {
        // Verificar permisos
        if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Sin permisos suficientes'
            ]);
            exit;
        }

        // Validar ID
        if (!isset($_POST['program_id']) || empty($_POST['program_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'ID de programa no proporcionado'
            ]);
            exit;
        }

        $program_id = (int)$_POST['program_id'];

        // Verificar que el programa existe
        $sql = "SELECT program_id, program_name FROM programs WHERE program_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $program_id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            echo json_encode([
                'success' => false, 
                'message' => 'Programa no encontrado'
            ]);
            exit;
        }

        // Soft delete: Actualizar active a 0
        $sql = "UPDATE programs SET active = 0 WHERE program_id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([':id' => $program_id]);

        if (!$result) {
            throw new Exception('Error al desactivar el programa');
        }

        // Log de la acción (opcional)
        error_log("Programa desactivado - ID: {$program_id}, Nombre: {$program['program_name']}");

        echo json_encode([
            'success' => true,
            'message' => 'Programa desactivado correctamente',
            'program_id' => $program_id
        ]);

    } catch (PDOException $e) {
        error_log("Error DB al eliminar programa: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error en la base de datos',
            'error_details' => $e->getMessage()
        ]);
        
    } catch (Exception $e) {
        error_log("Error al eliminar programa: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
?>