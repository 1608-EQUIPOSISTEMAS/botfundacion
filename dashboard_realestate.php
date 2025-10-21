<?php
session_start();
require_once 'conexion/conexion.php';
require_once 'conexion/conexioninmobiliaria.php';

// Obtener el rol del usuario
$user_role = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : null;
$user_permissions = [];
$role_name = 'Sin Rol';
$role_class = 'default';

// Mapear rol a permisos y nombres
switch($user_role) {
    case 1:
        $user_permissions = ['all'];
        $role_name = 'Administrador';
        $role_class = 'admin';
        break;
    case 2:
        $user_permissions = ['fundacion'];
        $role_name = 'Foundation';
        $role_class = 'foundation';
        break;
    case 3:
        $user_permissions = ['members'];
        $role_name = 'Asesor Comercial';
        $role_class = 'comercial';
        break;
    case 4:
        $user_permissions = ['members', 'fundacion'];
        $role_name = 'Comercial';
        $role_class = 'comercial';
        break;
    case 5:
        $user_permissions = ['inmobiliaria'];
        $role_name = 'Inmobiliaria';
        $role_class = 'inmobiliaria';
        break;
    default:
        $user_permissions = [];
        $role_name = 'Sin Permisos';
        $role_class = 'default';
}

// Obtener datos de las vistas
try {
    // Vista 1: Conversation Summary
    $stmtConversations = $pdoInmobiliaria->query("SELECT user_phone as celular, user_name as nombre, campaign_name as nombre_campaña, conversation_started_at FROM vw_conversation_summary WHERE conversation_id <> 16 ORDER BY conversation_started_at DESC");
    $conversations = $stmtConversations->fetchAll(PDO::FETCH_ASSOC);
    
    // Vista 2: Campaign Performance
    $stmtCampaigns = $pdoInmobiliaria->query("SELECT * FROM vw_campaign_performance ORDER BY priority DESC");
    $campaigns = $stmtCampaigns->fetchAll(PDO::FETCH_ASSOC);
    
    // Vista 3: Keyword Effectiveness
    $stmtKeywords = $pdoInmobiliaria->query("SELECT * FROM vw_keyword_effectiveness");
    $keywords = $stmtKeywords->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular métricas generales
    $total_conversations = count($conversations);
    $total_campaigns = count($campaigns);
    $total_keywords = count($keywords);
    
    // Métricas agregadas
    $total_matches = array_sum(array_column($keywords, 'match_count'));
    $avg_conversion = count($campaigns) > 0 ? round(array_sum(array_column($campaigns, 'conversion_rate')) / count($campaigns), 2) : 0;
    
} catch (PDOException $e) {
    $conversations = [];
    $campaigns = [];
    $keywords = [];
    $error_message = "Error al obtener datos: " . $e->getMessage();
    error_log("Error analytics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Analytics Report - Connect Plus</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
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
            max-width: none;
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

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.02em;
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

        .btn-export {
            padding: 10px 20px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-export:hover {
            background: #000;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* ========================================
           TARJETAS DE MÉTRICAS
        ======================================== */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
        }

        .metric-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .metric-content {
            flex: 1;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }

        .metric-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ========================================
           SECCIÓN DE GRÁFICOS
        ======================================== */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-container {
            background: var(--bg-primary);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            padding: 24px;
        }

        .chart-header {
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .chart-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        /* ========================================
           TABLA MINIMALISTA
        ======================================== */
        .data-container {
            background: var(--bg-primary);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .data-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--border);
        }

        .data-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

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

        .metric-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            background: var(--bg-secondary);
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            color: var(--accent);
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-indicator::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-indicator.success {
            color: var(--success);
        }

        .status-indicator.warning {
            color: var(--warning);
        }

        .status-indicator.danger {
            color: var(--danger);
        }

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

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
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

            .table-minimal {
                font-size: 13px;
            }

            .table-minimal thead th,
            .table-minimal tbody td {
                padding: 12px 16px;
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

        .table-minimal tbody tr,
        .metric-card,
        .chart-container {
            animation: fadeIn 0.3s ease-out;
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
                                <h1 class="header-title">Analisis de Campañas</h1>
                            </div>
                            
                            <button onclick="exportReport()" class="btn-export">
                                <i class="mdi mdi-download"></i>
                                <span>Exportar Reporte</span>
                            </button>
                        </div>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div style="padding: 20px; background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 24px;">
                            <i class="mdi mdi-alert-circle"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- TARJETAS DE MÉTRICAS -->
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(6, 182, 212, 0.1); color: #06B6D4;">
                                <i class="mdi mdi-message-text-outline"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo number_format($total_conversations); ?></div>
                                <div class="metric-label">Conversaciones</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="mdi mdi-bullhorn-outline"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $total_campaigns; ?></div>
                                <div class="metric-label">Campañas Activas</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                <i class="mdi mdi-key-outline"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $total_keywords; ?></div>
                                <div class="metric-label">Keywords</div>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                <i class="mdi mdi-chart-line"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value"><?php echo $avg_conversion; ?>%</div>
                                <div class="metric-label">Conversión Promedio</div>
                            </div>
                        </div>
                    </div>

                    <!-- GRÁFICOS -->
                    <div class="charts-grid">
                        <!-- Gráfico 1: Performance de Campañas -->
                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Performance de Campañas</div>
                                <div class="chart-subtitle">Total de conversaciones por campaña</div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="campaignsChart"></canvas>
                            </div>
                        </div>

                        <!-- Gráfico 2: Keywords Efectivas -->
                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Keywords Más Efectivas</div>
                                <div class="chart-subtitle">Top 10 keywords por matches</div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="keywordsChart"></canvas>
                            </div>
                        </div>

                        <!-- Gráfico 3: Tendencia de Conversaciones -->
                        <div class="chart-container" style="grid-column: span 2;">
                            <div class="chart-header">
                                <div class="chart-title">Tendencia de Conversaciones</div>
                                <div class="chart-subtitle">Últimas 30 conversaciones en el tiempo</div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="trendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA 1: Conversaciones -->
                    <div class="data-container">
                        <div class="data-header">
                            <h2>Resumen de Conversaciones</h2>
                        </div>

                        <?php if (!empty($conversations)): ?>
                            <table id="conversationsTable" class="table-minimal">
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($conversations[0]) as $column): ?>
                                            <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($conversations as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $value): ?>
                                                <td>
                                                    <?php if (is_numeric($value) && $value > 100): ?>
                                                        <span class="metric-badge"><?php echo number_format($value); ?></span>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($value ?? '—'); ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="mdi mdi-message-off-outline"></i>
                                <h3>Sin conversaciones</h3>
                                <p>No hay datos disponibles en esta vista</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TABLA 2: Performance de Campañas -->
                    <div class="data-container">
                        <div class="data-header">
                            <h2>Rendimiento de Campañas</h2>
                        </div>

                        <?php if (!empty($campaigns)): ?>
                            <table id="campaignsTable" class="table-minimal">
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($campaigns[0]) as $column): ?>
                                            <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($campaigns as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $value): ?>
                                                <td>
                                                    <?php if (strpos($key, 'rate') !== false || strpos($key, 'conversion') !== false): ?>
                                                        <span class="status-indicator <?php echo $value > 50 ? 'success' : ($value > 25 ? 'warning' : 'danger'); ?>">
                                                            <?php echo number_format($value, 2); ?>%
                                                        </span>
                                                    <?php elseif (is_numeric($value) && $value > 100): ?>
                                                        <span class="metric-badge"><?php echo number_format($value); ?></span>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($value ?? '—'); ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="mdi mdi-bullhorn-off-outline"></i>
                                <h3>Sin campañas</h3>
                                <p>No hay datos de performance disponibles</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TABLA 3: Keywords Effectiveness -->
                    <div class="data-container">
                        <div class="data-header">
                            <h2>Efectividad de Keywords</h2>
                        </div>

                        <?php if (!empty($keywords)): ?>
                            <table id="keywordsTable" class="table-minimal">
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($keywords[0]) as $column): ?>
                                            <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($keywords as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $value): ?>
                                                <td>
                                                    <?php if (strpos($key, 'count') !== false && is_numeric($value)): ?>
                                                        <span class="metric-badge"><?php echo number_format($value); ?></span>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($value ?? '—'); ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="mdi mdi-key-off-outline"></i>
                                <h3>Sin keywords</h3>
                                <p>No hay datos de efectividad disponibles</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // ========================================
        // CONFIGURACIÓN
        // ========================================
        const USER_ROLE = <?php echo json_encode($user_role); ?>;

        // Datos para gráficos
        const campaignsData = <?php echo json_encode($campaigns); ?>;
        const keywordsData = <?php echo json_encode($keywords); ?>;
        const conversationsData = <?php echo json_encode($conversations); ?>;

        // ========================================
        // DATATABLES MINIMALISTAS
        // ========================================
        $(document).ready(function() {
            $('.table-minimal').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "ordering": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });
        });

        // ========================================
        // GRÁFICOS CON CHART.JS
        // ========================================

        // Configuración global de Chart.js
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif';
        Chart.defaults.color = '#64748B';

        // GRÁFICO 1: Performance de Campañas (Barra Horizontal)
        if (campaignsData.length > 0) {
            const ctx1 = document.getElementById('campaignsChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: campaignsData.slice(0, 10).map(c => c.campaign_name || 'Sin nombre'),
                    datasets: [{
                        label: 'Conversaciones',
                        data: campaignsData.slice(0, 10).map(c => c.total_conversations || 0),
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderColor: '#06B6D4',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: '#F1F5F9' },
                            ticks: { color: '#64748B' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#0F172A', font: { weight: 500 } }
                        }
                    }
                }
            });
        }

        // GRÁFICO 2: Keywords (Doughnut)
        if (keywordsData.length > 0) {
            const ctx2 = document.getElementById('keywordsChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: keywordsData.slice(0, 10).map(k => k.matched_keyword || 'Sin keyword'),
                    datasets: [{
                        data: keywordsData.slice(0, 10).map(k => parseFloat(k.total_triggers) || 0),
                        backgroundColor: [
                            '#06B6D4', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444',
                            '#3B82F6', '#EC4899', '#14B8A6', '#F97316', '#6366F1'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });
        }

        // GRÁFICO 3: Tendencia (Línea)
        if (conversationsData.length > 0) {
            const ctx3 = document.getElementById('trendChart').getContext('2d');
            
            // Agrupar conversaciones por fecha
            const groupedByDate = conversationsData.reduce((acc, conv) => {
                const date = conv.conversation_started_at ? conv.conversation_started_at.split(' ')[0] : 'Sin fecha';
                acc[date] = (acc[date] || 0) + 1;
                return acc;
            }, {});

            const sortedDates = Object.keys(groupedByDate).sort();
            
            new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: sortedDates,
                    datasets: [{
                        label: 'Conversaciones',
                        data: sortedDates.map(date => groupedByDate[date]),
                        borderColor: '#06B6D4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#06B6D4',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: '#F1F5F9' },
                            ticks: { 
                                color: '#64748B',
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            grid: { color: '#F1F5F9' },
                            ticks: { color: '#64748B' }
                        }
                    }
                }
            });
        }

        // ========================================
        // EXPORTAR REPORTE
        // ========================================
        function exportReport() {
            Swal.fire({
                title: 'Exportar Reporte',
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <p style="color: #64748B; margin-bottom: 16px;">
                            Selecciona el formato de exportación:
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button onclick="exportPDF()" style="padding: 12px; background: #EF4444; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="mdi mdi-file-pdf-box"></i> Exportar como PDF
                            </button>
                            <button onclick="exportExcel()" style="padding: 12px; background: #10B981; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="mdi mdi-file-excel-box"></i> Exportar como Excel
                            </button>
                            <button onclick="exportCSV()" style="padding: 12px; background: #06B6D4; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="mdi mdi-file-delimited-outline"></i> Exportar como CSV
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                width: 500
            });
        }

        function exportPDF() {
            Swal.fire('En desarrollo', 'La exportación PDF estará disponible pronto', 'info');
        }

        function exportExcel() {
            Swal.fire('En desarrollo', 'La exportación Excel estará disponible pronto', 'info');
        }

        function exportCSV() {
            Swal.fire('En desarrollo', 'La exportación CSV estará disponible pronto', 'info');
        }

        // ========================================
        // SIDEBAR COLLAPSE
        // ========================================
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