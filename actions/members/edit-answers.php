<?php
    header('Content-Type: application/json');
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // No mostrar errores en output

    try {
        require_once '../../conexion/conexion.php';

        // Validar que PDO existe
        if (!isset($pdo)) {
            throw new Exception('Error de conexión: PDO no disponible');
        }

        // Validar datos POST
        if (!isset($_POST['member_id'])) {
            throw new Exception('Falta el ID del plan (member_id)');
        }

        if (!isset($_POST['respuestas'])) {
            throw new Exception('Falta el array de respuestas');
        }

        $member_id = (int)$_POST['member_id'];
        
        if ($member_id <= 0) {
            throw new Exception('ID de plan inválido: ' . $member_id);
        }

        // Verificar que el member existe
        $checkSql = "SELECT id FROM members WHERE id = :member_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':member_id' => $member_id]);
        
        if (!$checkStmt->fetch()) {
            throw new Exception('El plan con ID ' . $member_id . ' no existe');
        }

        $respuestas = $_POST['respuestas'];
        
        if (!is_array($respuestas)) {
            throw new Exception('El formato de respuestas es inválido (debe ser array)');
        }

        if (count($respuestas) !== 4) {
            throw new Exception('Se esperan 4 respuestas, se recibieron ' . count($respuestas));
        }

        // Iniciar transacción
        $pdo->beginTransaction();
        
        $actualizadas = 0;
        $insertadas = 0;

        foreach ($respuestas as $index => $respuesta) {
            // Validar estructura
            if (!isset($respuesta['opcion_numero'])) {
                throw new Exception("Respuesta #{$index}: falta opcion_numero");
            }

            if (!isset($respuesta['tipo_respuesta'])) {
                throw new Exception("Respuesta #{$index}: falta tipo_respuesta");
            }

            if (!isset($respuesta['mensaje'])) {
                throw new Exception("Respuesta #{$index}: falta mensaje");
            }

            $id = !empty($respuesta['id']) ? (int)$respuesta['id'] : null;
            $opcion_numero = (int)$respuesta['opcion_numero'];
            $tipo_respuesta = trim($respuesta['tipo_respuesta']);
            $mensaje = trim($respuesta['mensaje']);

            // Validaciones
            if ($opcion_numero < 1 || $opcion_numero > 4) {
                throw new Exception("Opción {$opcion_numero} fuera de rango (debe ser 1-4)");
            }

            if (empty($tipo_respuesta)) {
                throw new Exception("La respuesta {$opcion_numero} no tiene tipo seleccionado");
            }

            if (empty($mensaje)) {
                throw new Exception("La respuesta {$opcion_numero} no tiene mensaje");
            }

            if (!in_array($tipo_respuesta, ['texto', 'horario', 'submenu'])) {
                throw new Exception("Tipo de respuesta inválido en opción {$opcion_numero}: '{$tipo_respuesta}'");
            }

            try {
                if ($id && $id > 0) {
                    // UPDATE si ya existe
                    $sql = "UPDATE option_responses 
                            SET tipo_respuesta = :tipo_respuesta, 
                                mensaje = :mensaje 
                            WHERE id = :id 
                            AND member_id = :member_id";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':tipo_respuesta' => $tipo_respuesta,
                        ':mensaje' => $mensaje,
                        ':id' => $id,
                        ':member_id' => $member_id
                    ]);
                    
                    if ($stmt->rowCount() > 0) {
                        $actualizadas++;
                    }
                    
                } else {
                    // INSERT si no existe
                    $sql = "INSERT INTO option_responses (member_id, opcion_numero, tipo_respuesta, mensaje) 
                            VALUES (:member_id, :opcion_numero, :tipo_respuesta, :mensaje)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':member_id' => $member_id,
                        ':opcion_numero' => $opcion_numero,
                        ':tipo_respuesta' => $tipo_respuesta,
                        ':mensaje' => $mensaje
                    ]);
                    
                    $insertadas++;
                }
                
            } catch (PDOException $e) {
                throw new Exception("Error SQL en respuesta {$opcion_numero}: " . $e->getMessage());
            }
        }

        // Confirmar transacción
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => "Respuestas guardadas correctamente ({$actualizadas} actualizadas, {$insertadas} nuevas)",
            'stats' => [
                'actualizadas' => $actualizadas,
                'insertadas' => $insertadas
            ]
        ]);

    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error PDO en edit-answers.php: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Error de base de datos',
            'error_type' => 'database',
            'error_details' => $e->getMessage(),
            'error_code' => $e->getCode()
        ]);
        
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error en edit-answers.php: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_type' => 'validation',
            'error_details' => $e->getMessage()
        ]);
    }
?>