<?php
session_start();
require_once 'conexion/conexion.php';
require_once 'conexion/conexionfundation.php';

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
        $user_permissions = ['fundacion', 'inmobiliaria'];
        $role_name = 'Foundation';
        $role_class = 'foundation';
        break;
    case 3: // Comercial
        $user_permissions = ['members'];
        $role_name = 'Asesor Comercial';
        $role_class = 'comercial';
        break;
    case 4: // Members
        $user_permissions = ['members', 'fundacion'];
        $role_name = 'Comercial';
        $role_class = 'comercial';
        break;
    case 5: // inmobiliaria
        $user_permissions = ['inmobiliaria'];
        $role_name = 'Inmobiliaria';
        $role_class = 'inmobiliaria';
        break;
    default:
        $user_permissions = [];
        $role_name = 'Sin Permisos';
        $role_class = 'default';
}

// Log para debug
error_log("DEBUG - Rol procesado: {$user_role}, Nombre: {$role_name}, Permisos: " . json_encode($user_permissions));

// Obtener campaigns desde la base de datos inmobiliaria
try {
    $sql = "SELECT 
                id, 
                name, 
                description, 
                trigger_keywords, 
                is_active, 
                priority, 
                created_at, 
                updated_at,
                created_by
            FROM campaigns 
            WHERE deleted_at IS NULL
            ORDER BY priority DESC, created_at DESC";
    
    $stmt = $pdoInmobiliaria->prepare($sql);
    $stmt->execute();
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $campaigns = [];
    $error_message = "Error al obtener campañas: " . $e->getMessage();
    error_log("Error campaigns: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connect Plus - Bot WhatsApp</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/defecto.css">

    <style>
        /* ========================================
           RESET Y BASE MINIMALISTA
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0F172A;
            --secondary: #64748B;
            --accent: #06B6D4;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --bg-primary: #FFFFFF;
            --bg-secondary: #F8FAFC;
            --border: #E2E8F0;
            --text-primary: #0F172A;
            --text-secondary: #64748B;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
        }

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

        .sidebar-minimal {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========================================
           HEADER MINIMALISTA
        ======================================== */
        .modern-header {
            background: var(--bg-primary);
            border-radius: var(--radius);
            padding: 24px 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .btn-add-campaign {
            padding: 7px 20px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add-campaign:hover {
            background: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-add-campaign i {
            font-size: 18px;
        }

        .role-badge {
            padding: 6px 14px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .role-badge.admin {
            background: rgba(139, 92, 246, 0.08);
            border-color: rgba(139, 92, 246, 0.2);
            color: #8B5CF6;
        }

        .role-badge.foundation {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.2);
            color: #10B981;
        }

        .role-badge.comercial {
            background: rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.2);
            color: #F59E0B;
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #25D366;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .btn-whatsapp:hover {
            background: #22C55E;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        .btn-whatsapp.loading {
            background: var(--warning);
            pointer-events: none;
        }

        .btn-whatsapp.connected {
            background: var(--success);
        }

        .btn-whatsapp.error {
            background: var(--danger);
        }

        /* ========================================
           TARJETAS DE MÉTRICAS
        ======================================== */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s;
        }

        .metric-card:hover {
            border-color: #d4d4d4;
            transform: translateY(-2px);
        }

        .metric-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .metric-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f5f5f5;
        }

        .metric-icon i {
            font-size: 20px;
            color: #737373;
        }

        .metric-content {
            flex: 1;
        }

        .metric-value {
            font-size: 36px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1;
            margin-bottom: 4px;
        }

        .metric-label {
            font-size: 13px;
            color: #737373;
            font-weight: 500;
        }

        /* ========================================
           TABLA MINIMALISTA - REDISEÑO COMPLETO
        ======================================== */
        .campaigns-container {
            background: var(--bg-primary);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .campaigns-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--border);
        }

        .campaigns-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        /* OCULTAR CONTROLES NATIVOS DE DATATABLES */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        /* TABLA REDISEÑADA */
        .table-minimal {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-minimal thead {
            background: var(--bg-secondary);
        }

        .table-minimal thead th {
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            border: none;
            text-align: left;
        }

        .table-minimal thead th:last-child {
            text-align: right;
        }

        .table-minimal tbody tr {
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .table-minimal tbody tr:hover {
            background: var(--bg-secondary);
        }

        .table-minimal tbody tr:last-child {
            border-bottom: none;
        }

        .table-minimal tbody td {
            padding: 20px 24px;
            font-size: 14px;
            color: var(--text-primary);
            border: none;
            vertical-align: middle;
        }

        .table-minimal tbody td:last-child {
            text-align: right;
        }

        /* COLUMNA NOMBRE */
        .campaign-name {
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .campaign-icon {
            width: 40px;
            height: 40px;
            background: var(--bg-secondary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--accent);
            flex-shrink: 0;
        }

        /* STATUS MINIMALISTA */
        .status-dot {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-dot::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-dot.active {
            color: var(--success);
        }

        .status-dot.inactive {
            color: var(--secondary);
        }

        /* PRIORIDAD MINIMALISTA */
        .priority-value {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--bg-secondary);
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            color: var(--accent);
        }

        /* ACCIONES MINIMALISTAS */
        .actions-group {
            display: inline-flex;
            gap: 8px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            background: var(--bg-primary);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-secondary);
            font-size: 16px;
        }

        .action-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(6, 182, 212, 0.05);
            transform: translateY(-1px);
        }

        .action-btn.edit:hover {
            border-color: var(--warning);
            color: var(--warning);
            background: rgba(245, 158, 11, 0.05);
        }

        .action-btn.delete:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: rgba(239, 68, 68, 0.05);
        }

        /* ESTADO VACÍO */
        .empty-state {
            padding: 80px 32px;
            text-align: center;
        }

        .empty-state i {
            font-size: 64px;
            color: var(--border);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* ========================================
           RESPONSIVE
        ======================================== */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px 16px;
            }

            .modern-header {
                padding: 20px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-add-campaign,
            .btn-whatsapp {
                width: 100%;
                justify-content: center;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .metric-card {
                padding: 16px;
            }

            .metric-icon {
                width: 48px;
                height: 48px;
                font-size: 24px;
            }

            .metric-value {
                font-size: 24px;
            }

            .metric-label {
                font-size: 11px;
            }

            .table-minimal thead th,
            .table-minimal tbody td {
                padding: 12px 16px;
            }

            .campaign-name {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions-group {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ========================================
           ANIMACIONES
        ======================================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-minimal tbody tr {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .mdi-spin {
            animation: spin 1s linear infinite;
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
                    <!-- HEADER MINIMALISTA -->
                    <div class="modern-header">
                        <div class="header-content">
                            <div class="header-left">
                                <h1 class="header-title">Campañas de Bot</h1>
                            </div>
                            
                            <div class="header-actions">
                                <button id="startWhatsAppBtn" class="btn-whatsapp">
                                    <i class="mdi mdi-whatsapp"></i>
                                    <span id="btnText">Iniciar Bot</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TARJETAS DE MÉTRICAS -->
                    <?php
                    // Calcular métricas
                    $total_campaigns = count($campaigns);
                    $active_campaigns = count(array_filter($campaigns, fn($c) => $c['is_active']));
                    $inactive_campaigns = $total_campaigns - $active_campaigns;
                    $avg_priority = $total_campaigns > 0 ? round(array_sum(array_column($campaigns, 'priority')) / $total_campaigns, 1) : 0;
                    ?>
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(6, 182, 212, 0.1); color: #06B6D4;">
                                <i class="mdi mdi-robot"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $total_campaigns; ?></div>
                                <div class="metric-label">Total Campañas</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="mdi mdi-check-circle-outline"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $active_campaigns; ?></div>
                                <div class="metric-label">Activas</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(100, 116, 139, 0.1); color: #64748B;">
                                <i class="mdi mdi-pause-circle-outline"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $inactive_campaigns; ?></div>
                                <div class="metric-label">Inactivas</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                <i class="mdi mdi-chart-line"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $avg_priority; ?></div>
                                <div class="metric-label">Prioridad Promedio</div>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA MINIMALISTA -->
                    <div class="campaigns-container">
                        <div class="campaigns-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="margin: 0;">Todas las campañas</h2>
                            <button onclick="openAddCampaignModal()" class="btn-add-campaign">
                                <i class="mdi mdi-plus"></i>
                                <span>Nueva Campaña</span>
                            </button>
                        </div>

                        <?php if (isset($error_message)): ?>
                            <div style="padding: 20px; background: rgba(239, 68, 68, 0.1); color: #EF4444; border-bottom: 1px solid var(--border);">
                                <i class="mdi mdi-alert-circle"></i>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($campaigns)): ?>
                            <table id="campaignsTable" class="table-minimal">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Campaña</th>
                                        <th>Estado</th>
                                        <th>Prioridad</th>
                                        <th style="text-align: center;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <tr>
                                            <!-- NOMBRE CON ICONO -->
                                            <td>
                                                <div class="campaign-name">
                                                    <div class="campaign-icon">
                                                        <i class="mdi mdi-robot"></i>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; color: var(--text-primary);">
                                                            <?php echo htmlspecialchars($campaign['name']); ?>
                                                        </div>
                                                        <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">
                                                            <?php echo htmlspecialchars(substr($campaign['description'] ?? 'Sin descripción', 0, 60)); ?>...
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- ESTADO -->
                                            <td>
                                                <?php if ($campaign['is_active']): ?>
                                                    <span class="status-dot active">Activo</span>
                                                <?php else: ?>
                                                    <span class="status-dot inactive">Inactivo</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- PRIORIDAD -->
                                            <td>
                                                <span class="priority-value">
                                                    <?php echo htmlspecialchars($campaign['priority']); ?>
                                                </span>
                                            </td>

                                            <!-- ACCIONES -->
                                            <td>
                                                <div style="text-align: center;">
                                                    <!-- VER (va a campaigns_fundation.php?id=X) -->
                                                    <a href="campaigns_fundation.php?id=<?php echo $campaign['id']; ?>" 
                                                       class="action-btn" 
                                                       title="Ver detalles">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </a>

                                                    <!-- EDITAR (abre modal futuro) -->
                                                    <button 
                                                        class="action-btn edit" 
                                                        onclick="openEditModal(<?php echo $campaign['id']; ?>)"
                                                        title="Editar">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                    </button>

                                                    <!-- ELIMINAR (SweetAlert) -->
                                                    <button 
                                                        class="action-btn delete" 
                                                        onclick="deleteCampaign(<?php echo $campaign['id']; ?>, '<?php echo htmlspecialchars(addslashes($campaign['name'])); ?>')"
                                                        title="Eliminar">
                                                    <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="mdi mdi-robot-off-outline"></i>
                                <h3>No hay campañas</h3>
                                <p>Comienza creando tu primera campaña de bot</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <?php include 'modals/botinmobiliaria/whatsapp.php'; ?>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        // ========================================
        // CONFIGURACIÓN
        // ========================================
        const USER_ROLE = <?php echo json_encode($user_role); ?>;
        const USER_PERMISSIONS = <?php echo json_encode($user_permissions); ?>;

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

        // ========================================
        // DATATABLE MINIMALISTA (sin controles UI)
        // ========================================
        $(document).ready(function() {
            $('#campaignsTable').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "ordering": true,
                "order": [[2, "desc"]], // Ordenar por prioridad
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });

            // Verificar estado de WhatsApp al cargar
            checkWhatsAppStatus();

            // Cargar librería QR
            loadQRLibrary().catch(error => {
                console.warn('QR library failed to load:', error);
            });

            // Sidebar
            const sidebar = document.querySelector('.sidebar-minimal');
            const mainPanel = document.querySelector('.main-panel');
            
            if (sidebar && mainPanel) {
                sidebar.classList.add('collapsed');
                mainPanel.classList.add('expanded');
            }
        });

        // ========================================
        // FUNCIONES DE ACCIONES
        // ========================================

        // AGREGAR NUEVA CAMPAÑA
        function openAddCampaignModal() {
            // Aquí irá la lógica para abrir el modal de agregar campaña
            Swal.fire({
                title: 'Nueva Campaña',
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <p style="color: #64748B; margin-bottom: 16px;">
                            Esta función abrirá un modal para crear una nueva campaña
                        </p>
                        <div style="background: #F8FAFC; padding: 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
                            <code style="color: #0F172A; font-size: 13px;">
                                // Conecta aquí tu modal de creación<br>
                                $('#addCampaignModal').modal('show');
                            </code>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#06B6D4',
                width: 600
            });
        }

        // EDITAR - Prepara el botón para abrir un modal futuro
        function openEditModal(campaignId) {
            // Aquí irá la lógica para abrir el modal de edición
            Swal.fire({
                title: 'Editar Campaña',
                text: `ID de campaña: ${campaignId}`,
                icon: 'info',
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <p style="color: #64748B; margin-bottom: 16px;">
                            Esta función abrirá un modal para editar la campaña #${campaignId}
                        </p>
                        <div style="background: #F8FAFC; padding: 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
                            <code style="color: #0F172A; font-size: 13px;">
                                // Aquí conectarás tu modal de edición<br>
                                $('#editCampaignModal').modal('show');
                            </code>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#06B6D4'
            });
        }

        // ELIMINAR - SweetAlert de confirmación
        function deleteCampaign(campaignId, campaignName) {
            Swal.fire({
                title: '¿Eliminar campaña?',
                html: `
                    <div style="text-align: center; padding: 20px 0;">
                        <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="mdi mdi-trash-can-outline" style="font-size: 32px; color: #EF4444;"></i>
                        </div>
                        <p style="color: #64748B; margin-bottom: 8px;">
                            Vas a eliminar la campaña:
                        </p>
                        <p style="font-weight: 600; color: #0F172A; font-size: 16px;">
                            ${campaignName}
                        </p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // SIMULACIÓN - Eliminar esta parte en producción
                    Swal.fire({
                        title: '¡Eliminada!',
                        text: 'La campaña ha sido eliminada correctamente',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        timer: 2000
                    }).then(() => {
                        // location.reload();
                    });

                    /* CÓDIGO REAL PARA ELIMINAR - Descomenta esto:
                    
                    fetch('conexion/delete_campaign.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: campaignId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Eliminada!',
                                text: 'La campaña ha sido eliminada correctamente',
                                icon: 'success',
                                confirmButtonColor: '#10B981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo eliminar la campaña', 'error');
                    });
                    
                    */
                }
            });
        }

        // ========================================
        // WHATSAPP BOT - QR LIBRARY
        // ========================================
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
                            const qr = qrcode(40, 'L');
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
            // Verificar si lo que llega ya es una imagen Base64
            if (qrData && qrData.startsWith('data:image')) {
                container.innerHTML = '';

                const img = document.createElement('img');
                img.src = qrData;
                img.alt = 'Código QR';
                img.style.maxWidth = '250px';
                img.style.margin = 'auto';
                img.style.display = 'block';

                container.appendChild(img);
                return;
            }

            // Si no es una imagen, generar el QR con la librería
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

        // ========================================
        // WHATSAPP BOT - ESTADOS
        // ========================================
        function updateButtonState(state, text) {
            const btn = document.getElementById('startWhatsAppBtn');
            const btnText = document.getElementById('btnText');
            const icon = btn.querySelector('i');
            
            btn.className = 'btn-whatsapp';
            
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

        async function checkWhatsAppStatus() {
            try {
                const response = await fetch('conexion/whatsapp_proxy_fundacion.php?action=status');
                const data = await response.json();
                
                if (data.status === 'connected') {
                    whatsappConnected = true;
                    updateButtonState('connected', 'Conectado');
                }
            } catch (error) {
                console.log('WhatsApp no está conectado inicialmente');
            }
        }

        // ========================================
        // WHATSAPP BOT - INICIAR/DETENER
        // ========================================
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
                const response = await fetch('conexion/whatsapp_proxy_fundacion.php?action=start', {
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
                
                const response = await fetch('conexion/whatsapp_proxy_fundacion.php?action=stop', {
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
                const response = await fetch('conexion/whatsapp_proxy_fundacion.php?action=status');
                const data = await response.json();
                
                switch(data.status) {
                    case 'generating_qr':
                        document.getElementById('loadingQR').style.display = 'block';
                        document.getElementById('qrCode').style.display = 'none';
                        document.getElementById('whatsappReady').style.display = 'none';
                        updateButtonState('loading', 'Generando QR...');
                        break;
                        
                    case 'qr_ready':
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

        // ========================================
        // EVENTOS DEL MODAL
        // ========================================
        document.getElementById('stopWhatsAppBtn')?.addEventListener('click', async function() {
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