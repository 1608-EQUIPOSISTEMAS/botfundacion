<?php
session_start();
require_once '../../conexion/conexion.php';

header('Content-Type: application/json');

try {
    // Verificar permisos
    if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Sin permisos suficientes',
            'error_type' => 'permission'
        ]);
        exit;
    }

    // Validar datos requeridos
    $required = [
        'program_name' => 'Nombre del programa',
        'commercial_name' => 'Nombre comercial',
        'abbreviation_name' => 'Nombre abreviado',
        'alias' => 'Alias',
        'cat_category' => 'Línea de negocio',
        'cat_type_program' => 'Categoría',
        'cat_model_modality' => 'Modalidad',
        'certified_hours' => 'Horas certificadas',
        'sessions' => 'Número de sesiones',
        'version_code' => 'Código de versión'
    ];
    
    foreach ($required as $field => $label) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            echo json_encode([
                'success' => false, 
                'message' => "El campo '$label' es requerido",
                'error_type' => 'validation',
                'field' => $field
            ]);
            exit;
        }
    }

    // Validar números
    if ((int)$_POST['certified_hours'] < 1) {
        echo json_encode([
            'success' => false, 
            'message' => 'Las horas certificadas deben ser mayor a 0',
            'error_type' => 'validation'
        ]);
        exit;
    }

    if ((int)$_POST['sessions'] < 1) {
        echo json_encode([
            'success' => false, 
            'message' => 'El número de sesiones debe ser mayor a 0',
            'error_type' => 'validation'
        ]);
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Insertar programa
    $sql = "INSERT INTO programs (
                program_name,
                commercial_name,
                abbreviation_name,
                alias,
                cat_category, 
                cat_type_program, 
                cat_model_modality, 
                certified_hours, 
                active, 
                registration_date
            ) VALUES (
                :name,
                :commercial_name,
                :abbreviation_name,
                :alias,
                :category, 
                :type, 
                :modality, 
                :hours, 
                :active, 
                NOW()
            )";
    
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
        ':active' => isset($_POST['active']) && $_POST['active'] === '1' ? 1 : 0
    ]);
    
    if (!$result) {
        throw new Exception('Error al insertar el programa en la base de datos');
    }
    
    $program_id = $pdo->lastInsertId();
    
    if (!$program_id) {
        throw new Exception('No se pudo obtener el ID del programa creado');
    }
    
    // Insertar versión
    $sql = "INSERT INTO program_versions (
                program_id, 
                version_code, 
                sessions, 
                registration_date
            ) VALUES (
                :program_id, 
                :version_code, 
                :sessions, 
                NOW()
            )";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':program_id' => $program_id,
        ':version_code' => trim($_POST['version_code']),
        ':sessions' => (int)$_POST['sessions']
    ]);
    
    if (!$result) {
        throw new Exception('Error al crear la versión del programa');
    }
    
    // Confirmar transacción
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Programa creado correctamente',
        'program_id' => $program_id,
        'data' => [
            'name' => $_POST['program_name'],
            'commercial_name' => $_POST['commercial_name'],
            'alias' => $_POST['alias'],
            'version' => $_POST['version_code']
        ]
    ]);
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Error DB al crear programa: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    
    $errorMessage = 'Error en la base de datos';
    
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        $errorMessage = 'Ya existe un programa con este alias o nombre';
    } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        $errorMessage = 'Error: valores de catálogo inválidos';
    } elseif (strpos($e->getMessage(), 'Data too long') !== false) {
        $errorMessage = 'Uno de los campos excede el tamaño permitido';
    } elseif (strpos($e->getMessage(), "Unknown column") !== false) {
        $errorMessage = 'Error de estructura de base de datos: columna no encontrada';
    }
    
    echo json_encode([
        'success' => false, 
        'message' => $errorMessage,
        'error_type' => 'database',
        'error_code' => $e->getCode(),
        'error_details' => $e->getMessage()
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Error general al crear programa: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'error_type' => 'general',
        'error_details' => $e->getMessage()
    ]);
}
?>