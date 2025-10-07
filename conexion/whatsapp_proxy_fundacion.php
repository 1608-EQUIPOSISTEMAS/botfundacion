<?php
header('Content-Type: application/json');

// URL del servidor Node.js
$NODE_SERVER = 'http://34.31.232.104:3002';

// Obtener la acción solicitada
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'start':
            // Leer el body del request (rol y permisos)
            $input = json_decode(file_get_contents('php://input'), true);
            
            $role = $input['role'] ?? null;
            $permissions = $input['permissions'] ?? [];
            
            if (!$role) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se proporcionó el rol del usuario'
                ]);
                exit;
            }
            
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
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al comunicarse con el servidor Node.js'
                ]);
            }
            break;
            
        case 'status':
            // Obtener estado del QR
            $ch = curl_init($NODE_SERVER . '/get-qr');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                echo $response;
            } else {
                echo json_encode([
                    'status' => 'disconnected',
                    'qr' => null
                ]);
            }
            break;
            
        case 'stop':
            // Detener WhatsApp
            $ch = curl_init($NODE_SERVER . '/stop-whatsapp');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            echo $response ?: json_encode(['success' => true]);
            break;
            
        case 'cleanup':
            // Limpiar sesión
            $ch = curl_init($NODE_SERVER . '/cleanup-session');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            echo $response ?: json_encode(['success' => false, 'message' => 'Sin respuesta del servidor']);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en el proxy: ' . $e->getMessage()
    ]);
}
?>