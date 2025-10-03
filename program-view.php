<?php
session_start();
require_once 'conexion/conexion.php';

// Validar ID
$program_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($program_id <= 0) {
    header('Location: programs.php');
    exit;
}

// Obtener datos del programa principal
try {
    $sql = "SELECT 
        p.*,
        c1.description as linea,
        c2.description as categoria,
        c3.description as tipo_programa
    FROM programs p
    LEFT JOIN catalog c1 ON p.cat_category = c1.catalog_id
    LEFT JOIN catalog c2 ON p.cat_type_program = c2.catalog_id
    LEFT JOIN catalog c3 ON p.cat_type_program = c3.catalog_id
    WHERE p.program_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$program_id]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$program) {
        header('Location: programs.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    die("Error al cargar el programa");
}

// Obtener versiones del programa
try {
    $sql = "SELECT 
        pv.*,
        c1.description as categoria_curso,
        c2.description as tipo_modalidad
    FROM program_versions pv
    LEFT JOIN catalog c1 ON pv.cat_category_course = c1.catalog_id
    LEFT JOIN catalog c2 ON pv.cat_type_modality = c2.catalog_id
    WHERE pv.program_id = ?
    ORDER BY pv.program_version_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$program_id]);
    $versions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $versions = [];
}

// Verificar si necesita mostrar estructura (cat_type_program: 542, 543, 544, 545)
$show_structure = in_array($program['cat_type_program'], [542, 543, 544, 545]);

// Si necesita estructura, obtenerla
$structures = [];
if ($show_structure && !empty($versions)) {
    try {
        $version_ids = array_column($versions, 'program_version_id');
        $placeholders = str_repeat('?,', count($version_ids) - 1) . '?';
        
        $sql = "SELECT * FROM program_version_structure 
                WHERE parent_program_version_id IN ($placeholders)
                ORDER BY parent_program_version_id, child_program_version_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($version_ids);
        $structures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>W|E - <?php echo htmlspecialchars($program['program_name']); ?></title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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

        /* Header */
        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .page-meta {
            display: flex;
            gap: 16px;
            margin-top: 8px;
        }

        .meta-badge {
            padding: 6px 12px;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 13px;
            color: #737373;
        }

        .btn-back {
            padding: 10px 20px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #525252;
            text-decoration: none;
        }

        .btn-back:hover {
            border-color: #1a1a1a;
            background: #fafafa;
        }

        /* Card info principal */
        .info-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            color: #1a1a1a;
            font-weight: 500;
        }

        .info-value a {
            color: #0066ff;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        /* Sección de versiones */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .version-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .version-card:hover {
            border-color: #1a1a1a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .version-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f5f5f5;
        }

        .version-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .version-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: #f0fdf4;
            color: #22c55e;
        }

        .status-inactive {
            background: #fef2f2;
            color: #ef4444;
        }

        .version-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        /* Estructura */
        .structure-list {
            margin-top: 12px;
            padding: 16px;
            background: #fafafa;
            border-radius: 8px;
        }

        .structure-item {
            padding: 8px 12px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-panel {
                margin-left: 0;
            }

            .info-grid,
            .version-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .info-grid,
            .version-grid {
                grid-template-columns: 1fr;
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
                        <div>
                            <h1><?php echo htmlspecialchars($program['program_name']); ?></h1>
                            <div class="page-meta">
                                <span class="meta-badge"><?php echo htmlspecialchars($program['linea']); ?></span>
                                <span class="meta-badge"><?php echo htmlspecialchars($program['categoria']); ?></span>
                            </div>
                        </div>
                        <a href="programas.php" class="btn-back">
                            <i class="mdi mdi-arrow-left"></i>
                            Volver
                        </a>
                    </div>

                    <!-- Info principal del programa -->
                    <div class="info-card">
                        <h3 class="section-title">
                            Información General
                        </h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Horas Certificadas</span>
                                <span class="info-value"><?php echo htmlspecialchars($program['certified_hours'] ?? 'N/A'); ?> horas</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nombre Comercial</span>
                                <span class="info-value"><?php echo htmlspecialchars($program['commercial_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Abreviación</span>
                                <span class="info-value"><?php echo htmlspecialchars($program['abbreviation_name'] ?? 'N/A'); ?></span>
                            </div>
                            <?php if (!empty($program['brochure_url'])): ?>
                            <div class="info-item">
                                <span class="info-label">Brochure</span>
                                <span class="info-value">
                                    <a href="<?php echo htmlspecialchars($program['brochure_url']); ?>" target="_blank">
                                        <i class="mdi mdi-file-pdf-box"></i> Ver PDF
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($program['voice_url'])): ?>
                            <div class="info-item">
                                <span class="info-label">Audio</span>
                                <span class="info-value">
                                    <a href="<?php echo htmlspecialchars($program['voice_url']); ?>" target="_blank">
                                        <i class="mdi mdi-microphone"></i> Escuchar
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($program['video_url'])): ?>
                            <div class="info-item">
                                <span class="info-label">Video</span>
                                <span class="info-value">
                                    <a href="<?php echo htmlspecialchars($program['video_url']); ?>" target="_blank">
                                        <i class="mdi mdi-video"></i> Ver video
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($program['sales_page_url'])): ?>
                            <div class="info-item">
                                <span class="info-label">Página de Ventas</span>
                                <span class="info-value">
                                    <a href="<?php echo htmlspecialchars($program['sales_page_url']); ?>" target="_blank">
                                        <i class="mdi mdi-web"></i> Visitar
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($program['img_url'])): ?>
                            <div class="info-item">
                                <span class="info-label">Imagen</span>
                                <span class="info-value">
                                    <a href="<?php echo htmlspecialchars($program['img_url']); ?>" target="_blank">
                                        <i class="mdi mdi-image"></i> Ver imagen
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Versiones -->
                    <h3 class="section-title">
                        Versiones del Programa (<?php echo count($versions); ?>)
                    </h3>

                    <?php if (empty($versions)): ?>
                        <div class="info-card">
                            <p style="color: #737373; text-align: center; padding: 40px;">
                                No hay versiones registradas para este programa
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($versions as $version): ?>
                            <div class="version-card">
                                <div class="version-header">
                                    <span class="version-title">
                                        Versión: <?php echo htmlspecialchars($version['version_code']); ?>
                                    </span>
                                    <span class="version-status <?php echo $version['active'] == '1' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $version['active'] == '1' ? 'Activa' : 'Inactiva'; ?>
                                    </span>
                                </div>

                                <div class="version-grid">
                                    <div class="info-item">
                                        <span class="info-label">Sesiones</span>
                                        <span class="info-value"><?php echo htmlspecialchars($version['sessions'] ?? '0'); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Categoría</span>
                                        <span class="info-value"><?php echo htmlspecialchars($version['categoria_curso'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Modalidad</span>
                                        <span class="info-value"><?php echo htmlspecialchars($version['tipo_modalidad'] ?? 'N/A'); ?></span>
                                    </div>
                                    <?php if (!empty($version['brochure_url'])): ?>
                                    <div class="info-item">
                                        <span class="info-label">Brochure</span>
                                        <span class="info-value">
                                            <a href="<?php echo htmlspecialchars($version['brochure_url']); ?>" target="_blank">Ver PDF</a>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($show_structure): ?>
                                    <?php 
                                    $version_structures = array_filter($structures, function($s) use ($version) {
                                        return $s['parent_program_version_id'] == $version['program_version_id'];
                                    });
                                    ?>
                                    <?php if (!empty($version_structures)): ?>
                                        <div class="structure-list">
                                            <div class="info-label" style="margin-bottom: 12px;">Estructura del Programa</div>
                                            <?php foreach ($version_structures as $struct): ?>
                                                <div class="structure-item">
                                                    ID Hijo: <?php echo htmlspecialchars($struct['child_program_version_id']); ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
</body>
</html>