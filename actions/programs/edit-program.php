<?php
    session_start();
    require_once '../../conexion/conexion.php';

    header('Content-Type: application/json');

    try {
        // Verificar permisos
        if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
            exit;
        }

        // Validar campos requeridos
        $required = [
            'program_id' => 'ID del programa',
            'program_name' => 'Nombre del programa',
            'commercial_name' => 'Nombre comercial',
            'abbreviation_name' => 'Nombre abreviado',
            'alias' => 'Alias',
            'cat_category' => 'Línea de negocio',
            'cat_type_program' => 'Categoría',
            'cat_model_modality' => 'Modalidad',
            'certified_hours' => 'Horas certificadas'
        ];
        
        foreach ($required as $field => $label) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                echo json_encode([
                    'success' => false,
                    'message' => "Campo requerido: $label",
                    'field' => $field
                ]);
                exit;
            }
        }

        // Validaciones
        if ((int)$_POST['certified_hours'] < 1) {
            echo json_encode(['success' => false, 'message' => 'Horas certificadas inválidas']);
            exit;
        }

        $program_id = (int)$_POST['program_id'];

        // Verificar que existe
        $sql = "SELECT program_id FROM programs WHERE program_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $program_id]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Programa no encontrado']);
            exit;
        }

        // Actualizar programa
        $sql = "UPDATE programs SET
                    program_name = :name,
                    commercial_name = :commercial_name,
                    abbreviation_name = :abbreviation_name,
                    alias = :alias,
                    cat_category = :category,
                    cat_type_program = :type,
                    cat_model_modality = :modality,
                    certified_hours = :hours,
                    active = :active
                WHERE program_id = :program_id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => trim($_POST['program_name']),
            ':commercial_name' => strtoupper(trim($_POST['commercial_name'])),
            ':abbreviation_name' => strtoupper(trim($_POST['abbreviation_name'])),
            ':alias' => strtoupper(trim($_POST['alias'])),
            ':category' => (int)$_POST['cat_category'],
            ':type' => (int)$_POST['cat_type_program'],
            ':modality' => (int)$_POST['cat_model_modality'],
            ':hours' => (int)$_POST['certified_hours'],
            ':active' => isset($_POST['active']) && $_POST['active'] === '1' ? 1 : 0,
            ':program_id' => $program_id
        ]);

        if (!$result) {
            throw new Exception('Error al actualizar el programa');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Programa actualizado correctamente',
            'program_id' => $program_id
        ]);

    } catch (PDOException $e) {
        error_log("Error DB: " . $e->getMessage());
        
        $errorMsg = 'Error en la base de datos';
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $errorMsg = 'Ya existe un programa con ese alias o nombre';
        }
        
        echo json_encode([
            'success' => false,
            'message' => $errorMsg,
            'error_details' => $e->getMessage()
        ]);
        
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
?>