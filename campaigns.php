<?php
session_start();
require_once 'conexion/conexioninmobiliaria.php';

$campaign_id = 1;
$user_name = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

// Obtener tipos de mensajes
$message_types = [];
try {
    $sql = "SELECT id, type_code, description, requires_media, allows_content FROM message_types ORDER BY description ASC";
    $stmt = $pdoInmobiliaria->prepare($sql);
    $stmt->execute();
    $message_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener tipos de mensajes: " . $e->getMessage());
}

// Obtener mensajes de la campaña
$messages_data = [];
try {
    $sql = "SELECT 
                m.id,
                m.content,
                m.sort_order,
                m.is_active,
                m.delay_seconds,
                m.notes,
                m.created_at,
                m.updated_at,
                mt.type_code,
                mt.description as type_description,
                mt.requires_media,
                mt.allows_content,
                COUNT(mm.id) as media_count
            FROM messages m
            INNER JOIN message_types mt ON m.message_type_id = mt.id
            LEFT JOIN message_media mm ON m.id = mm.message_id AND mm.deleted_at IS NULL
            WHERE m.campaign_id = :campaign_id 
            AND m.deleted_at IS NULL
            GROUP BY m.id
            ORDER BY m.sort_order ASC, m.created_at ASC";
    
    $stmt = $pdoInmobiliaria->prepare($sql);
    $stmt->execute(['campaign_id' => $campaign_id]);
    $messages_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener mensajes: " . $e->getMessage());
    $messages_data = [];
}

// Calcular métricas
$total_messages = count($messages_data);
$active_messages = count(array_filter($messages_data, fn($m) => $m['is_active'] == 1));
$inactive_messages = $total_messages - $active_messages;
$total_types = count(array_unique(array_column($messages_data, 'type_code')));
$total_media = array_sum(array_column($messages_data, 'media_count'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>W|E - Gestión de Mensajes</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.dataTables.min.css">
    
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
    </style>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #fafafa;
            color: #1a1a1a;
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

        .main-panel.expanded {
            margin-left: 0;
            width: 100%;
        }

        .sidebar-minimal {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .page-header p {
            font-size: 15px;
            color: #737373;
            font-weight: 400;
        }

        /* Métricas Grid - 5 columnas */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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

        .metric-icon.icon-total { background: #f0f9ff; }
        .metric-icon.icon-total i { color: #0284c7; }
        .metric-icon.icon-active { background: #f0fdf4; }
        .metric-icon.icon-active i { color: #22c55e; }
        .metric-icon.icon-inactive { background: #fef2f2; }
        .metric-icon.icon-inactive i { color: #ef4444; }
        .metric-icon.icon-types { background: #faf5ff; }
        .metric-icon.icon-types i { color: #a855f7; }
        .metric-icon.icon-media { background: #fffbeb; }
        .metric-icon.icon-media i { color: #f59e0b; }

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

        /* Barra de acciones */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            gap: 20px;
        }

        .view-toggle {
            display: flex;
            gap: 8px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 4px;
        }

        .view-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            background: transparent;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #737373;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .view-btn.active {
            background: #1a1a1a;
            color: #fff;
        }

        .btn-new {
            padding: 12px 28px;
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

        .btn-new:hover {
            background: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-new i {
            font-size: 18px;
        }

        /* VISTA CHAT */
        .chat-container {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
            display: none;
        }

        .chat-container.active {
            display: block;
        }

        .chat-header {
            background: #f9fafb;
            border-bottom: 1px solid #e5e5e5;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }

        .chat-info h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .chat-info p {
            font-size: 13px;
            color: #737373;
        }

        .chat-messages {
            padding: 32px 24px;
            min-height: 500px;
            max-height: 70vh;
            overflow-y: auto;
            background: #fafafa;
        }

        /* Burbuja de mensaje */
        .message-bubble-wrapper {
            margin-bottom: 20px;
            position: relative;
            cursor: move;
            transition: all 0.3s ease;
        }

        .message-bubble-wrapper:hover .bubble-actions {
            opacity: 1;
        }

        .message-bubble-wrapper.drag-over {
            margin-top: 60px;
        }

        .message-bubble {
            max-width: 65%;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 12px 16px;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }

        .message-bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: #1a1a1a;
        }

        .message-bubble.inactive {
            opacity: 0.5;
        }

        .bubble-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .bubble-order {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: #525252;
            flex-shrink: 0;
        }

        .bubble-type {
            padding: 3px 8px;
            background: #f5f5f5;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            color: #525252;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .bubble-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-left: auto;
        }

        .bubble-status.active {
            background: #22c55e;
        }

        .bubble-status.inactive {
            background: #ef4444;
        }

        .bubble-content {
            font-size: 15px;
            color: #1a1a1a;
            line-height: 1.5;
            margin-bottom: 8px;
            word-wrap: break-word;
        }

        .bubble-content.empty {
            color: #a3a3a3;
            font-style: italic;
            font-size: 14px;
        }

        .bubble-media {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 12px;
            color: #f59e0b;
            font-weight: 500;
        }

        .bubble-media i {
            font-size: 16px;
        }

        .bubble-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 11px;
            color: #a3a3a3;
        }

        .bubble-actions {
            position: absolute;
            top: 8px;
            right: -120px;
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .bubble-action-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .bubble-action-btn:hover {
            border-color: #1a1a1a;
            transform: translateY(-1px);
        }

        .bubble-action-btn i {
            font-size: 14px;
            color: #525252;
        }

        .bubble-action-btn.edit:hover {
            border-color: #eab308;
            background: #fef9e7;
        }

        .bubble-action-btn.edit:hover i {
            color: #eab308;
        }

        .bubble-action-btn.delete:hover {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .bubble-action-btn.delete:hover i {
            color: #ef4444;
        }

        .bubble-action-btn.media:hover {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .bubble-action-btn.media:hover i {
            color: #f59e0b;
        }

        /* Separador de tiempo */
        .time-separator {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            padding: 0 24px;
        }

        .time-line {
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .time-badge {
            padding: 6px 12px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #737373;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .time-badge i {
            font-size: 14px;
        }

        /* Drag handle */
        .drag-handle {
            position: absolute;
            left: -32px;
            top: 50%;
            transform: translateY(-50%);
            cursor: grab;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message-bubble-wrapper:hover .drag-handle {
            opacity: 1;
        }

        .drag-handle i {
            font-size: 20px;
            color: #d4d4d4;
        }

        .drag-handle:hover i {
            color: #737373;
        }

        .message-bubble-wrapper.dragging {
            opacity: 0.4;
        }

        .message-bubble-wrapper.dragging .message-bubble {
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        /* VISTA DATATABLE */
        .table-container {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 24px;
            display: none;
        }

        .table-container.active {
            display: block;
        }

        #messagesTable {
            width: 100% !important;
        }

        .dataTables_wrapper {
            padding: 0;
        }

        table.dataTable thead th {
            background: #f9fafb;
            color: #1a1a1a;
            font-weight: 600;
            font-size: 13px;
            padding: 16px 12px;
            border-bottom: 2px solid #e5e5e5;
        }

        table.dataTable tbody td {
            padding: 16px 12px;
            font-size: 14px;
            color: #525252;
            border-bottom: 1px solid #f5f5f5;
        }

        table.dataTable tbody tr:hover {
            background: #fafafa;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.active {
            background: #f0fdf4;
            color: #22c55e;
        }

        .status-badge.inactive {
            background: #fef2f2;
            color: #ef4444;
        }

        .status-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .type-badge {
            padding: 4px 10px;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #525252;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-actions {
            display: flex;
            gap: 6px;
        }

        .table-action-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #e5e5e5;
            border-radius: 5px;
            background: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .table-action-btn:hover {
            border-color: #1a1a1a;
        }

        .table-action-btn i {
            font-size: 13px;
            color: #525252;
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 120px 20px;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 24px;
            opacity: 0.3;
        }

        .empty-state-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 15px;
            color: #737373;
            margin-bottom: 32px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .bubble-actions {
                position: static;
                opacity: 1;
                margin-top: 12px;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .message-bubble {
                max-width: 85%;
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .view-toggle {
                width: 100%;
                justify-content: center;
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
                    <!-- Header -->
                    <div class="page-header">
                        <h1>Gestión de Mensajes</h1>
                        <p>Vista previa del flujo de mensajes - Campaña #<?php echo $campaign_id; ?></p>
                    </div>

                    <?php if (empty($messages_data)): ?>
                        <!-- Estado vacío -->
                        <div class="empty-state">
                            <div class="empty-state-icon">💬</div>
                            <h2 class="empty-state-title">No hay mensajes</h2>
                            <p class="empty-state-text">Crea tu primer mensaje para comenzar el flujo conversacional</p>
                            <button class="btn-new" onclick="openNewMessageModal()">
                                <i class="mdi mdi-plus"></i>
                                Nuevo mensaje
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Métricas -->
                        <div class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-total">
                                        <i class="mdi mdi-message-text-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value"><?php echo $total_messages; ?></div>
                                <div class="metric-label">Total mensajes</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-active">
                                        <i class="mdi mdi-check-circle-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value"><?php echo $active_messages; ?></div>
                                <div class="metric-label">Mensajes activos</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-inactive">
                                        <i class="mdi mdi-close-circle-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value"><?php echo $inactive_messages; ?></div>
                                <div class="metric-label">Mensajes inactivos</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-types">
                                        <i class="mdi mdi-shape-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value"><?php echo $total_types; ?></div>
                                <div class="metric-label">Tipos de mensaje</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-media">
                                        <i class="mdi mdi-image-multiple-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value"><?php echo $total_media; ?></div>
                                <div class="metric-label">Archivos adjuntos</div>
                            </div>
                        </div>

                        <!-- Barra de acciones -->
                        <div class="actions-bar">
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="chat" onclick="switchView('chat')">
                                    <i class="mdi mdi-message-text"></i>
                                    Vista Chat
                                </button>
                                <button class="view-btn" data-view="table" onclick="switchView('table')">
                                    <i class="mdi mdi-table"></i>
                                    Vista Tabla
                                </button>
                            </div>
                            <button class="btn-new" onclick="openNewMessageModal()">
                                <i class="mdi mdi-plus"></i>
                                Nuevo mensaje
                            </button>
                        </div>

                        <!-- Vista Chat -->
                        <div class="chat-container active" id="chatView">
                            <div class="chat-header">
                                <div class="chat-avatar">WE</div>
                                <div class="chat-info">
                                    <h3>Vista Previa de Campaña</h3>
                                    <p><?php echo $total_messages; ?> mensajes · Flujo automático</p>
                                </div>
                            </div>

                            <div class="chat-messages" id="chatMessages">
                                <?php 
                                foreach ($messages_data as $index => $msg): 
                                    $currentDelay = (int)$msg['delay_seconds'];
                                    
                                    if ($index > 0 && $currentDelay > 0):
                                ?>
                                <div class="time-separator">
                                    <div class="time-line"></div>
                                    <div class="time-badge">
                                        <i class="mdi mdi-clock-outline"></i>
                                        Espera <?php echo $currentDelay; ?>s
                                    </div>
                                    <div class="time-line"></div>
                                </div>
                                <?php endif; ?>

                                <div class="message-bubble-wrapper" 
                                    data-id="<?php echo $msg['id']; ?>"
                                    data-order="<?php echo $msg['sort_order']; ?>"
                                    draggable="true">
                                    
                                    <div class="drag-handle">
                                        <i class="mdi mdi-drag-vertical"></i>
                                    </div>

                                    <div class="message-bubble <?php echo $msg['is_active'] == 0 ? 'inactive' : ''; ?>">
                                        <div class="bubble-header">
                                            <div class="bubble-order"><?php echo $msg['sort_order']; ?></div>
                                            <div class="bubble-type"><?php echo htmlspecialchars($msg['type_code']); ?></div>
                                            <div class="bubble-status <?php echo $msg['is_active'] == 1 ? 'active' : 'inactive'; ?>"></div>
                                        </div>

                                        <?php if (!empty($msg['content'])): ?>
                                        <div class="bubble-content">
                                            <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="bubble-content empty">
                                            Sin contenido de texto
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($msg['media_count'] > 0): ?>
                                        <div class="bubble-media">
                                            <i class="mdi mdi-paperclip"></i>
                                            <?php echo $msg['media_count']; ?> archivo(s) adjunto(s)
                                        </div>
                                        <?php endif; ?>

                                        <div class="bubble-footer">
                                            <span><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                            <?php if (!empty($msg['notes'])): ?>
                                            <span><i class="mdi mdi-note-text-outline"></i> Con notas</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="bubble-actions">
                                        <button class="bubble-action-btn media" onclick="manageMedia(<?php echo $msg['id']; ?>)" title="Gestionar archivos">
                                            <i class="mdi mdi-image-multiple"></i>
                                        </button>
                                        <button class="bubble-action-btn edit" onclick="editMessage(<?php echo $msg['id']; ?>)" title="Editar">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="bubble-action-btn delete" onclick="deleteMessage(<?php echo $msg['id']; ?>)" title="Eliminar">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Vista Tabla -->
                        <div class="table-container" id="tableView">
                            <table id="messagesTable" class="display">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Orden</th>
                                        <th>Tipo</th>
                                        <th>Contenido</th>
                                        <th>Estado</th>
                                        <th>Media</th>
                                        <th>Delay</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages_data as $msg): ?>
                                    <tr data-id="<?php echo $msg['id']; ?>" data-order="<?php echo $msg['sort_order']; ?>">
                                        <td></td>
                                        <td><strong><?php echo $msg['sort_order']; ?></strong></td>
                                        <td>
                                            <span class="type-badge"><?php echo htmlspecialchars($msg['type_code']); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($msg['content'])): ?>
                                                <?php echo substr(htmlspecialchars($msg['content']), 0, 80); ?>
                                                <?php echo strlen($msg['content']) > 80 ? '...' : ''; ?>
                                            <?php else: ?>
                                                <em style="color: #a3a3a3;">Sin contenido</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $msg['is_active'] == 1 ? 'active' : 'inactive'; ?>">
                                                <span class="dot"></span>
                                                <?php echo $msg['is_active'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($msg['media_count'] > 0): ?>
                                                <i class="mdi mdi-paperclip"></i> <?php echo $msg['media_count']; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $msg['delay_seconds']; ?>s</td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="table-action-btn" onclick="manageMedia(<?php echo $msg['id']; ?>)" title="Media">
                                                    <i class="mdi mdi-image-multiple"></i>
                                                </button>
                                                <button class="table-action-btn" onclick="editMessage(<?php echo $msg['id']; ?>)" title="Editar">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="table-action-btn" onclick="deleteMessage(<?php echo $msg['id']; ?>)" title="Eliminar">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Nuevo Mensaje -->
    <?php include 'modals/messages/message-create-modal.php'; ?>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>

    <script>
        // Variables globales
        let draggedElement = null;
        let dataTable = null;

        // Cambiar entre vistas
        function switchView(view) {
            const chatView = document.getElementById('chatView');
            const tableView = document.getElementById('tableView');
            const buttons = document.querySelectorAll('.view-btn');

            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.view === view) {
                    btn.classList.add('active');
                }
            });

            if (view === 'chat') {
                chatView.classList.add('active');
                tableView.classList.remove('active');
            } else {
                chatView.classList.remove('active');
                tableView.classList.add('active');
                initDataTable();
            }
        }

        // Inicializar DataTable con Row Reorder
        function initDataTable() {
            if (dataTable) {
                return;
            }

            dataTable = $('#messagesTable').DataTable({
                rowReorder: {
                    selector: 'td:first-child',
                    update: false,
                    dataSrc: 'order'
                },
                columnDefs: [
                    { orderable: false, targets: [0, 7] },
                    { visible: true, targets: 0 }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[1, 'asc']]
            });

            // Evento al reordenar filas
            dataTable.on('row-reorder', function(e, diff, edit) {
                console.log('=== ROW REORDER EVENT ===');
                console.log('Diff:', diff);
                console.log('Edit:', edit);
                
                if (diff.length === 0) {
                    console.log('No hay cambios');
                    return;
                }

                const newOrder = [];
                
                // Obtener TODAS las filas en el nuevo orden
                const table = dataTable.table().node();
                const rows = $(table).find('tbody tr').get();
                
                console.log('Total de filas encontradas:', rows.length);
                
                rows.forEach(function(row, index) {
                    const $row = $(row);
                    const id = $row.data('id');
                    const newOrderNum = (index + 1) * 10;
                    
                    console.log(`Fila ${index}: ID=${id}, Nuevo orden=${newOrderNum}`);
                    
                    if (id) {
                        newOrder.push({
                            id: parseInt(id),
                            order: newOrderNum
                        });
                        
                        // Actualizar visualmente el número de orden
                        $row.find('td:eq(1) strong').text(newOrderNum);
                        $row.attr('data-order', newOrderNum);
                    }
                });

                console.log('Array final a enviar:', newOrder);

                if (newOrder.length > 0) {
                    saveOrder(newOrder);
                } else {
                    console.error('No se generó ningún orden válido');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo capturar el nuevo orden de los mensajes'
                    });
                }
            });
        }

        // Drag & Drop para Vista Chat
        const chatMessages = document.getElementById('chatMessages');

        if (chatMessages) {
            const bubbles = document.querySelectorAll('.message-bubble-wrapper');
            
            bubbles.forEach(bubble => {
                bubble.addEventListener('dragstart', function(e) {
                    draggedElement = this;
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });

                bubble.addEventListener('dragend', function(e) {
                    this.classList.remove('dragging');
                    // Pequeño delay para que se complete la animación
                    setTimeout(() => {
                        updateAllOrders();
                    }, 100);
                });

                bubble.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(chatMessages, e.clientY);
                    const draggable = document.querySelector('.dragging');
                    
                    if (!draggable) return;
                    
                    if (afterElement == null) {
                        chatMessages.appendChild(draggable);
                    } else {
                        chatMessages.insertBefore(draggable, afterElement);
                    }
                });
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.message-bubble-wrapper:not(.dragging):not(.time-separator)')];
            
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Actualizar todos los órdenes automáticamente
        function updateAllOrders() {
            const bubbles = document.querySelectorAll('.message-bubble-wrapper');
            const newOrder = [];
            let orderNum = 10;
            
            bubbles.forEach((bubble) => {
                const id = bubble.dataset.id;
                
                if (!id) return; // Skip si no tiene ID
                
                newOrder.push({
                    id: parseInt(id),
                    order: orderNum
                });
                
                // Actualizar visualmente
                const orderElement = bubble.querySelector('.bubble-order');
                if (orderElement) {
                    orderElement.textContent = orderNum;
                }
                bubble.dataset.order = orderNum;
                
                orderNum += 10;
            });
            
            console.log('Nuevo orden desde Chat:', newOrder);
            
            if (newOrder.length > 0) {
                saveOrder(newOrder);
            }
        }

        // Guardar nuevo orden
        function saveOrder(newOrder) {
            console.log('Enviando a servidor:', newOrder);
            
            fetch('actions/messages/reorder-messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    messages: newOrder,
                    campaign_id: <?php echo $campaign_id; ?>
                })
            })
            .then(response => {
                console.log('Status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Respuesta del servidor:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Orden actualizado!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        throw new Error(data.message || 'Error desconocido');
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    console.error('Texto recibido:', text);
                    throw e;
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el orden: ' + error.message,
                    confirmButtonText: 'Entendido'
                });
            });
        }

        // Abrir modal de nuevo mensaje
        function openNewMessageModal() {
            Swal.fire({
                icon: 'info',
                title: 'Próximamente',
                text: 'El modal de creación de mensajes estará disponible pronto',
                confirmButtonText: 'Entendido'
            });
        }

        function manageMedia(id) {
            window.location.href = 'message-media.php?id=' + id;
        }

        function editMessage(id) {
            window.location.href = 'message-edit.php?id=' + id;
        }

        function deleteMessage(id) {
            Swal.fire({
                title: '¿Eliminar mensaje?',
                text: "Se eliminará del flujo de la campaña",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#737373',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('actions/messages/delete-message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'message_id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }
    </script>

    <script>
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