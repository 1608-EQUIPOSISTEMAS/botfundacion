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

// Obtener los datos de la tabla bot_history
try {
    $sql = "SELECT id, concat, invoke_text, registration_date, core_contact, client_contact, pay 
            FROM bot_history 
            WHERE flow_status='completed' 
            ORDER BY registration_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bot_history_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $bot_history_data = [];
    $error_message = "Error al obtener el historial del bot: " . $e->getMessage();
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
    <link rel="stylesheet" href="assets/css/defecto.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .main-panel {
            margin-left: 260px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            background: #fafafa;
        }

        .content-wrapper {
            padding: 40px 32px;
            max-width: none; /* Sin límite cuando está expandido */
            margin: 0 auto;
            width: 100%;
        }

        .sidebar-minimal.collapsed {
            transform: translateX(-260px);
        }

        .main-panel.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Smooth transitions */
        .sidebar-minimal {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }


        /* Reset de DataTables para diseño minimalista */
        .dataTables_wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        /* Header minimalista */
        .page-header-minimal {
            margin-bottom: 32px;
        }

        .page-title-minimal {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-family: 'Inter', sans-serif;
        }

        .page-subtitle-minimal {
            font-size: 14px;
            color: #737373;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
        }

        /* Card contenedor minimalista */
        .card-minimal {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: none;
        }

        .card-body-minimal {
            padding: 0;
        }

        /* Controles superiores DataTables */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            padding: 20px 24px;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 8px 16px 8px 36px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
            width: 280px;
            margin-left: 8px;
            font-family: 'Inter', sans-serif;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #1a1a1a;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 8px 32px 8px 12px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            margin: 0 8px;
            font-family: 'Inter', sans-serif;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-size: 14px;
            color: #737373;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
        }

        /* Tabla minimalista */
        .table-minimal {
            width: 100%;
            border-collapse: collapse;
            margin: 0 !important;
        }

        .table-minimal thead {
            background: #fafafa;
        }

        .table-minimal thead th {
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #f0f0f0;
            border-top: none;
            font-family: 'Inter', sans-serif;
        }

        .table-minimal tbody tr {
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.15s ease;
        }

        .table-minimal tbody tr:hover {
            background: #fafafa;
        }

        .table-minimal tbody tr:hover .row-actions-minimal {
            opacity: 1;
        }

        .table-minimal tbody td {
            padding: 16px 24px;
            font-size: 14px;
            color: #1a1a1a;
            vertical-align: middle;
            border-top: none;
            font-family: 'Inter', sans-serif;
        }

        /* Estilos de celdas específicas */
        .id-cell-minimal {
            font-weight: 500;
            color: #737373;
            font-size: 13px;
        }

        .phone-cell-minimal {
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            color: #1a1a1a;
        }

        .message-cell-minimal {
            max-width: 400px;
            line-height: 1.5;
            color: #404040;
            padding: 12px 24px;
        }

        .date-cell-minimal {
            color: #737373;
            font-size: 13px;
        }

        .contact-cell-minimal {
            font-size: 13px;
            color: #1a1a1a;
        }

        .contact-cell-minimal.empty {
            color: #a3a3a3;
            font-style: italic;
        }

        /* Status badges minimalistas */
        .status-badge-minimal {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }

        .status-badge-minimal.paid {
            background: #f0fdf4;
            color: #15803d;
        }

        .status-badge-minimal.unpaid {
            background: #fafafa;
            color: #737373;
        }

        .status-badge-minimal::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Acciones contextuales */
        .row-actions-minimal {
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .btn-action-minimal {
            padding: 6px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            background: white;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-action-minimal:hover:not(:disabled) {
            background: #1a1a1a;
            color: white;
            border-color: #1a1a1a;
        }

        .btn-action-minimal:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #fafafa;
            color: #a3a3a3;
        }

        .btn-action-minimal:disabled:hover {
            background: #fafafa;
            color: #a3a3a3;
            border-color: #e5e5e5;
        }

        /* Footer de tabla */
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 16px 24px;
            font-size: 13px;
            color: #737373;
            font-family: 'Inter', sans-serif;
        }

        /* Empty state */
        .empty-state-minimal {
            text-align: center;
            padding: 80px 24px;
            color: #a3a3a3;
        }

        .empty-state-icon-minimal {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        /* Separador limpio */
        .separator-line-minimal {
            border: none;
            height: 24px;
            background: transparent;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                float: none;
                text-align: left;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .table-minimal thead th,
            .table-minimal tbody td {
                padding: 12px 16px;
            }

            .row-actions-minimal {
                opacity: 1;
            }
        }

        /* Sobreescribir estilos antiguos */
        .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
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
                    <div class="page-header-minimal" style="margin-bottom: -32px;">
                        <h1 class="page-title-minimal">Historial del Bot</h1>
                        <p class="page-subtitle-minimal">Interacciones registradas en WhatsApp</p>
                    </div>

                    <hr class="separator-line-minimal">

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card card-minimal">
                                <div class="card-body card-body-minimal">
                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger" style="margin: 24px;">
                                            <i class="mdi mdi-alert-circle mr-2"></i>
                                            <?php echo htmlspecialchars($error_message); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table id="botHistoryTable" class="table table-minimal" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 140px;">ID</th>
                                                    <th>Mensaje</th>
                                                    <th style="width: 140px;">Fecha</th>
                                                    <th style="width: 120px;">Core</th>
                                                    <th style="width: 120px;">Cliente</th>
                                                    <th style="width: 120px;">Estado</th>
                                                 <th style="width: 120px;">Marcar Paga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($bot_history_data)): ?>
                                                    <?php foreach ($bot_history_data as $row): ?>
                                                        <tr data-id="<?php echo $row['id']; ?>" data-pay="<?php echo $row['pay']; ?>">
                                                            <td class="id-cell-minimal"><?php echo htmlspecialchars($row['id']); ?></td>
                                                            <td class="phone-cell-minimal"><?php echo htmlspecialchars($row['invoke_text']); ?></td>
                                                            <td class="date-cell-minimal">
                                                                <?php 
                                                                    $date = new DateTime($row['registration_date']);
                                                                    echo $date->format('d M Y'); 
                                                                ?>
                                                            </td>
                                                            <td class="contact-cell-minimal <?php echo empty($row['core_contact']) ? 'empty' : ''; ?>">
                                                                <?php echo !empty($row['core_contact']) ? htmlspecialchars($row['core_contact']) : 'Sin asignar'; ?>
                                                            </td>
                                                            <td class="contact-cell-minimal">
                                                                <?php echo htmlspecialchars($row['client_contact']); ?>
                                                            </td>
                                                            <td>
                                                                <span class="status-badge-minimal <?php echo $row['pay'] == 1 ? 'paid' : 'unpaid'; ?> pay-badge-<?php echo $row['id']; ?>">
                                                                    <?php echo $row['pay'] == 1 ? 'Pagado' : 'Pendiente'; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="row-actions-minimal">
                                                                    <?php if ($row['pay'] == 0): ?>
                                                                        <button type="button" 
                                                                                class="btn-action-minimal btn-marcar-pagado" 
                                                                                data-id="<?php echo $row['id']; ?>">
                                                                            Marcar pagado
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="button" 
                                                                                class="btn-action-minimal" 
                                                                                disabled>
                                                                            Completado
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="8">
                                                            <div class="empty-state-minimal">
                                                                <div class="empty-state-icon-minimal">📭</div>
                                                                <p>No hay registros de bot disponibles</p>
                                                            </div>
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

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable con diseño minimalista
            var table = $('#botHistoryTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                    "search": "Buscar:",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "→",
                        "previous": "←"
                    }
                },
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                "order": [[3, "desc"]],
                "columnDefs": [
                    {
                        "targets": 0,
                        "className": "id-cell-minimal"
                    },
                    {
                        "targets": 1,
                        "className": "phone-cell-minimal"
                    },
                    {
                        "targets": 2,
                        "className": "message-cell-minimal",
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "className": "date-cell-minimal",
                        "type": "date"
                    },
                    {
                        "targets": [4, 5],
                        "className": "contact-cell-minimal"
                    }
                ],
                "autoWidth": false
            });

            // Manejar clic en botón marcar pagado
            $('#botHistoryTable').on('click', '.btn-marcar-pagado', function() {
                const id = $(this).data('id');
                const $row = $(this).closest('tr');
                
                Swal.fire({
                    title: '¿Marcar como pagado?',
                    text: "Esta acción actualizará el estado de pago",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, marcar pagado',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#1a1a1a',
                    cancelButtonColor: '#737373'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'acciones/informacion/marcar_pagado.php',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Procesando...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: response.message,
                                        confirmButtonColor: '#1a1a1a'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#1a1a1a'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al procesar la solicitud: ' + error,
                                    confirmButtonColor: '#1a1a1a'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        // Ocultar sidebar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-minimal');
            const mainPanel = document.querySelector('.main-panel');
            
            if (sidebar && mainPanel) {
                sidebar.classList.add('collapsed');
                mainPanel.classList.add('expanded');
            }
        });
    </script>
</body>
</html>