<?php
session_start();
require_once 'conexion/conexion.php';

$version_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($version_id <= 0) {
    header('Location: programas.php');
    exit;
}

// Obtener datos completos de la versión
try {
    $sql = "SELECT 
        pv.*,
        p.program_name,
        p.program_id,
        p.cat_type_program,
        c1.description as categoria_curso,
        c2.description as tipo_modalidad,
        c3.description as linea_programa,
        c4.description as categoria_programa,
        c5.description as tipo_programa
    FROM program_versions pv
    INNER JOIN programs p ON pv.program_id = p.program_id
    LEFT JOIN catalog c1 ON pv.cat_category_course = c1.catalog_id
    LEFT JOIN catalog c2 ON pv.cat_type_modality = c2.catalog_id
    LEFT JOIN catalog c3 ON p.cat_category = c3.catalog_id
    LEFT JOIN catalog c4 ON p.cat_type_program = c4.catalog_id
    LEFT JOIN catalog c5 ON p.cat_type_program = c5.catalog_id
    WHERE pv.program_version_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$version_id]);
    $version = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$version) {
        header('Location: programas.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    die("Error al cargar la versión");
}

// Obtener ediciones de esta versión
try {
    $sql = "SELECT 
        pe.*,
        c1.description as categoria_seguimiento,
        c1.variable_2 as seguimiento_detalle,
        c2.description as estado_edicion
    FROM program_editions pe
    LEFT JOIN catalog c1 ON pe.cat_category_following = c1.catalog_id
    LEFT JOIN catalog c2 ON pe.status = c2.catalog_id
    WHERE pe.program_version_id = ?
    ORDER BY pe.edition_number DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$version_id]);
    $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $editions = [];
}

// Verificar si necesita mostrar estructura
$show_structure = in_array($version['cat_type_program'], [542, 543, 544, 545]);

// Obtener estructura si aplica
$structures = [];
if ($show_structure) {
    try {
        $sql = "SELECT 
            pvs.*,
            p.program_name as curso_nombre,
            p.commercial_name as curso_comercial
        FROM program_version_structure pvs
        INNER JOIN programs p ON pvs.child_program_version_id = p.program_id
        WHERE pvs.parent_program_version_id = ?
        ORDER BY pvs.order_position";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$version_id]);
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
    <title>Versión <?php echo htmlspecialchars($version['version_code']); ?></title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/defecto.css">
    <link rel="shortcut icon" href="assets/images/we.png" />

    <style>
        /* RESET MINIMALISTA */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* SISTEMA DE DISEÑO UNIFICADO */
        :root {
            /* Espaciado: solo múltiplos de 8 */
            --space-xs: 8px;
            --space-sm: 16px;
            --space-md: 24px;
            --space-lg: 32px;
            --space-xl: 48px;
            
            /* Tipografía: jerarquía clara */
            --text-xs: 13px;
            --text-sm: 14px;
            --text-base: 15px;
            --text-lg: 18px;
            --text-xl: 28px;
            
            /* Colores: paleta minimalista */
            --color-text: #0a0a0a;
            --color-text-light: #737373;
            --color-border: #e5e5e5;
            --color-bg: #fafafa;
            --color-white: #ffffff;
            
            /* Transiciones: una sola curva */
            --ease: cubic-bezier(0.4, 0, 0.2, 1);
        }

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

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--color-white);
            color: var(--color-text);
            line-height: 1.5;
            font-size: var(--text-base);
        }

        /* LAYOUT */
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

        /* NAVEGACIÓN MINIMALISTA */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: var(--text-xs);
            color: var(--color-text-light);
            margin-bottom: var(--space-lg);
        }

        .breadcrumb a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s var(--ease);
        }

        .breadcrumb a:hover {
            color: var(--color-text);
        }

        .breadcrumb-separator {
            opacity: 0.3;
        }

        /* HEADER: Simplicidad extrema */
        .page-header {
            margin-bottom: var(--space-xl);
        }

        .page-title {
            font-size: var(--text-xl);
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: var(--space-xs);
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--color-text-light);
            font-size: var(--text-base);
            font-weight: 400;
        }

        /* ACTIONS: Solo lo esencial */
        .actions {
            display: flex;
            gap: var(--space-sm);
            margin-top: var(--space-md);
        }

        .btn {
            padding: 10px var(--space-sm);
            font-size: var(--text-sm);
            font-weight: 500;
            border: 1px solid var(--color-border);
            background: var(--color-white);
            color: var(--color-text);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s var(--ease);
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            border-color: var(--color-text);
            transform: translateY(-1px);
        }

        .btn-text {
            border: none;
            padding: 10px 0;
            color: var(--color-text-light);
        }

        .btn-text:hover {
            color: var(--color-text);
            transform: none;
        }

        /* CONTENT SECTIONS: Sin decoración */
        .section {
            margin-bottom: var(--space-xl);
        }

        .section-title {
            font-size: var(--text-lg);
            font-weight: 600;
            margin-bottom: var(--space-md);
            color: var(--color-text);
        }

        /* DATA DISPLAY: Layout fluido */
        .data-row {
            padding: var(--space-sm) 0;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: var(--space-md);
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: var(--color-text-light);
            font-size: var(--text-sm);
        }

        .data-value {
            color: var(--color-text);
            font-weight: 500;
            text-align: right;
        }

        .data-value a {
            color: inherit;
            text-decoration: underline;
            text-decoration-color: var(--color-border);
            transition: text-decoration-color 0.2s var(--ease);
        }

        .data-value a:hover {
            text-decoration-color: var(--color-text);
        }

        /* STRUCTURE: Lista simple */
        .structure-list {
            list-style: none;
        }

        .structure-item {
            padding: var(--space-sm) 0;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: baseline;
            gap: var(--space-sm);
        }

        .structure-item:last-child {
            border-bottom: none;
        }

        .structure-number {
            color: var(--color-text-light);
            font-size: var(--text-xs);
            font-weight: 600;
            min-width: 24px;
        }

        .structure-name {
            flex: 1;
            font-weight: 500;
        }

        .structure-commercial {
            color: var(--color-text-light);
            font-size: var(--text-sm);
        }

        /* EDITIONS: Tarjetas minimalistas */
        .editions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-md);
        }

        .editions-count {
            color: var(--color-text-light);
            font-size: var(--text-sm);
            font-weight: 400;
        }

        .edition-card {
            padding: var(--space-md) 0;
            border-bottom: 1px solid var(--color-border);
            transition: opacity 0.2s var(--ease);
        }

        .edition-card:hover {
            opacity: 0.7;
        }

        .edition-card:last-child {
            border-bottom: none;
        }

        .edition-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-sm);
        }

        .edition-number {
            font-weight: 600;
            font-size: var(--text-base);
        }

        .edition-actions {
            display: flex;
            gap: var(--space-xs);
            opacity: 0;
            transition: opacity 0.2s var(--ease);
        }

        .edition-card:hover .edition-actions {
            opacity: 1;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-border);
            background: var(--color-white);
            border-radius: 6px;
            cursor: pointer;
            color: var(--color-text-light);
            transition: all 0.2s var(--ease);
        }

        .btn-icon:hover {
            border-color: var(--color-text);
            color: var(--color-text);
        }

        .edition-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-sm);
        }

        .edition-field {
            font-size: var(--text-sm);
        }

        .edition-field-label {
            color: var(--color-text-light);
            display: block;
            margin-bottom: 2px;
        }

        .edition-field-value {
            color: var(--color-text);
        }

        /* EMPTY STATE: Ultra simple */
        .empty {
            padding: var(--space-xl) 0;
            text-align: center;
            color: var(--color-text-light);
            font-size: var(--text-sm);
        }

        /* STATUS: Sin badges coloridos */
        .status {
            display: inline-block;
            font-size: var(--text-xs);
            color: var(--color-text-light);
        }

        .status-active {
            color: var(--color-text);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: var(--space-md) var(--space-sm);
            }

            .page-title {
                font-size: 24px;
            }

            .data-row {
                flex-direction: column;
                gap: var(--space-xs);
            }

            .data-value {
                text-align: left;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .edition-data {
                grid-template-columns: 1fr;
            }
        }

        /* DIVIDER: Separación sutil */
        .divider {
            height: 1px;
            background: var(--color-border);
            margin: var(--space-xl) 0;
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
                    
                    <!-- Navegación -->
                    <nav class="breadcrumb">
                        <a href="programas.php">Programas</a>
                        <span class="breadcrumb-separator">/</span>
                        <a href="program-view.php?id=<?php echo $version['program_id']; ?>">
                            <?php echo htmlspecialchars($version['program_name']); ?>
                        </a>
                        <span class="breadcrumb-separator">/</span>
                        <span><?php echo htmlspecialchars($version['version_code']); ?></span>
                    </nav>

                    <!-- Header -->
                    <header class="page-header">
                        <h1 class="page-title">
                            <?php echo htmlspecialchars($version['program_name']); ?>
                                                    <p class="page-subtitle">
                            Versión <?php echo htmlspecialchars($version['version_code']); ?>
                        </p>
                        </h1>

                        <div class="actions">
                            <a href="program-view.php?id=<?php echo $version['program_id']; ?>" class="btn-text">
                                ← Volver
                            </a>
                            <button class="btn" onclick="editVersion()">
                                Editar
                            </button>
                            <button class="btn" onclick="deleteVersion()">
                                Eliminar
                            </button>
                        </div>
                    </header>

                    <!-- Información General -->
                    <div class="info-card">
                        <section class="section">
                            <h2 class="section-title">Información General</h2>
                            
                            <div class="data-row">
                                <span class="data-label">Código</span>
                                <span class="data-value"><?php echo htmlspecialchars($version['version_code']); ?></span>
                            </div>
                            
                            <div class="data-row">
                                <span class="data-label">Sesiones</span>
                                <span class="data-value"><?php echo htmlspecialchars($version['sessions'] ?? '0'); ?></span>
                            </div>
                            
                            <div class="data-row">
                                <span class="data-label">Categoría</span>
                                <span class="data-value"><?php echo htmlspecialchars($version['categoria_curso'] ?? 'N/A'); ?></span>
                            </div>
                            
                            <div class="data-row">
                                <span class="data-label">Modalidad</span>
                                <span class="data-value"><?php echo htmlspecialchars($version['tipo_modalidad'] ?? 'N/A'); ?></span>
                            </div>
                            
                            <div class="data-row">
                                <span class="data-label">Estado</span>
                                <span class="data-value">
                                    <span class="status <?php echo $version['active'] == '1' ? 'status-active' : ''; ?>">
                                        <?php echo $version['active'] == '1' ? 'Activa' : 'Inactiva'; ?>
                                    </span>
                                </span>
                            </div>
                            
                            <?php if (!empty($version['brochure_url'])): ?>
                            <div class="data-row">
                                <span class="data-label">Brochure</span>
                                <span class="data-value">
                                    <a href="<?php echo htmlspecialchars($version['brochure_url']); ?>" target="_blank">
                                        Ver documento
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </section>
                    </div>

                    <!-- Estructura del Programa -->
                    <?php if ($show_structure && !empty($structures)): ?>
                    <div class="divider"></div>
                    
                    <section class="section">
                        <h2 class="section-title">Estructura (<?php echo count($structures); ?>)</h2>
                        
                        <ul class="structure-list">
                            <?php foreach ($structures as $index => $struct): ?>
                            <li class="structure-item">
                                <span class="structure-number"><?php echo $index + 1; ?></span>
                                <span class="structure-name">
                                    <?php echo htmlspecialchars($struct['curso_nombre']); ?>
                                </span>
                                <?php if (!empty($struct['curso_comercial'])): ?>
                                <span class="structure-commercial">
                                    <?php echo htmlspecialchars($struct['curso_comercial']); ?>
                                </span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                    <?php endif; ?>

                    <!-- Ediciones -->
                    <div class="divider"></div>
                    
                    <section class="section">
                        <div class="editions-header">
                            <h2 class="section-title" style="margin-bottom: 0;">
                                Ediciones
                                <span class="editions-count">(<?php echo count($editions); ?>)</span>
                            </h2>
                            <button class="btn" onclick="addEdition()">
                                Nueva Edición
                            </button>
                        </div>

                        <?php if (empty($editions)): ?>
                        <div class="empty">
                            No hay ediciones registradas
                        </div>
                        <?php else: ?>
                            <?php foreach ($editions as $edition): ?>
                            <article class="edition-card">
                                <div class="edition-header">
                                    <h3 class="edition-number">
                                        Edición #<?php echo htmlspecialchars($edition['edition_number']); ?>
                                    </h3>
                                    <div class="edition-actions">
                                        <button class="btn-icon" onclick="editEdition(<?php echo $edition['program_edition_id']; ?>)" title="Editar">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="btn-icon" onclick="deleteEdition(<?php echo $edition['program_edition_id']; ?>)" title="Eliminar">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="edition-data">
                                    <?php if (!empty($edition['categoria_seguimiento'])): ?>
                                    <div class="edition-field">
                                        <span class="edition-field-label">Seguimiento</span>
                                        <span class="edition-field-value">
                                            <?php echo htmlspecialchars($edition['categoria_seguimiento']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($edition['seguimiento_detalle'])): ?>
                                    <div class="edition-field">
                                        <span class="edition-field-label">Detalle</span>
                                        <span class="edition-field-value">
                                            <?php echo htmlspecialchars($edition['seguimiento_detalle']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($edition['estado_edicion'])): ?>
                                    <div class="edition-field">
                                        <span class="edition-field-label">Estado</span>
                                        <span class="edition-field-value">
                                            <?php echo htmlspecialchars($edition['estado_edicion']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </section>

                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/misc.js"></script>

    <script>
        // Ocultar sidebar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-minimal');
            const mainPanel = document.querySelector('.main-panel');
            
            if (sidebar && mainPanel) {
                sidebar.classList.add('collapsed');
                mainPanel.classList.add('expanded');
            }
        });

        function editVersion() {
            window.location.href = 'edit_version.php?id=<?php echo $version_id; ?>';
        }

        function deleteVersion() {
            if (confirm('¿Eliminar esta versión y todas sus ediciones?')) {
                window.location.href = 'delete_version.php?id=<?php echo $version_id; ?>';
            }
        }

        function addEdition() {
            window.location.href = 'add_edition.php?version_id=<?php echo $version_id; ?>';
        }

        function editEdition(editionId) {
            window.location.href = 'edit_edition.php?id=' + editionId;
        }

        function deleteEdition(editionId) {
            if (confirm('¿Eliminar esta edición?')) {
                window.location.href = 'delete_edition.php?id=' + editionId;
            }
        }
    </script>
</body>
</html>