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
    $sql = "SELECT id, concat, invoke_text, registration_date, core_contact, client_contact FROM bot_history ORDER BY registration_date DESC";
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

    <style>
       /* Estilos para celdas tipo textarea */
.cell-textarea {
    display: block;
    width: 100%;
    min-height: 60px;
    max-height: 150px;
    padding: 8px 12px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
    overflow-y: auto;
    word-wrap: break-word;
    white-space: pre-wrap;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2d3748;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}

.cell-textarea:hover {
    background: #ffffff;
    border-color: #06b6d4;
    transition: all 0.2s ease;
}

/* Estilos para valores NULL */
.null-value {
    color: #9ca3af;
    font-style: italic;
    text-align: center;
    padding: 8px;
}

/* Ajustes responsivos DataTable */
#botHistoryTable td {
    vertical-align: middle;
}

#botHistoryTable td.mensaje-col {
    max-width: 350px;
    padding: 12px;
}

#botHistoryTable td.phone-col {
    min-width: 120px;
}

/* Scrollbar personalizado */
.cell-textarea::-webkit-scrollbar {
    width: 6px;
}

.cell-textarea::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.cell-textarea::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.cell-textarea::-webkit-scrollbar-thumb:hover {
    background: #06b6d4;
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
                    <div class="card" style="border-radius: 20px; box-shadow: 0 4px 16px rgba(6,182,212,0.08);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <h1 class="mb-0" style="font-size: 1.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="mdi mdi-history"></i>
                                        Historial del Bot
                                    </h1>
                                    <!-- <?php if ($user_role): ?>
                                        <span style="margin-left: 8px;" class="role-badge-modern <?php echo htmlspecialchars($role_class); ?>">
                                            <i class="mdi mdi-shield-check"></i>
                                            <?php echo htmlspecialchars($role_name); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="margin-left: 8px;" class="role-badge-modern">
                                            <i class="mdi mdi-alert"></i>
                                            Sin Rol Asignado
                                        </span>
                                    <?php endif; ?> -->
                                </div>

                            </div>

                            <?php if (!empty($user_permissions)): ?>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.875rem;">
                                    Aquí puedes ver el historial de interacciones del bot de WhatsApp.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="separator-line">

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
                                        <table id="botHistoryTable" class="table table-bordered table-hover" style="width:100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><i class="mdi mdi-pound mr-1"></i> ID</th>
                                                    <th><i class="mdi mdi-phone-outgoing mr-1"></i> Teléfono</th>
                                                    <th><i class="mdi mdi-message-text mr-1"></i> Mensaje Recibido</th>
                                                    <th><i class="mdi mdi-calendar-clock mr-1"></i> Fecha</th>
                                                    <th><i class="mdi mdi-account-circle mr-1"></i> Core</th>
                                                    <th><i class="mdi mdi-account-circle-outline mr-1"></i> Cliente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($bot_history_data)): ?>
                                                    <?php foreach ($bot_history_data as $row): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                            <td class="phone-col"><?php echo htmlspecialchars($row['concat']); ?></td>
                                                            <td class="mensaje-col">
                                                                <div class="cell-textarea">
                                                                    <?php echo htmlspecialchars($row['invoke_text']); ?>
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                                                            <td>
                                                                <?php
                                                                    if (!empty($row['core_contact'])) {
                                                                        echo htmlspecialchars($row['core_contact']);
                                                                    } else {
                                                                        echo '<span class="null-value">Sin asignar</span>';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['client_contact']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            <i class="mdi mdi-database-remove" style="font-size: 2rem;"></i>
                                                            <p class="mt-2">No hay registros de bot disponibles</p>
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
            $('#botHistoryTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                "order": [[3, "desc"]],
                "columnDefs": [
                    {
                        "targets": 0,
                        "width": "50px",
                        "className": "text-center"
                    },
                    {
                        "targets": 1,
                        "width": "120px",
                        "className": "phone-col"
                    },
                    {
                        "targets": 2,
                        "width": "35%",
                        "className": "mensaje-col",
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "width": "140px",
                        "type": "date"
                    },
                    {
                        "targets": 4,
                        "width": "120px",
                        "className": "text-center"
                    },
                    {
                        "targets": 5,
                        "width": "120px",
                        "className": "text-center"
                    }
                ],
                "autoWidth": false,
                "scrollX": false,
                "drawCallback": function() {
                    // Ajustar altura de las celdas después de dibujar
                    $('.cell-textarea').each(function() {
                        const content = $(this).text().trim();
                        if (content.length > 100) {
                            $(this).css('min-height', '80px');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>