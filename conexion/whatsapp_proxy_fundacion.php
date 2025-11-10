<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// URL del servidor Node.js
$NODE_SERVER = 'http://34.42.193.17:3009';

// Obtener la acción solicitada
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Log de la acción
error_log("[PROXY] Acción solicitada: {$action}");

try {
    switch ($action) {
        case 'start':
            // Leer el body del request (rol y permisos)
            $input = json_decode(file_get_contents('php://input'), true);
            
            $role = $input['role'] ?? 'user';
            $permissions = $input['permissions'] ?? [];
            
            error_log("[PROXY] Iniciando WhatsApp con rol: {$role}");
            
            // Enviar al servidor Node.js con rol
            $ch = curl_init($NODE_SERVER . '/start-whatsapp');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'role' => $role,
                'permissions' => $permissions
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                error_log("[PROXY] Error CURL: {$curlError}");
                echo json_encode([
                    'success' => false,
                    'message' => 'Error de conexión con el servidor Node.js: ' . $curlError
                ]);
                exit;
            }
            
            if ($httpCode === 200 && $response) {
                error_log("[PROXY] WhatsApp iniciado correctamente");
                echo $response;
            } else {
                error_log("[PROXY] Error HTTP: {$httpCode}");
                echo json_encode([
                    'success' => false,
                    'message' => "Error al comunicarse con el servidor Node.js (HTTP {$httpCode})"
                ]);
            }
            break;
            
        case 'status':
        case 'qr':
            // Obtener estado del QR y conexión
            error_log("[PROXY] Consultando estado/QR");
            
            $ch = curl_init($NODE_SERVER . '/get-qr');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                error_log("[PROXY] Error CURL en status: {$curlError}");
                echo json_encode([
                    'status' => 'error',
                    'qr' => null,
                    'message' => 'Error de conexión: ' . $curlError
                ]);
                exit;
            }
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'status' => 'disconnected',
                    'qr' => null,
                    'message' => 'Servidor no disponible'
                ]);
            }
            break;
            
        case 'stop':
            // Detener WhatsApp
            error_log("[PROXY] Deteniendo WhatsApp");
            
            $ch = curl_init($NODE_SERVER . '/stop-whatsapp');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al detener WhatsApp'
                ]);
            }
            break;
            
        case 'cleanup':
            // Limpiar sesión
            error_log("[PROXY] Limpiando sesión");
            
            $ch = curl_init($NODE_SERVER . '/cleanup-session');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al limpiar sesión'
                ]);
            }
            break;
            
        case 'health':
            // Health check del servidor
            error_log("[PROXY] Health check");
            
            $ch = curl_init($NODE_SERVER . '/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'success' => false,
                    'status' => 'unhealthy',
                    'message' => 'Servidor Node.js no responde'
                ]);
            }
            break;
            
        default:
            error_log("[PROXY] Acción inválida: {$action}");
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida. Acciones disponibles: start, status, qr, stop, cleanup, health'
            ]);
    }
    
} catch (Exception $e) {
    error_log("[PROXY] Excepción: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el proxy: ' . $e->getMessage()
    ]);
}
?>