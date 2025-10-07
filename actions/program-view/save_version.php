<?php
session_start();
require_once '../../conexion/conexion.php';

header('Content-Type: application/json');

try {
    // Validar que vengan los datos básicos
    if (!isset($_POST['program_id']) || !isset($_POST['version_code']) || !isset($_POST['sessions'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $program_id = (int)$_POST['program_id'];
    $version_code = trim($_POST['version_code']);
    $sessions = (int)$_POST['sessions'];
    $cat_category_course = (int)$_POST['cat_category_course'];
    $cat_type_modality = (int)$_POST['cat_type_modality'];
    $active = isset($_POST['active']) ? (int)$_POST['active'] : 1;
    $requires_structure = isset($_POST['requires_structure']) ? (int)$_POST['requires_structure'] : 0;
    
    // Validar datos requeridos
    if (empty($version_code) || $sessions <= 0) {
        throw new Exception('El código de versión y sesiones son obligatorios');
    }

    if ($cat_category_course <= 0 || $cat_type_modality <= 0) {
        throw new Exception('Debe seleccionar categoría y modalidad');
    }

    // Manejar archivo PDF si se subió
    $brochure_url = null;
    if (isset($_FILES['brochure_pdf']) && $_FILES['brochure_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/brochures/';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['brochure_pdf']['name'], PATHINFO_EXTENSION));
        
        // Validar que sea PDF
        if ($file_extension !== 'pdf') {
            throw new Exception('Solo se permiten archivos PDF');
        }

        // Validar tamaño (10MB máximo)
        if ($_FILES['brochure_pdf']['size'] > 10 * 1024 * 1024) {
            throw new Exception('El archivo no debe superar 10MB');
        }

        // Generar nombre único
        $file_name = 'brochure_' . $program_id . '_' . time() . '.pdf';
        $file_path = $upload_dir . $file_name;

        // Mover archivo
        if (!move_uploaded_file($_FILES['brochure_pdf']['tmp_name'], $file_path)) {
            throw new Exception('Error al subir el archivo');
        }

        // Guardar URL relativa
        $brochure_url = 'uploads/brochures/' . $file_name;
    }
    
    $pdo->beginTransaction();
    
    // Insertar versión
    $sql = "INSERT INTO program_versions 
            (program_id, version_code, sessions, cat_category_course, cat_type_modality, brochure_url, active, registration_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $program_id,
        $version_code,
        $sessions,
        $cat_category_course,
        $cat_type_modality,
        $brochure_url,
        $active
    ]);
    
    $version_id = $pdo->lastInsertId();

    if (!$version_id) {
        throw new Exception('Error al obtener el ID de la versión creada');
    }
    
    // Si requiere estructura, insertar cursos
    if ($requires_structure == 1 && isset($_POST['courses']) && is_array($_POST['courses'])) {
        $courses = array_filter($_POST['courses'], function($val) {
            return !empty($val) && (int)$val > 0;
        });

        if (empty($courses)) {
            throw new Exception('Debe seleccionar al menos un curso para la estructura');
        }

        $sql = "INSERT INTO program_version_structure (parent_program_version_id, child_program_version_id) 
                VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($courses as $course_id) {
            $course_id = (int)$course_id;
            $stmt->execute([$version_id, $course_id]);
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'version_id' => $version_id,
        'message' => 'Versión creada exitosamente'
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log del error
    error_log("Error save_version.php: " . $e->getMessage());
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}