<?php
session_start();
require_once 'conexion/conexion.php';

// DEBUG: Ver qué valor tiene realmente la sesión
error_log("DEBUG - ROL en sesión: " . print_r($_SESSION['rol_id'] ?? 'NO DEFINIDO', true));

// Obtener el rol del usuario y asegurar que sea integer
$user_role = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : null;
$user_permissions = [];
$role_name = 'Sin Rol';
$role_class = 'default';

// Mapear rol a permisos y nombres
switch($user_role) {
    case 1: // Administrador
        $user_permissions = ['all'];
        $role_name = 'Administrador';
        $role_class = 'admin';
        break;
    case 2: // Foundation
        $user_permissions = ['fundacion'];
        $role_name = 'Foundation';
        $role_class = 'foundation';
        break;
    case 3: // Comercial
        $user_permissions = ['members'];
        $role_name = 'Asesor Comercial';
        $role_class = 'comercial';
        break;
    case 4: // Members
        $user_permissions = ['members'];
        $role_name = 'Comercial';
        $role_class = 'comercial';
        break;
    default:
        $user_permissions = [];
        $role_name = 'Sin Permisos';
        $role_class = 'default';
}

// Log para debug
error_log("DEBUG - Rol procesado: {$user_role}, Nombre: {$role_name}, Permisos: " . json_encode($user_permissions));

// Obtener configuraciones
try {
    $sql = "SELECT id, mensaje_bienvenida, palabra_clave FROM configuraciones ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $configuraciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $configuraciones = [];
    $error_message = "Error al obtener configuraciones: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connect Plus</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/defecto.css">

    <style>
        /* Header moderno */
        .modern-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 0;
        }

        .modern-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid #06b6d4;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #06b6d4;
        }

        .role-badge-modern.admin {
            background: rgba(139, 92, 246, 0.1);
            border-color: #8b5cf6;
            color: #a78bfa;
        }

        .role-badge-modern.foundation {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #34d399;
        }

        .role-badge-modern.comercial {
            background: rgba(245, 158, 11, 0.1);
            border-color: #f59e0b;
            color: #fbbf24;
        }

        /* Botón WhatsApp moderno */
        .btn-whatsapp-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #25d366;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp-modern:hover {
            background: #22c55e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 211, 102, 0.4);
        }

        .btn-whatsapp-modern.loading {
            background: #f59e0b;
            pointer-events: none;
        }

        .btn-whatsapp-modern.connected {
            background: #10b981;
        }

        .btn-whatsapp-modern.error {
            background: #ef4444;
        }

        /* Línea separadora */
        .separator-line {
            height: 2px;
            background: linear-gradient(to right, transparent, #e0e0e0 20%, #e0e0e0 80%, transparent);
            margin: 1.5rem 0;
            border: none;
        }

        /* Estilos para DataTables */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 0.375rem 0.75rem;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 0.375rem 0.75rem;
            margin: 0 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #0056b3 !important;
            color: white !important;
            border-color: #0056b3 !important;
        }

        /* Animación spin */
        @keyframes mdi-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .mdi-spin {
            animation: mdi-spin 1s linear infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .modern-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php include 'includes/header.php'; ?>

        <div class="container-fluid page-body-wrapper">
            <?php include 'includes/sidebar.php'; ?>
            
            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- Header Unificado - Todo en uno -->
                    <div class="card" style="border-radius: 20px; box-shadow: 0 4px 16px rgba(6,182,212,0.08);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Título y Badge -->
                                <div class="d-flex align-items-center gap-3">
                                    <h1 class="mb-0" style="font-size: 1.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="mdi mdi-robot"></i>
                                        Bot de WhatsApp
                                    </h1>
                                    <?php if ($user_role): ?>
                                        <span style="margin-left: 8px;" class="role-badge-modern <?php echo htmlspecialchars($role_class); ?>">
                                            <i class="mdi mdi-shield-check"></i>
                                            <?php echo htmlspecialchars($role_name); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="margin-left: 8px;" class="role-badge-modern">
                                            <i class="mdi mdi-alert"></i>
                                            Sin Rol Asignado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Botón WhatsApp -->
                                <button id="startWhatsAppBtn" class="btn-whatsapp-modern">
                                    <i class="mdi mdi-whatsapp"></i>
                                    <span id="btnText">Iniciar Bot</span>
                                </button>
                            </div>

                            <?php if (!empty($user_permissions)): ?>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.875rem;">
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Línea Separadora -->
                    <hr class="separator-line">

                    <!-- Tabla -->
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 16px rgba(6,182,212,0.08);">
                                <div class="card-body">
                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger">
                                            <i class="mdi mdi-alert-circle mr-2"></i>
                                            <?php echo htmlspecialchars($error_message); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="table-responsive">
                                        <table id="configuracionesTable" class="table table-bordered table-hover" style="width:100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><i class="mdi mdi-pound mr-1"></i> ID</th>
                                                    <th><i class="mdi mdi-key mr-1"></i> Palabra Clave</th>
                                                    <th><i class="mdi mdi-message-text mr-1"></i> Mensaje de Bienvenida</th>
                                                    <th><i class="mdi mdi-cogs mr-1"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($configuraciones)): ?>
                                                    <?php foreach ($configuraciones as $config): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($config['id']); ?></td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    <?php echo htmlspecialchars($config['palabra_clave']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                                                     title="<?php echo htmlspecialchars($config['mensaje_bienvenida']); ?>">
                                                                    <?php echo htmlspecialchars($config['mensaje_bienvenida']); ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button 
                                                                    class="btn btn-outline-primary btn-sm btn-rounded"
                                                                    data-toggle="modal"
                                                                    data-target="#editarModal"
                                                                    data-id="<?php echo htmlspecialchars($config['id']); ?>"
                                                                    data-palabra="<?php echo htmlspecialchars($config['palabra_clave']); ?>"
                                                                    data-mensaje="<?php echo htmlspecialchars($config['mensaje_bienvenida']); ?>"
                                                                >
                                                                    <i class="mdi mdi-pencil-outline mr-2"></i>
                                                                    <span>Editar</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">
                                                            <i class="mdi mdi-database-remove" style="font-size: 2rem;"></i>
                                                            <p class="mt-2">No hay configuraciones disponibles</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <?php include 'modals/bot/whatsapp.php'; ?>
    <?php include 'modals/bot/editar.php'; ?>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    
    <script>
        // Configuración del rol del usuario desde PHP
        const USER_ROLE = <?php echo json_encode($user_role); ?>;
        const USER_PERMISSIONS = <?php echo json_encode($user_permissions); ?>;

        console.log('Usuario actual - Rol:', USER_ROLE, 'Permisos:', USER_PERMISSIONS);

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        let qrLibraryLoaded = false;
        let checkingQR = false;
        let whatsappConnected = false;

        // Inicializar DataTable
        $(document).ready(function() {
            $('#configuracionesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
                ]
            });
        });

        function loadQRLibrary() {
            return new Promise((resolve, reject) => {
                if (typeof QRCode !== 'undefined') {
                    qrLibraryLoaded = true;
                    resolve();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js';
                script.onload = () => {
                    window.createQRCode = function(text, container) {
                        try {
                            container.innerHTML = '';
                            const qr = qrcode(0, 'M');
                            qr.addData(text);
                            qr.make();
                            
                            const div = document.createElement('div');
                            div.innerHTML = qr.createImgTag(4, 8);
                            div.style.display = 'flex';
                            div.style.justifyContent = 'center';
                            div.style.alignItems = 'center';
                            container.appendChild(div);
                            
                            return true;
                        } catch (error) {
                            console.error('Error generando QR:', error);
                            return false;
                        }
                    };
                    
                    qrLibraryLoaded = true;
                    resolve();
                };
                script.onerror = () => reject(new Error('Failed to load QR library'));
                document.head.appendChild(script);
            });
        }

        function generateQRCode(container, qrData) {
            if (qrLibraryLoaded && window.createQRCode) {
                const success = window.createQRCode(qrData, container);
                if (!success) showTextQR(container, qrData);
            } else {
                showTextQR(container, qrData);
            }
        }

        function showTextQR(container, qrData) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="mdi mdi-qrcode-scan mb-3" style="font-size: 2rem;"></i>
                    <p><strong>Código QR:</strong></p>
                    <textarea class="form-control" rows="4" readonly style="font-size: 10px;">${qrData}</textarea>
                </div>`;
        }

        function updateButtonState(state, text) {
            const btn = document.getElementById('startWhatsAppBtn');
            const btnText = document.getElementById('btnText');
            const icon = btn.querySelector('i');
            
            btn.className = 'btn-whatsapp-modern';
            
            switch(state) {
                case 'loading':
                    btn.classList.add('loading');
                    btn.disabled = true;
                    btnText.textContent = text;
                    icon.className = 'mdi mdi-loading mdi-spin';
                    break;
                case 'connected':
                    btn.classList.add('connected');
                    btn.disabled = false;
                    btnText.textContent = text;
                    icon.className = 'mdi mdi-check-circle';
                    break;
                case 'error':
                    btn.classList.add('error');
                    btn.disabled = false;
                    btnText.textContent = text;
                    icon.className = 'mdi mdi-alert-circle';
                    break;
                default:
                    btn.disabled = false;
                    btnText.textContent = text;
                    icon.className = 'mdi mdi-whatsapp';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadQRLibrary().catch(error => {
                console.warn('QR library failed to load:', error);
            });

            checkWhatsAppStatus();
        });

        async function checkWhatsAppStatus() {
            try {
                const response = await fetch('conexion/whatsapp_proxy_fundacion2.php?action=status');
                const data = await response.json();
                
                if (data.status === 'connected') {
                    whatsappConnected = true;
                    updateButtonState('connected', 'Conectado');
                }
            } catch (error) {
                console.log('WhatsApp no está conectado inicialmente');
            }
        }

        document.getElementById('startWhatsAppBtn').addEventListener('click', async function() {
            if (!USER_PERMISSIONS || USER_PERMISSIONS.length === 0) {
                Swal.fire({
                    title: 'Sin Permisos',
                    text: 'No tienes permisos asignados para usar el bot de WhatsApp',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            if (whatsappConnected) {
                const result = await Swal.fire({
                    title: '¿Desconectar WhatsApp?',
                    text: 'El bot de WhatsApp está actualmente conectado.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, desconectar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    await stopWhatsApp();
                }
                return;
            }

            Swal.fire({
                title: 'Iniciando Bot',
                html: '<p>Preparando la conexión...</p>',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            updateButtonState('loading', 'Iniciando...');

            try {
                const response = await fetch('conexion/whatsapp_proxy_fundacion2.php?action=start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        role: USER_ROLE,
                        permissions: USER_PERMISSIONS
                    })
                });

                const result = await response.json();
                console.log('Respuesta del servidor:', result);
                
                if (result.success) {
                    Swal.close();
                    $('#whatsappModal').modal('show');
                    
                    document.getElementById('loadingQR').style.display = 'block';
                    document.getElementById('qrCode').style.display = 'none';
                    document.getElementById('whatsappReady').style.display = 'none';
                    document.getElementById('qrCode').innerHTML = '';
                    
                    if (!checkingQR) {
                        checkingQR = true;
                        checkForQR();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error',
                        confirmButtonColor: '#007bff'
                    });
                    
                    updateButtonState('error', 'Error');
                    setTimeout(() => {
                        updateButtonState('default', 'Iniciar Bot');
                    }, 3000);
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error del Servidor',
                    text: 'No se pudo conectar con el servidor Node.js',
                    icon: 'error',
                    footer: '<small>' + error.message + '</small>'
                });
                
                console.error('Error de conexión:', error);
                updateButtonState('error', 'Error');
                
                setTimeout(() => {
                    updateButtonState('default', 'Iniciar Bot');
                }, 5000);
            }
        });

        async function stopWhatsApp() {
            try {
                updateButtonState('loading', 'Desconectando...');
                
                const response = await fetch('conexion/whatsapp_proxy_fundacion2.php?action=stop', {
                    method: 'POST'
                });
                
                checkingQR = false;
                whatsappConnected = false;
                
                Toast.fire({
                    icon: 'success',
                    title: 'WhatsApp Desconectado'
                });
                
                updateButtonState('default', 'Iniciar Bot');
                $('#whatsappModal').modal('hide');
                
            } catch (error) {
                console.error('Error stopping bot:', error);
                Toast.fire({
                    icon: 'error',
                    title: 'Error al desconectar'
                });
            }
        }

        async function checkForQR() {
            if (!checkingQR) return;
            
            try {
                const response = await fetch('conexion/whatsapp_proxy_fundacion2.php?action=status');
                const data = await response.json();
                
                switch(data.status) {
                    case 'generating_qr':
                        document.getElementById('loadingQR').style.display = 'block';
                        document.getElementById('qrCode').style.display = 'none';
                        document.getElementById('whatsappReady').style.display = 'none';
                        updateButtonState('loading', 'Generando QR...');
                        break;
                        
                    case 'waiting_scan':
                        if (data.qr) {
                            document.getElementById('loadingQR').style.display = 'none';
                            document.getElementById('qrCode').style.display = 'block';
                            document.getElementById('whatsappReady').style.display = 'none';
                            
                            const qrContainer = document.getElementById('qrCode');
                            generateQRCode(qrContainer, data.qr);
                            updateButtonState('loading', 'Esperando...');
                        }
                        break;
                        
                    case 'connected':
                        document.getElementById('loadingQR').style.display = 'none';
                        document.getElementById('qrCode').style.display = 'none';
                        document.getElementById('whatsappReady').style.display = 'block';
                        
                        whatsappConnected = true;
                        updateButtonState('connected', 'Conectado');
                        checkingQR = false;
                        
                        Swal.fire({
                            title: '¡Conectado!',
                            text: 'WhatsApp Bot funcionando correctamente',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#whatsappModal').modal('hide');
                        });
                        
                        return;
                }
                
                setTimeout(checkForQR, 2000);
                
            } catch (error) {
                console.error('Error checking QR:', error);
                setTimeout(checkForQR, 3000);
            }
        }

        document.getElementById('stopWhatsAppBtn').addEventListener('click', async function() {
            const result = await Swal.fire({
                title: '¿Detener Bot?',
                text: 'Se cerrará la conexión actual del bot',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, detener',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                await stopWhatsApp();
            }
        });

        $('#whatsappModal').on('hidden.bs.modal', function () {
            if (!whatsappConnected) {
                checkingQR = false;
                updateButtonState('default', 'Iniciar Bot');
            }
        });
    </script>
</body>
</html>