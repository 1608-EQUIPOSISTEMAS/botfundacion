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
            c2.description as tipo_modalidad,
            c3.variable_2 AS categoria_seguimiento
            FROM program_versions pv
            LEFT JOIN catalog c1 ON pv.cat_category_course = c1.catalog_id
            LEFT JOIN catalog c2 ON pv.cat_type_modality = c2.catalog_id
            LEFT JOIN program_editions pe ON pv.program_version_id = pe.program_version_id
            LEFT JOIN catalog c3 ON pe.cat_category_following = c3.catalog_id
            WHERE pv.program_id = ?
            ORDER BY pv.program_version_id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$program_id]);
        $versions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $versions = [];
    }

    // Verificar si necesita mostrar estructura
    $show_structure = in_array($program['cat_type_program'], [542, 543, 544, 545]);

    // Si necesita estructura, obtenerla
    $structures = [];
    if ($show_structure && !empty($versions)) {
        try {
            $version_ids = array_column($versions, 'program_version_id');
            $placeholders = str_repeat('?,', count($version_ids) - 1) . '?';
            
            $sql = "SELECT pvs.parent_program_version_id, p.program_name as nombre_curso FROM program_version_structure pvs
                    INNER JOIN programs p on pvs.child_program_version_id = p.program_id
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
    <link rel="shortcut icon" href="assets/images/we.png" />

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
            line-height: 1.5;
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

        /* Header mejorado */
        .page-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        .header-content {
            flex: 1;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .page-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .meta-badge {
            padding: 6px 14px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 13px;
            color: #525252;
            font-weight: 500;
            transition: all 0.2s;
        }

        .meta-badge:hover {
            background: #e5e5e5;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }

        .btn-back {
            padding: 10px 20px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #525252;
            text-decoration: none;
        }

        .btn-back:hover {
            border-color: #1a1a1a;
            background: #fafafa;
            transform: translateX(-2px);
        }

        .btn-primary {
            padding: 10px 24px;
            background: #1a1a1a;
            border: 1px solid #1a1a1a;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #0a0a0a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Card info principal mejorado */
        .info-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            transition: all 0.3s;
        }

        .info-card:hover {
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .info-value {
            font-size: 15px;
            color: #0a0a0a;
            font-weight: 500;
        }

        .info-value a {
            color: #0066ff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .info-value a:hover {
            color: #0052cc;
            transform: translateX(2px);
        }

        .info-value a i {
            font-size: 16px;
        }

        /* Sección de versiones mejorada */
        .versions-section {
            margin-top: 48px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #0a0a0a;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #525252;
        }

        /* Version card mejorada */
        .version-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.2s;
            position: relative;
            cursor: pointer;
        }

        .version-card:hover {
            border-color: #1a1a1a;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .version-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f5f5f5;
        }

        .version-title-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .version-title {
            font-size: 17px;
            font-weight: 600;
            color: #0a0a0a;
        }

        .version-actions {
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .version-card:hover .version-actions {
            opacity: 1;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            color: #525252;
        }

        .btn-icon:hover {
            background: #e5e5e5;
            color: #0a0a0a;
        }

        .btn-icon.danger:hover {
            background: #fee;
            color: #ef4444;
        }

        .btn-icon.edit:hover {
            background: #fee;
            color: #eab308;
        }

        .version-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-active {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-inactive {
            background: #fef2f2;
            color: #dc2626;
        }

        .version-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        /* Estructura mejorada */
        .structure-list {
            margin-top: 20px;
            padding: 20px;
            background: #fafafa;
            border-radius: 12px;
            border: 1px dashed #e5e5e5;
        }

        .structure-header {
            font-size: 12px;
            font-weight: 600;
            color: #525252;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .structure-item {
            padding: 12px 16px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #525252;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .structure-item:last-child {
            margin-bottom: 0;
        }

        .structure-item:hover {
            border-color: #1a1a1a;
            background: #fafafa;
        }

        .structure-item i {
            color: #a3a3a3;
            font-size: 16px;
        }

        /* Empty state mejorado */
        .empty-state {
            background: #fff;
            border: 2px dashed #e5e5e5;
            border-radius: 16px;
            padding: 64px 32px;
            text-align: center;
            transition: all 0.3s;
        }

        .empty-state:hover {
            border-color: #d4d4d4;
            background: #fafafa;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border-radius: 16px;
            color: #a3a3a3;
            font-size: 32px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 8px;
        }

        .empty-text {
            color: #737373;
            font-size: 14px;
            margin-bottom: 24px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-panel {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 32px 24px;
            }

            .page-header {
                flex-direction: column;
                gap: 16px;
            }

            .header-actions {
                width: 100%;
            }

            .btn-back,
            .btn-primary {
                flex: 1;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .info-grid,
            .version-grid {
                grid-template-columns: 1fr;
            }

            .version-actions {
                opacity: 1;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
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
                        <div class="header-content">
                            <h1><?php echo htmlspecialchars($program['program_name']); ?></h1>
                            <div class="page-meta">
                                <span class="meta-badge">
                                    <i class="mdi mdi-tag-outline"></i>
                                    <?php echo htmlspecialchars($program['linea']); ?>
                                </span>
                                <span class="meta-badge">
                                    <i class="mdi mdi-shape-outline"></i>
                                    <?php echo htmlspecialchars($program['categoria']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="programs.php" class="btn-back">
                                <i class="mdi mdi-arrow-left"></i>
                                Volver
                            </a>
                        </div>
                    </div>

                    <!-- Info principal del programa -->
                    <div class="info-card">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Horas Certificadas</span>
                                <span class="info-value">
                                    <i class="mdi mdi-clock-outline"></i>
                                    <?php echo htmlspecialchars($program['certified_hours'] ?? 'N/A'); ?> horas
                                </span>
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
                    <div class="versions-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                Versiones
                                <span class="section-count"><?php echo count($versions); ?></span>
                            </h3>
                                <button type="button" class="btn-primary" onclick="openAddVersionModal()">
                                    <i class="mdi mdi-plus"></i>
                                    Agregar Versión
                                </button>
                        </div>

                        <?php if (empty($versions)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="mdi mdi-package-variant"></i>
                                </div>
                                <div class="empty-title">No hay versiones registradas</div>
                                <div class="empty-text">
                                    Comienza agregando la primera versión de este programa
                                </div>
                                <a href="add_version.php?program_id=<?php echo $program_id; ?>" class="btn-primary">
                                    <i class="mdi mdi-plus"></i>
                                    Crear Primera Versión
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($versions as $version): ?>
                                <div class="version-card" onclick="viewVersion(<?php echo $version['program_version_id']; ?>)">
                                    <div class="version-header">
                                        <div class="version-title-wrapper">
                                            <span class="version-title">
                                                Versión <?php echo htmlspecialchars($version['version_code']); ?>
                                            </span>
                                            <span class="version-status <?php echo $version['active'] == '1' ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $version['active'] == '1' ? 'Activa' : 'Inactiva'; ?>
                                            </span>
                                        </div>
                                        <div class="version-actions">
                                            <button class="btn-icon edit" onclick="editVersion(<?php echo $version['program_version_id']; ?>)" title="Editar">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button class="btn-icon danger" onclick="deleteVersion(<?php echo $version['program_version_id']; ?>)" title="Eliminar">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="version-grid">
                                        <div class="info-item">
                                            <span class="info-label">Sesiones</span>
                                            <span class="info-value">
                                                <i class="mdi mdi-calendar-multiple"></i>
                                                <?php echo htmlspecialchars($version['sessions'] ?? '0'); ?>
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Categoría</span>
                                            <span class="info-value"><?php echo htmlspecialchars($version['categoria_curso'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Modalidad</span>
                                            <span class="info-value"><?php echo htmlspecialchars($version['tipo_modalidad'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Seguimiento</span>
                                            <span class="info-value"><?php echo htmlspecialchars($version['categoria_seguimiento'] ?? 'N/A'); ?></span>
                                        </div>
                                        <?php if (!empty($version['brochure_url'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Brochure</span>
                                            <span class="info-value">
                                                <a href="<?php echo htmlspecialchars($version['brochure_url']); ?>" target="_blank">
                                                    <i class="mdi mdi-file-pdf-box"></i> Ver PDF
                                                </a>
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
                                                <div class="structure-header">
                                                    <i class="mdi mdi-file-tree"></i>
                                                    Estructura del Programa
                                                </div>
                                                <?php foreach ($version_structures as $struct): ?>
                                                    <div class="structure-item">
                                                        <i class="mdi mdi-arrow-right-circle-outline"></i>
                                                        Cursos: <?php echo htmlspecialchars($struct['nombre_curso']); ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <?php include 'modals/program-view/modal_add.php'?>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>

    <script>
        function viewVersion(versionId) {
            window.location.href = `version_detail.php?id=${versionId}`;
        }

        function editVersion(versionId) {
            event.stopPropagation(); // Prevenir que se active el click del card
            window.location.href = `edit_version.php?id=${versionId}`;
        }

        function deleteVersion(versionId) {
            event.stopPropagation(); // Prevenir que se active el click del card
            if (confirm('¿Estás seguro de que deseas eliminar esta versión? Esta acción no se puede deshacer.')) {
                window.location.href = `delete_version.php?id=${versionId}`;
            }
        }
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