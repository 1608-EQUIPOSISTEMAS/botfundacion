<?php
session_start();
require_once 'conexion/conexion.php';

// Verificar permisos
$user_role = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : null;
if (!$user_role) {
    header('Location: login.php');
    exit;
}

// Definir los cores específicos
$core1 = '51922495159@c.us';
$core2 = '51963801628@c.us';

// Obtener filtros
$filtro_core = isset($_GET['core']) ? $_GET['core'] : 'todos';
$filtro_fecha_tipo = isset($_GET['fecha_tipo']) ? $_GET['fecha_tipo'] : 'todos';

// Variables de fecha
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$mes_seleccionado = isset($_GET['mes']) ? $_GET['mes'] : '';
$ano_seleccionado = isset($_GET['ano']) ? $_GET['ano'] : '';

// Construir condición WHERE para Core
$where_core = "";
$params = [];

switch($filtro_core) {
    case 'core1':
        $where_core = "AND core_contact = :core";
        $params[':core'] = $core1;
        break;
    case 'core2':
        $where_core = "AND core_contact = :core";
        $params[':core'] = $core2;
        break;
    case 'sin_asignar':
        $where_core = "AND (core_contact IS NULL OR core_contact = '')";
        break;
}

// Construir condición WHERE para Fechas
$where_fecha = "";

switch($filtro_fecha_tipo) {
    case 'rango':
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $where_fecha = "AND DATE(registration_date) BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $fecha_inicio;
            $params[':fecha_fin'] = $fecha_fin;
        }
        break;
    case 'mes':
        if (!empty($mes_seleccionado) && !empty($ano_seleccionado)) {
            $where_fecha = "AND YEAR(registration_date) = :ano AND MONTH(registration_date) = :mes";
            $params[':ano'] = $ano_seleccionado;
            $params[':mes'] = $mes_seleccionado;
        }
        break;
    case 'ano':
        if (!empty($ano_seleccionado)) {
            $where_fecha = "AND YEAR(registration_date) = :ano";
            $params[':ano'] = $ano_seleccionado;
        }
        break;
}

// Combinar condiciones WHERE
$where_completo = "WHERE 1=1 $where_core $where_fecha";

// KPI 1: Total de interacciones
$sql_total = "SELECT COUNT(*) as total FROM bot_history $where_completo";
$stmt = $pdo->prepare($sql_total);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_interacciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// KPI 2: Interacciones hoy
$sql_hoy = "SELECT COUNT(*) as hoy FROM bot_history WHERE DATE(registration_date) = CURDATE() $where_core";
$stmt_hoy = $pdo->prepare($sql_hoy);
foreach ($params as $key => $value) {
    if (strpos($key, 'core') !== false) {
        $stmt_hoy->bindValue($key, $value);
    }
}
$stmt_hoy->execute();
$interacciones_hoy = $stmt_hoy->fetch(PDO::FETCH_ASSOC)['hoy'];

// KPI 3: Interacciones últimos 7 días
$sql_semana = "SELECT COUNT(*) as semana FROM bot_history WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) $where_core";
$stmt_semana = $pdo->prepare($sql_semana);
foreach ($params as $key => $value) {
    if (strpos($key, 'core') !== false) {
        $stmt_semana->bindValue($key, $value);
    }
}
$stmt_semana->execute();
$interacciones_semana = $stmt_semana->fetch(PDO::FETCH_ASSOC)['semana'];

// KPI 4: Mensajes asignados
if ($filtro_core === 'todos') {
    $sql_core = "SELECT COUNT(*) as core_assigned FROM bot_history WHERE (core_contact IS NOT NULL AND core_contact != '') $where_fecha";
    $stmt_core_assigned = $pdo->prepare($sql_core);
    foreach ($params as $key => $value) {
        if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
            $stmt_core_assigned->bindValue($key, $value);
        }
    }
    $stmt_core_assigned->execute();
    $core_asignados = $stmt_core_assigned->fetch(PDO::FETCH_ASSOC)['core_assigned'];
} else {
    $core_asignados = $total_interacciones;
}

// Tasa de asignación
$tasa_asignacion = $total_interacciones > 0 ? round(($core_asignados / $total_interacciones) * 100, 2) : 0;

// Promedio mensajes por día
$sql_primer_registro = "SELECT MIN(DATE(registration_date)) as primera_fecha FROM bot_history $where_completo";
$stmt = $pdo->prepare($sql_primer_registro);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$primera_fecha = $stmt->fetch(PDO::FETCH_ASSOC)['primera_fecha'];

$dias_activo = 1;
if ($primera_fecha) {
    $dias_activo = max(1, (strtotime('now') - strtotime($primera_fecha)) / 86400);
}
$promedio_dia = round($total_interacciones / $dias_activo, 2);

// Comparativa entre Cores
$sql_core1 = "SELECT COUNT(*) as total FROM bot_history WHERE core_contact = :core1 $where_fecha";
$stmt = $pdo->prepare($sql_core1);
$stmt->bindValue(':core1', $core1);
foreach ($params as $key => $value) {
    if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$total_core1 = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$sql_core2 = "SELECT COUNT(*) as total FROM bot_history WHERE core_contact = :core2 $where_fecha";
$stmt = $pdo->prepare($sql_core2);
$stmt->bindValue(':core2', $core2);
foreach ($params as $key => $value) {
    if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$total_core2 = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$sql_sin_asignar = "SELECT COUNT(*) as total FROM bot_history WHERE (core_contact IS NULL OR core_contact = '') $where_fecha";
$stmt = $pdo->prepare($sql_sin_asignar);
foreach ($params as $key => $value) {
    if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$total_sin_asignar = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Gráfico: Interacciones por día (últimos 30 días o rango seleccionado)
$sql_por_dia = "
    SELECT DATE(registration_date) as fecha, COUNT(*) as total
    FROM bot_history
    $where_completo
    GROUP BY DATE(registration_date)
    ORDER BY fecha ASC
";
$stmt = $pdo->prepare($sql_por_dia);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$datos_por_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Análisis de palabras clave
$sql_mensajes = "SELECT invoke_text FROM bot_history $where_completo AND invoke_text IS NOT NULL AND invoke_text != ''";
$stmt = $pdo->prepare($sql_mensajes);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$mensajes = $stmt->fetchAll(PDO::FETCH_COLUMN);

$palabras_clave = [
    'inscribir' => 0, 'inscribirme' => 0, 'inscripción' => 0,
    'congreso' => 0, 'evento' => 0,
    'descuento' => 0, 'promoción' => 0,
    'información' => 0, 'info' => 0,
    'precio' => 0, 'costo' => 0,
    'fecha' => 0, 'cuando' => 0,
    'referido' => 0, 'referencia' => 0
];

foreach ($mensajes as $mensaje) {
    $mensaje_lower = mb_strtolower($mensaje, 'UTF-8');
    foreach ($palabras_clave as $palabra => &$contador) {
        if (strpos($mensaje_lower, $palabra) !== false) {
            $contador++;
        }
    }
}

arsort($palabras_clave);
$top_palabras = array_slice($palabras_clave, 0, 8, true);

// Distribución por hora del día
$sql_por_hora = "
    SELECT HOUR(registration_date) as hora, COUNT(*) as total
    FROM bot_history
    $where_completo
    GROUP BY HOUR(registration_date)
    ORDER BY hora ASC
";
$stmt = $pdo->prepare($sql_por_hora);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$datos_por_hora = $stmt->fetchAll(PDO::FETCH_ASSOC);

$horas_completas = array_fill(0, 24, 0);
foreach ($datos_por_hora as $dato) {
    $horas_completas[(int)$dato['hora']] = (int)$dato['total'];
}

// Rendimiento por Core (últimos 7 días o periodo seleccionado)
$where_fecha_cores = $where_fecha;
if (empty($where_fecha)) {
    $where_fecha_cores = "AND registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}

$sql_rendimiento_core1 = "
    SELECT DATE(registration_date) as fecha, COUNT(*) as total
    FROM bot_history
    WHERE core_contact = :core1 $where_fecha_cores
    GROUP BY DATE(registration_date)
    ORDER BY fecha ASC
";
$stmt = $pdo->prepare($sql_rendimiento_core1);
$stmt->bindValue(':core1', $core1);
foreach ($params as $key => $value) {
    if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$rendimiento_core1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_rendimiento_core2 = "
    SELECT DATE(registration_date) as fecha, COUNT(*) as total
    FROM bot_history
    WHERE core_contact = :core2 $where_fecha_cores
    GROUP BY DATE(registration_date)
    ORDER BY fecha ASC
";
$stmt = $pdo->prepare($sql_rendimiento_core2);
$stmt->bindValue(':core2', $core2);
foreach ($params as $key => $value) {
    if (strpos($key, 'fecha') !== false || strpos($key, 'ano') !== false || strpos($key, 'mes') !== false) {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$rendimiento_core2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ANALÍTICA FOUNDATION CON NUEVOS ESTADOS
$sql_foundation_funnel = "
    SELECT 
        state_contact,
        flow_status,
        COUNT(*) as total,
        COUNT(DISTINCT client_contact) as usuarios_unicos
    FROM bot_history 
    WHERE (state_contact LIKE 'foundation%' 
           OR state_contact = 'finish_flow' 
           OR state_contact LIKE 'abandoned_foundation%'
           OR state_contact = 'null')
    $where_core 
    $where_fecha
    GROUP BY state_contact, flow_status
";
$stmt = $pdo->prepare($sql_foundation_funnel);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$foundation_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar datos del embudo
$funnel_foundation = [
    'modality_selection' => 0,
    'payment_selection' => 0,
    'completados' => 0,
    'abandonados' => 0,
    'sin_interaccion' => 0
];

$detalle_estados = [];

foreach ($foundation_data as $row) {
    $state = $row['state_contact'];
    $total = (int)$row['total'];
    $unicos = (int)$row['usuarios_unicos'];
    
    // Clasificar por estado
    if ($state === 'foundation_modality_selection') {
        $funnel_foundation['modality_selection'] += $total;
        $detalle_estados[] = [
            'nombre' => '📋 Selección de Modalidad',
            'estado' => 'En Proceso',
            'total' => $total,
            'unicos' => $unicos,
            'color' => 'blue'
        ];
    } elseif ($state === 'foundation_payment_selection') {
        $funnel_foundation['payment_selection'] += $total;
        $detalle_estados[] = [
            'nombre' => '💳 Selección de Pago',
            'estado' => 'En Proceso',
            'total' => $total,
            'unicos' => $unicos,
            'color' => 'purple'
        ];
    } elseif ($state === 'finish_flow' && $row['flow_status'] === 'completed') {
        $funnel_foundation['completados'] += $total;
        $detalle_estados[] = [
            'nombre' => '✅ Flujo Completado',
            'estado' => 'Éxito',
            'total' => $total,
            'unicos' => $unicos,
            'color' => 'green'
        ];
    } elseif (strpos($state, 'abandoned_foundation') !== false) {
        $funnel_foundation['abandonados'] += $total;
        $fase = str_replace(['abandoned_foundation_', '_'], ['', ' '], $state);
        $detalle_estados[] = [
            'nombre' => '⏰ Abandonado en ' . ucwords($fase),
            'estado' => 'Abandono',
            'total' => $total,
            'unicos' => $unicos,
            'color' => 'orange'
        ];
    } elseif ($state === 'null') {
        $funnel_foundation['sin_interaccion'] += $total;
        $detalle_estados[] = [
            'nombre' => '❌ Sin Interacción del Cliente',
            'estado' => 'Sin Respuesta',
            'total' => $total,
            'unicos' => $unicos,
            'color' => 'gray'
        ];
    }
}

// Calcular métricas
$total_inicio = $funnel_foundation['modality_selection'] + $funnel_foundation['payment_selection'];
$total_general = array_sum($funnel_foundation);
$tasa_conversion = $total_inicio > 0 ? round(($funnel_foundation['completados'] / $total_inicio) * 100, 1) : 0;
$tasa_abandono = $total_inicio > 0 ? round(($funnel_foundation['abandonados'] / $total_inicio) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Analítica Bot - Connect Plus</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F8FAFC;
            
            --pastel-blue: #DBEAFE;
            --pastel-green: #D1FAE5;
            --pastel-purple: #E9D5FF;
            --pastel-pink: #FBCFE8;
            --pastel-yellow: #FEF3C7;
            --pastel-orange: #FED7AA;
            
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --accent-purple: #8B5CF6;
            --accent-pink: #EC4899;
            
            --text-primary: #0F172A;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            
            --border: #E2E8F0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.02);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        body {
            background: var(--bg-secondary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .content-wrapper {
            padding: 40px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .analytics-header {
            margin-bottom: 48px;
        }

        .analytics-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .analytics-header p {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        /* Filtros */
        .filter-section {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .filter-row {
            margin-bottom: 24px;
        }

        .filter-row:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            border-radius: 10px;
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tab:hover {
            background: var(--bg-secondary);
            text-decoration: none;
            color: var(--text-primary);
        }

        .filter-tab.active {
            background: var(--text-primary);
            color: white;
            border-color: var(--text-primary);
        }

        .filter-tab i {
            font-size: 16px;
            opacity: 0.8;
        }

        /* Filtro de Fechas */
        .date-filter-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .date-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .date-input-group label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .date-input-group input,
        .date-input-group select {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg-primary);
            transition: all 0.2s ease;
        }

        .date-input-group input:focus,
        .date-input-group select:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .btn-filter {
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-filter-apply {
            background: var(--accent-blue);
            color: white;
        }

        .btn-filter-apply:hover {
            background: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-filter-reset {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
        }

        .btn-filter-reset:hover {
            background: white;
            color: var(--text-primary);
        }

        /* KPIs */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .kpi-card-modern {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .kpi-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .kpi-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .kpi-card-modern:hover::before {
            opacity: 1;
        }

        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .kpi-icon-minimal {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .kpi-icon-minimal.blue { background: var(--pastel-blue); color: var(--accent-blue); }
        .kpi-icon-minimal.green { background: var(--pastel-green); color: var(--accent-green); }
        .kpi-icon-minimal.purple { background: var(--pastel-purple); color: var(--accent-purple); }
        .kpi-icon-minimal.pink { background: var(--pastel-pink); color: var(--accent-pink); }

        .kpi-label-modern {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-value-modern {
            font-size: 36px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.1;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .kpi-description {
            font-size: 13px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-badge-minimal {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success-minimal {
            background: var(--pastel-green);
            color: #065F46;
        }

        .badge-info-minimal {
            background: var(--pastel-blue);
            color: #075985;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }

        .summary-item {
            background: var(--bg-primary);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .summary-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .summary-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .chart-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .chart-container-modern {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .chart-header-modern {
            margin-bottom: 28px;
        }

        .chart-title-modern {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .chart-subtitle {
            font-size: 13px;
            color: var(--text-muted);
        }

        .chart-canvas {
            position: relative;
            height: 280px;
        }

        .chart-canvas-tall {
            position: relative;
            height: 320px;
        }

        @media (max-width: 1200px) {
            .chart-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 24px;
            }

            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .analytics-header h1 {
                font-size: 24px;
            }

            .date-filter-container {
                grid-template-columns: 1fr;
            }
        }

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

        .kpi-card-modern,
        .chart-container-modern {
            animation: fadeIn 0.4s ease-out;
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
                    <div class="analytics-header">
                        <h1>Analítica del Bot</h1>
                        <p>Métricas en tiempo real del rendimiento y eficiencia del bot de WhatsApp</p>
                    </div>

                    <!-- Filtros -->
                    <div class="filter-section">
                        <!-- Filtro de Core -->
                        <div class="filter-row">
                            <div class="filter-label">Filtrar por Core</div>
                            <div class="filter-tabs">
                                <a href="?core=todos&fecha_tipo=<?php echo $filtro_fecha_tipo; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>&mes=<?php echo $mes_seleccionado; ?>&ano=<?php echo $ano_seleccionado; ?>" 
                                   class="filter-tab <?php echo $filtro_core === 'todos' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-view-grid"></i>
                                    Vista General
                                </a>
                                <a href="?core=core1&fecha_tipo=<?php echo $filtro_fecha_tipo; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>&mes=<?php echo $mes_seleccionado; ?>&ano=<?php echo $ano_seleccionado; ?>" 
                                   class="filter-tab <?php echo $filtro_core === 'core1' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-account"></i>
                                    Linea 1
                                </a>
                                <a href="?core=core2&fecha_tipo=<?php echo $filtro_fecha_tipo; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>&mes=<?php echo $mes_seleccionado; ?>&ano=<?php echo $ano_seleccionado; ?>" 
                                   class="filter-tab <?php echo $filtro_core === 'core2' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-account"></i>
                                    Linea 2
                                </a>
                                <a href="?core=sin_asignar&fecha_tipo=<?php echo $filtro_fecha_tipo; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>&mes=<?php echo $mes_seleccionado; ?>&ano=<?php echo $ano_seleccionado; ?>" 
                                   class="filter-tab <?php echo $filtro_core === 'sin_asignar' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-account-off"></i>
                                    Sin Asignar
                                </a>
                            </div>
                        </div>

                        <!-- Filtro de Fechas -->
                        <div class="filter-row">
                            <div class="filter-label">Filtrar por Fecha</div>
                            <div class="filter-tabs">
                                <a href="?core=<?php echo $filtro_core; ?>&fecha_tipo=todos" 
                                   class="filter-tab <?php echo $filtro_fecha_tipo === 'todos' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-calendar-blank"></i>
                                    Todas las Fechas
                                </a>
                                <a href="javascript:void(0)" 
                                   onclick="showDateFilter('rango')"
                                   class="filter-tab <?php echo $filtro_fecha_tipo === 'rango' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-calendar-range"></i>
                                    Rango de Fechas
                                </a>
                                <a href="javascript:void(0)" 
                                   onclick="showDateFilter('mes')"
                                   class="filter-tab <?php echo $filtro_fecha_tipo === 'mes' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-calendar-month"></i>
                                    Por Mes
                                </a>
                                <a href="javascript:void(0)" 
                                   onclick="showDateFilter('ano')"
                                   class="filter-tab <?php echo $filtro_fecha_tipo === 'ano' ? 'active' : ''; ?>">
                                    <i class="mdi mdi-calendar"></i>
                                    Por Año
                                </a>
                            </div>

                            <!-- Formulario de Rango de Fechas -->
                            <form method="GET" id="rangoForm" style="display: <?php echo $filtro_fecha_tipo === 'rango' ? 'block' : 'none'; ?>;">
                                <input type="hidden" name="core" value="<?php echo $filtro_core; ?>">
                                <input type="hidden" name="fecha_tipo" value="rango">
                                <div class="date-filter-container">
                                    <div class="date-input-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required>
                                    </div>
                                    <div class="date-input-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" name="fecha_fin" value="<?php echo $fecha_fin; ?>" required>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn-filter btn-filter-apply">
                                        <i class="mdi mdi-check"></i>
                                        Aplicar
                                    </button>
                                    <a href="?core=<?php echo $filtro_core; ?>&fecha_tipo=todos" class="btn-filter btn-filter-reset">
                                        <i class="mdi mdi-close"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </form>

                            <!-- Formulario de Mes -->
                            <form method="GET" id="mesForm" style="display: <?php echo $filtro_fecha_tipo === 'mes' ? 'block' : 'none'; ?>;">
                                <input type="hidden" name="core" value="<?php echo $filtro_core; ?>">
                                <input type="hidden" name="fecha_tipo" value="mes">
                                <div class="date-filter-container">
                                    <div class="date-input-group">
                                        <label>Mes</label>
                                        <select name="mes" required>
                                            <option value="">Seleccione un mes</option>
                                            <option value="1" <?php echo $mes_seleccionado == '1' ? 'selected' : ''; ?>>Enero</option>
                                            <option value="2" <?php echo $mes_seleccionado == '2' ? 'selected' : ''; ?>>Febrero</option>
                                            <option value="3" <?php echo $mes_seleccionado == '3' ? 'selected' : ''; ?>>Marzo</option>
                                            <option value="4" <?php echo $mes_seleccionado == '4' ? 'selected' : ''; ?>>Abril</option>
                                            <option value="5" <?php echo $mes_seleccionado == '5' ? 'selected' : ''; ?>>Mayo</option>
                                            <option value="6" <?php echo $mes_seleccionado == '6' ? 'selected' : ''; ?>>Junio</option>
                                            <option value="7" <?php echo $mes_seleccionado == '7' ? 'selected' : ''; ?>>Julio</option>
                                            <option value="8" <?php echo $mes_seleccionado == '8' ? 'selected' : ''; ?>>Agosto</option>
                                            <option value="9" <?php echo $mes_seleccionado == '9' ? 'selected' : ''; ?>>Septiembre</option>
                                            <option value="10" <?php echo $mes_seleccionado == '10' ? 'selected' : ''; ?>>Octubre</option>
                                            <option value="11" <?php echo $mes_seleccionado == '11' ? 'selected' : ''; ?>>Noviembre</option>
                                            <option value="12" <?php echo $mes_seleccionado == '12' ? 'selected' : ''; ?>>Diciembre</option>
                                        </select>
                                    </div>
                                    <div class="date-input-group">
                                        <label>Año</label>
                                        <select name="ano" required>
                                            <option value="">Seleccione un año</option>
                                            <?php 
                                            $ano_actual = date('Y');
                                            for ($i = $ano_actual; $i >= 2020; $i--) {
                                                $selected = ($ano_seleccionado == $i) ? 'selected' : '';
                                                echo "<option value='$i' $selected>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn-filter btn-filter-apply">
                                        <i class="mdi mdi-check"></i>
                                        Aplicar
                                    </button>
                                    <a href="?core=<?php echo $filtro_core; ?>&fecha_tipo=todos" class="btn-filter btn-filter-reset">
                                        <i class="mdi mdi-close"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </form>

                            <!-- Formulario de Año -->
                            <form method="GET" id="anoForm" style="display: <?php echo $filtro_fecha_tipo === 'ano' ? 'block' : 'none'; ?>;">
                                <input type="hidden" name="core" value="<?php echo $filtro_core; ?>">
                                <input type="hidden" name="fecha_tipo" value="ano">
                                <div class="date-filter-container">
                                    <div class="date-input-group">
                                        <label>Año</label>
                                        <select name="ano" required>
                                            <option value="">Seleccione un año</option>
                                            <?php 
                                            $ano_actual = date('Y');
                                            for ($i = $ano_actual; $i >= 2020; $i--) {
                                                $selected = ($ano_seleccionado == $i) ? 'selected' : '';
                                                echo "<option value='$i' $selected>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn-filter btn-filter-apply">
                                        <i class="mdi mdi-check"></i>
                                        Aplicar
                                    </button>
                                    <a href="?core=<?php echo $filtro_core; ?>&fecha_tipo=todos" class="btn-filter btn-filter-reset">
                                        <i class="mdi mdi-close"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if ($filtro_core === 'todos'): ?>
                    <!-- Resumen Comparativo -->
                    <div class="summary-cards">
                        <div class="summary-item">
                            <div class="summary-number"><?php echo number_format($total_core1); ?></div>
                            <div class="summary-label">Core 1</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number"><?php echo number_format($total_core2); ?></div>
                            <div class="summary-label">Core 2</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number"><?php echo number_format($total_sin_asignar); ?></div>
                            <div class="summary-label">Sin Asignar</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number"><?php echo $tasa_asignacion; ?>%</div>
                            <div class="summary-label">Tasa Asignación</div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- KPIs Principales -->
                    <div class="kpi-grid">
                        <div class="kpi-card-modern">
                            <div class="kpi-header">
                                <div class="kpi-icon-minimal blue">
                                    <i class="mdi mdi-forum"></i>
                                </div>
                            </div>
                            <div class="kpi-label-modern">Total Conversaciones</div>
                            <div class="kpi-value-modern"><?php echo number_format($total_interacciones); ?></div>
                            <div class="kpi-description">
                                <span class="kpi-badge-minimal badge-info-minimal">
                                    <?php 
                                    switch($filtro_core) {
                                        case 'core1': echo 'Core 1'; break;
                                        case 'core2': echo 'Core 2'; break;
                                        case 'sin_asignar': echo 'Sin asignar'; break;
                                        default: echo 'Histórico';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="kpi-card-modern">
                            <div class="kpi-header">
                                <div class="kpi-icon-minimal green">
                                    <i class="mdi mdi-calendar-today"></i>
                                </div>
                            </div>
                            <div class="kpi-label-modern">Hoy</div>
                            <div class="kpi-value-modern"><?php echo number_format($interacciones_hoy); ?></div>
                            <div class="kpi-description">
                                <span class="kpi-badge-minimal badge-success-minimal">
                                    <i class="mdi mdi-trending-up"></i>
                                    Activo
                                </span>
                            </div>
                        </div>

                        <div class="kpi-card-modern">
                            <div class="kpi-header">
                                <div class="kpi-icon-minimal purple">
                                    <i class="mdi mdi-calendar-week"></i>
                                </div>
                            </div>
                            <div class="kpi-label-modern">Últimos 7 Días</div>
                            <div class="kpi-value-modern"><?php echo number_format($interacciones_semana); ?></div>
                            <div class="kpi-description">
                                Promedio: <?php echo round($interacciones_semana / 7, 1); ?>/día
                            </div>
                        </div>

                        <div class="kpi-card-modern">
                            <div class="kpi-header">
                                <div class="kpi-icon-minimal pink">
                                    <i class="mdi mdi-chart-line"></i>
                                </div>
                            </div>
                            <div class="kpi-label-modern">Promedio Diario</div>
                            <div class="kpi-value-modern"><?php echo $promedio_dia; ?></div>
                            <div class="kpi-description">
                                En <?php echo round($dias_activo); ?> días activos
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos Principales -->
                    <div class="chart-row">
                        <div class="chart-container-modern">
                            <div class="chart-header-modern">
                                <div class="chart-title-modern">Tendencia de Conversaciones</div>
                                <div class="chart-subtitle">
                                    <?php 
                                    if ($filtro_fecha_tipo === 'rango' && !empty($fecha_inicio)) {
                                        echo "Del $fecha_inicio al $fecha_fin";
                                    } elseif ($filtro_fecha_tipo === 'mes' && !empty($mes_seleccionado)) {
                                        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                        echo $meses[$mes_seleccionado] . " " . $ano_seleccionado;
                                    } elseif ($filtro_fecha_tipo === 'ano' && !empty($ano_seleccionado)) {
                                        echo "Año $ano_seleccionado";
                                    } else {
                                        echo "Últimos 30 días";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="chart-canvas">
                                <canvas id="chartTendencia"></canvas>
                            </div>
                        </div>

                        <div class="chart-container-modern">
                            <div class="chart-header-modern">
                                <div class="chart-title-modern">Palabras Clave</div>
                                <div class="chart-subtitle">Top menciones</div>
                            </div>
                            <div class="chart-canvas">
                                <canvas id="chartPalabras"></canvas>
                            </div>
                        </div>
                    </div>

                    <?php if ($filtro_core === 'todos'): ?>
                    <!-- Comparativa de Cores -->
                    <div class="chart-container-modern" style="margin-bottom: 24px;">
                        <div class="chart-header-modern">
                            <div class="chart-title-modern">Comparativa de Cores</div>
                            <div class="chart-subtitle">Rendimiento del periodo seleccionado</div>
                        </div>
                        <div class="chart-canvas-tall">
                            <canvas id="chartComparativa"></canvas>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Distribución Horaria -->
                    <div class="chart-container-modern">
                        <div class="chart-header-modern">
                            <div class="chart-title-modern">Actividad por Hora</div>
                            <div class="chart-subtitle">Distribución en 24 horas</div>
                        </div>
                        <div class="chart-canvas-tall">
                            <canvas id="chartHoraria"></canvas>
                        </div>
                    </div>

                    <!-- Análisis Foundation -->
                    <div class="chart-container-modern" style="margin-top: 24px;">
                        <div class="chart-header-modern">
                            <div class="chart-title-modern">🎯 Embudo de Conversión - Foundation</div>
                            <div class="chart-subtitle">Análisis del flujo de interacción completo</div>
                        </div>
                        
                        <div style="padding: 20px 0;">
                            <!-- KPIs Foundation -->
                            <div class="kpi-grid" style="margin-bottom: 32px;">
                                <div class="kpi-card-modern">
                                    <div class="kpi-header">
                                        <div class="kpi-icon-minimal blue">
                                            <i class="mdi mdi-account-check"></i>
                                        </div>
                                    </div>
                                    <div class="kpi-label-modern">En Selección Modalidad</div>
                                    <div class="kpi-value-modern"><?php echo number_format($funnel_foundation['modality_selection']); ?></div>
                                    <div class="kpi-description">Usuarios activos eligiendo</div>
                                </div>
                                
                                <div class="kpi-card-modern">
                                    <div class="kpi-header">
                                        <div class="kpi-icon-minimal purple">
                                            <i class="mdi mdi-credit-card"></i>
                                        </div>
                                    </div>
                                    <div class="kpi-label-modern">En Selección Pago</div>
                                    <div class="kpi-value-modern"><?php echo number_format($funnel_foundation['payment_selection']); ?></div>
                                    <div class="kpi-description">Avanzaron a métodos de pago</div>
                                </div>
                                
                                <div class="kpi-card-modern">
                                    <div class="kpi-header">
                                        <div class="kpi-icon-minimal green">
                                            <i class="mdi mdi-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="kpi-label-modern">Completados</div>
                                    <div class="kpi-value-modern"><?php echo number_format($funnel_foundation['completados']); ?></div>
                                    <div class="kpi-description">
                                        <span class="kpi-badge-minimal badge-success-minimal">
                                            <?php echo $tasa_conversion; ?>% conversión
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="kpi-card-modern">
                                    <div class="kpi-header">
                                        <div class="kpi-icon-minimal pink">
                                            <i class="mdi mdi-alert-circle"></i>
                                        </div>
                                    </div>
                                    <div class="kpi-label-modern">Abandonados</div>
                                    <div class="kpi-value-modern"><?php echo number_format($funnel_foundation['abandonados']); ?></div>
                                    <div class="kpi-description">
                                        <?php echo $tasa_abandono; ?>% del flujo activo
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen Adicional -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
                                <div style="background: var(--pastel-yellow); padding: 20px; border-radius: 12px; border-left: 4px solid #F59E0B;">
                                    <div style="font-size: 24px; font-weight: 700; color: #92400E; margin-bottom: 4px;">
                                        <?php echo number_format($funnel_foundation['sin_interaccion']); ?>
                                    </div>
                                    <div style="font-size: 12px; color: #78350F; font-weight: 500;">
                                        Sin Interacción del Cliente
                                    </div>
                                </div>
                                
                                <div style="background: var(--pastel-blue); padding: 20px; border-radius: 12px; border-left: 4px solid #3B82F6;">
                                    <div style="font-size: 24px; font-weight: 700; color: #1E40AF; margin-bottom: 4px;">
                                        <?php echo number_format($total_general); ?>
                                    </div>
                                    <div style="font-size: 12px; color: #1E3A8A; font-weight: 500;">
                                        Total Registros Foundation
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de Estados Detallada -->
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid var(--border);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Estado</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Fase</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Usuarios Únicos</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">% del Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalle_estados as $estado): 
                                            $porcentaje = $total_general > 0 ? round(($estado['total'] / $total_general) * 100, 1) : 0;
                                            
                                            // Colores según tipo
                                            $badge_class = '';
                                            switch($estado['color']) {
                                                case 'green': $badge_class = 'background: var(--pastel-green); color: #065F46;'; break;
                                                case 'blue': $badge_class = 'background: var(--pastel-blue); color: #075985;'; break;
                                                case 'purple': $badge_class = 'background: var(--pastel-purple); color: #6B21A8;'; break;
                                                case 'orange': $badge_class = 'background: var(--pastel-orange); color: #9A3412;'; break;
                                                case 'gray': $badge_class = 'background: #E5E7EB; color: #374151;'; break;
                                            }
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--border);">
                                            <td style="padding: 12px; font-size: 13px; font-weight: 500; color: var(--text-primary);">
                                                <?php echo $estado['nombre']; ?>
                                            </td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="<?php echo $badge_class; ?> padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                    <?php echo $estado['estado']; ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px; text-align: center; font-size: 16px; font-weight: 700; color: var(--text-primary);">
                                                <?php echo number_format($estado['total']); ?>
                                            </td>
                                            <td style="padding: 12px; text-align: center; font-size: 14px; font-weight: 600; color: var(--accent-blue);">
                                                <?php echo number_format($estado['unicos']); ?>
                                            </td>
                                            <td style="padding: 12px; text-align: center; font-size: 14px; font-weight: 600; color: var(--text-secondary);">
                                                <?php echo $porcentaje; ?>%
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($funnel_foundation['abandonados'] > 0): ?>
                            <!-- Alerta de Abandonos -->
                            <div style="margin-top: 32px; padding: 24px; background: var(--pastel-pink); border-radius: 12px; border-left: 4px solid #EC4899;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                    <i class="mdi mdi-alert-circle" style="font-size: 24px; color: #BE185D;"></i>
                                    <h4 style="font-size: 14px; font-weight: 600; color: #BE185D; margin: 0;">
                                        Atención: <?php echo $funnel_foundation['abandonados']; ?> usuarios abandonaron el proceso
                                    </h4>
                                </div>
                                <p style="font-size: 13px; color: #9F1239; margin: 0;">
                                    Tasa de abandono: <strong><?php echo $tasa_abandono; ?>%</strong> del flujo activo. 
                                    Considera optimizar estos puntos de fricción.
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Gráfico Embudo -->
                        <div class="chart-canvas-tall" style="margin-top: 24px;">
                            <canvas id="chartFunnelFoundation"></canvas>
                        </div>
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

    <script>
        // Mostrar formulario de fecha según tipo
        function showDateFilter(tipo) {
            document.getElementById('rangoForm').style.display = 'none';
            document.getElementById('mesForm').style.display = 'none';
            document.getElementById('anoForm').style.display = 'none';
            
            if (tipo === 'rango') {
                document.getElementById('rangoForm').style.display = 'block';
            } else if (tipo === 'mes') {
                document.getElementById('mesForm').style.display = 'block';
            } else if (tipo === 'ano') {
                document.getElementById('anoForm').style.display = 'block';
            }
        }

        // Configuración global
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#94A3B8';
        Chart.defaults.plugins.legend.display = false;

        // Datos
        const datosPorDia = <?php echo json_encode($datos_por_dia); ?>;
        const topPalabras = <?php echo json_encode($top_palabras); ?>;
        const horasCompletas = <?php echo json_encode($horas_completas); ?>;
        const rendimientoCore1 = <?php echo json_encode($rendimiento_core1); ?>;
        const rendimientoCore2 = <?php echo json_encode($rendimiento_core2); ?>;

        // Gráfico de Tendencia
        const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
        new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: datosPorDia.map(d => {
                    const date = new Date(d.fecha);
                    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    data: datosPorDia.map(d => d.total),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3B82F6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 14, weight: '700' },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#F1F5F9',
                            drawTicks: false
                        },
                        ticks: {
                            padding: 8,
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            padding: 8,
                            font: { size: 11 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Gráfico de Palabras Clave
        const ctxPalabras = document.getElementById('chartPalabras').getContext('2d');
        new Chart(ctxPalabras, {
            type: 'doughnut',
            data: {
                labels: Object.keys(topPalabras),
                datasets: [{
                    data: Object.values(topPalabras),
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#8B5CF6', '#EC4899',
                        '#F59E0B', '#EF4444', '#06B6D4', '#84CC16'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: { size: 11, weight: '500' },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#64748B'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });

        <?php if ($filtro_core === 'todos'): ?>
        // Gráfico Comparativo
        const labelsCores = rendimientoCore1.map(item => {
            const fecha = new Date(item.fecha);
            return fecha.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
        });

        const dataCore1Array = rendimientoCore1.map(item => parseInt(item.total));
        const dataCore2Array = rendimientoCore2.map(item => parseInt(item.total));

        const ctxComparativa = document.getElementById('chartComparativa').getContext('2d');
        new Chart(ctxComparativa, {
            type: 'bar',
            data: {
                labels: labelsCores,
                datasets: [
                    {
                        label: 'Core 1',
                        data: dataCore1Array,
                        backgroundColor: '#DBEAFE',
                        borderColor: '#3B82F6',
                        borderWidth: 2,
                        borderRadius: 8
                    },
                    {
                        label: 'Core 2',
                        data: dataCore2Array,
                        backgroundColor: '#D1FAE5',
                        borderColor: '#10B981',
                        borderWidth: 2,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            padding: 20,
                            font: { size: 12, weight: '600' },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#64748B'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#F1F5F9',
                            drawTicks: false
                        },
                        ticks: {
                            padding: 8,
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            padding: 8,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Gráfico de Actividad Horaria
        const ctxHoraria = document.getElementById('chartHoraria').getContext('2d');
        new Chart(ctxHoraria, {
            type: 'bar',
            data: {
                labels: Array.from({length: 24}, (_, i) => `${i}h`),
                datasets: [{
                    data: horasCompletas,
                    backgroundColor: horasCompletas.map(val => {
                        const max = Math.max(...horasCompletas);
                        const intensity = val / max;
                        return `rgba(139, 92, 246, ${0.3 + intensity * 0.5})`;
                    }),
                    borderColor: '#8B5CF6',
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label.replace('h', ':00')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#F1F5F9',
                            drawTicks: false
                        },
                        ticks: {
                            padding: 8,
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            padding: 8,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // Gráfico de Embudo Foundation
        const ctxFunnel = document.getElementById('chartFunnelFoundation').getContext('2d');
        new Chart(ctxFunnel, {
            type: 'bar',
            data: {
                labels: ['Modalidad', 'En vivo Tipo Pago', 'Completados', 'Abandonados', 'Sin Interacción'],
                datasets: [{
                    label: 'Usuarios',
                    data: [
                        <?php echo $funnel_foundation['modality_selection']; ?>,
                        <?php echo $funnel_foundation['payment_selection']; ?>,
                        <?php echo $funnel_foundation['completados']; ?>,
                        <?php echo $funnel_foundation['abandonados']; ?>,
                        <?php echo $funnel_foundation['sin_interaccion']; ?>
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(251, 146, 60, 0.7)',
                        'rgba(156, 163, 175, 0.7)'
                    ],
                    borderColor: [
                        '#3B82F6',
                        '#8B5CF6',
                        '#10B981',
                        '#FB923C',
                        '#9CA3AF'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 14, weight: '700' }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#F1F5F9', drawTicks: false },
                        ticks: { padding: 8, precision: 0, font: { size: 11 } }
                    },
                    y: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: { padding: 12, font: { size: 13, weight: '600' } }
                    }
                }
            }
        });
    </script>
</body>
</html>