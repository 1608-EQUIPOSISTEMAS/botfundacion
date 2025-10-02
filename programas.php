<?php
session_start();
require_once 'conexion/conexion.php';

// [Tu código PHP original de permisos y datos permanece igual]
$user_role = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : null;
$user_permissions = [];
$role_name = 'Sin Rol';
$role_class = 'default';

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
        $user_permissions = ['members'];
        $role_name = 'Comercial';
        $role_class = 'comercial';
        break;
    default:
        $user_permissions = [];
        $role_name = 'Sin Permisos';
        $role_class = 'default';
}

$programs_data = [];
try {
    $sql = "SELECT p.program_id, p.program_name, p.active, c.description as linea, p.certified_hours, pv.sessions as secciones, c2.description as categoria, pv.version_code from
programs p
inner join catalog c on p.cat_category = c.catalog_id
inner join catalog c2 on p.cat_type_program = c2.catalog_id 
inner join program_versions pv on p.program_id = pv.program_id and pv.program_version_id = (select MAX(pv2.program_version_id) from program_versions pv2 where pv2.program_id = p.program_id)
where p.active = 1
ORDER BY p.program_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $programs_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener programas: " . $e->getMessage());
    $programs_data = [];
}

$total_programs = count($programs_data);
$active_programs = count(array_filter($programs_data, fn($p) => $p['active'] === '1'));
$inactive_programs = $total_programs - $active_programs;
$total_lineas = count(array_unique(array_column($programs_data, 'linea')));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>W|E - Programas</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">

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

        /* Layout principal corregido */
        .main-panel {
            margin-left: 260px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            background: #fafafa;
        }

        .content-wrapper {
            padding: 40px 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header de página */
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

        /* Grid de métricas - 4 columnas iguales */
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

        .metric-icon.icon-total {
            background: #f0f9ff;
        }

        .metric-icon.icon-total i {
            color: #0284c7;
        }

        .metric-icon.icon-active {
            background: #f0fdf4;
        }

        .metric-icon.icon-active i {
            color: #22c55e;
        }

        .metric-icon.icon-inactive {
            background: #fef2f2;
        }

        .metric-icon.icon-inactive i {
            color: #ef4444;
        }

        .metric-icon.icon-lineas {
            background: #faf5ff;
        }

        .metric-icon.icon-lineas i {
            color: #a855f7;
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

        /* Barra de acciones */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            gap: 20px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 420px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            font-size: 15px;
            background: #fff;
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #a3a3a3;
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

        /* Filtros */
        .filters-container {
            margin-bottom: 32px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 600;
            color: #737373;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 8px 18px;
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            background: #fff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #525252;
        }

        .filter-pill:hover {
            border-color: #1a1a1a;
            background: #fafafa;
        }

        .filter-pill.active {
            border-color: #1a1a1a;
            background: #1a1a1a;
            color: #fff;
        }

        /* Grid de programas - 3 columnas */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .program-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .program-card:hover {
            border-color: #1a1a1a;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .program-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .program-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .program-category {
            font-size: 13px;
            color: #737373;
            margin-bottom: 4px;
        }

        .program-line {
            font-size: 13px;
            color: #a3a3a3;
        }

        .program-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .status-active {
            background: #22c55e;
        }

        .status-inactive {
            background: #ef4444;
        }

        /* Stats */
        .program-stats {
            display: flex;
            gap: 20px;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #f5f5f5;
            font-size: 13px;
            color: #737373;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-item i {
            font-size: 16px;
            color: #a3a3a3;
        }

        .stat-value {
            font-weight: 600;
            color: #1a1a1a;
            margin-right: 2px;
        }

        /* Acciones */
        .program-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            opacity: 0;
            transition: opacity 0.2s;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f5f5f5;
        }

        .program-card:hover .program-actions {
            opacity: 1;
        }

        .btn-action {
            padding: 8px 12px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            background: #fff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #525252;
        }

        .btn-action:hover {
            border-color: #1a1a1a;
            background: #fafafa;
            transform: translateY(-1px);
        }

        .btn-view:hover {
            border-color: #0066ff;
            color: #0066ff;
            background: #f0f4ff;
        }

        .btn-edit:hover {
            border-color: #eab308;
            color: #eab308;
            background: #fef9e7;
        }

        .btn-delete:hover {
            border-color: #ef4444;
            color: #dc2626;
            background: #fef2f2;
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
        @media (max-width: 1400px) {
            .programs-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .main-panel {
                margin-left: 0;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 24px 16px;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: 100%;
            }

            .programs-grid {
                grid-template-columns: 1fr;
            }

            .program-actions {
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
                        <h1>Portafolio de Producto</h1>
                        <p>Administra y organiza todos los programas del sistema</p>
                    </div>

                    <?php if (empty($programs_data)): ?>
                        <!-- Estado vacío -->
                        <div class="empty-state">
                            <div class="empty-state-icon">📚</div>
                            <h2 class="empty-state-title">No hay programas</h2>
                            <p class="empty-state-text">Crea tu primer programa para comenzar</p>
                            <button class="btn-new" onclick="openAddProgramModal()">
                                <i class="mdi mdi-plus"></i>
                                Nuevo programa
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Métricas -->
                        <div class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-total">
                                        <i class="mdi mdi-folder-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value" id="metricTotal"><?php echo $total_programs; ?></div>
                                <div class="metric-label">Total de programas</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-active">
                                        <i class="mdi mdi-check-circle-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value" id="metricActive"><?php echo $active_programs; ?></div>
                                <div class="metric-label">Programas activos</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-inactive">
                                        <i class="mdi mdi-close-circle-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value" id="metricInactive"><?php echo $inactive_programs; ?></div>
                                <div class="metric-label">Programas inactivos</div>
                            </div>
                            
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-icon icon-lineas">
                                        <i class="mdi mdi-shape-outline"></i>
                                    </div>
                                </div>
                                <div class="metric-value" id="metricLineas"><?php echo $total_lineas; ?></div>
                                <div class="metric-label">Líneas de negocio</div>
                            </div>
                        </div>

                        <!-- Barra de acciones -->
                        <div class="actions-bar">
                            <div class="search-box">
                                <i class="mdi mdi-magnify"></i>
                                <input type="text" id="searchInput" placeholder="Buscar programas...">
                            </div>
                            <button class="btn-new" onclick="openAddProgramModal()">
                                <i class="mdi mdi-plus"></i>
                                Nuevo programa
                            </button>
                        </div>

                        <!-- Filtros -->
                        <div class="filters-container">
                            <div class="filter-group">
                                <div class="filter-label">Línea de negocio</div>
                                <div class="filter-pills" id="lineFilters">
                                    <button class="filter-pill active" data-type="linea" data-value="all">Todas</button>
                                    <?php 
                                    $lineas = array_unique(array_column($programs_data, 'linea'));
                                    foreach ($lineas as $linea): 
                                    ?>
                                        <button class="filter-pill" data-type="linea" data-value="<?php echo htmlspecialchars($linea); ?>">
                                            <?php echo htmlspecialchars($linea); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="filter-group">
                                <div class="filter-label">Categoría</div>
                                <div class="filter-pills" id="categoryFilters">
                                    <button class="filter-pill active" data-type="categoria" data-value="all">Todas</button>
                                    <?php 
                                    $categorias = array_unique(array_column($programs_data, 'categoria'));
                                    foreach ($categorias as $cat): 
                                    ?>
                                        <button class="filter-pill" data-type="categoria" data-value="<?php echo htmlspecialchars($cat); ?>">
                                            <?php echo htmlspecialchars($cat); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Grid de programas -->
                        <div class="programs-grid" id="programsGrid">
                            <?php foreach ($programs_data as $row): ?>
                                <div class="program-card" 
                                    data-linea="<?php echo htmlspecialchars($row['linea']); ?>"
                                    data-categoria="<?php echo htmlspecialchars($row['categoria']); ?>"
                                    data-name="<?php echo htmlspecialchars(strtolower($row['program_name'])); ?>">
                                    
                                    <div class="program-card-header">
                                        <div style="flex: 1;">
                                            <h3 class="program-title"><?php echo htmlspecialchars($row['program_name']); ?></h3>
                                            <p class="program-category"><?php echo htmlspecialchars($row['categoria']); ?></p>
                                            <p class="program-line"><?php echo htmlspecialchars($row['linea']); ?></p>
                                        </div>
                                        <div class="program-status <?php echo $row['active'] === '1' ? 'status-active' : 'status-inactive'; ?>"></div>
                                    </div>

                                    <div class="program-stats">
                                        <div class="stat-item">
                                            <i class="mdi mdi-format-list-bulleted"></i>
                                            <span><span class="stat-value"><?php echo htmlspecialchars($row['secciones'] ?? '0'); ?></span> sesiones</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="mdi mdi-clock-outline"></i>
                                            <span><span class="stat-value"><?php echo htmlspecialchars($row['certified_hours'] ?? '0'); ?></span> horas</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="mdi mdi-tag-outline"></i>
                                            <span class="stat-value"><?php echo htmlspecialchars($row['version_code'] ?? 'V1.0'); ?></span>
                                        </div>
                                    </div>

                                    <div class="program-actions">
                                        <button class="btn-action btn-view" onclick="viewProgram(<?php echo $row['program_id']; ?>)">
                                            Ver
                                        </button>
                                        <?php if(tienePermiso('producto') || tienePermiso('all')): ?>
                                        <button class="btn-action btn-edit" onclick="editProgram(<?php echo $row['program_id']; ?>)">
                                            Editar
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteProgram(<?php echo $row['program_id']; ?>)">
                                            Eliminar
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <script>
        let activeFilters = {
            linea: 'all',
            categoria: 'all',
            search: ''
        };

        // Búsqueda
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                activeFilters.search = e.target.value.toLowerCase();
                applyFilters();
            });
        }

        // Filtros de línea
        const lineFilters = document.querySelectorAll('#lineFilters .filter-pill');
        lineFilters.forEach(pill => {
            pill.addEventListener('click', function() {
                lineFilters.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                activeFilters.linea = this.dataset.value;
                applyFilters();
            });
        });

        // Filtros de categoría
        const categoryFilters = document.querySelectorAll('#categoryFilters .filter-pill');
        categoryFilters.forEach(pill => {
            pill.addEventListener('click', function() {
                categoryFilters.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                activeFilters.categoria = this.dataset.value;
                applyFilters();
            });
        });

        function applyFilters() {
            const cards = document.querySelectorAll('.program-card');
            
            cards.forEach(card => {
                const matchLinea = activeFilters.linea === 'all' || card.dataset.linea === activeFilters.linea;
                const matchCategoria = activeFilters.categoria === 'all' || card.dataset.categoria === activeFilters.categoria;
                const matchSearch = activeFilters.search === '' || card.dataset.name.includes(activeFilters.search);
                
                card.style.display = (matchLinea && matchCategoria && matchSearch) ? 'flex' : 'none';
            });
            
            updateMetrics();
        }

        function updateMetrics() {
            const visibleCards = Array.from(document.querySelectorAll('.program-card'))
                .filter(card => card.style.display !== 'none');
            
            const totalVisible = visibleCards.length;
            const activeVisible = visibleCards.filter(card => card.querySelector('.status-active')).length;
            const inactiveVisible = totalVisible - activeVisible;
            const uniqueLineas = new Set(visibleCards.map(card => card.dataset.linea));
            
            document.getElementById('metricTotal').textContent = totalVisible;
            document.getElementById('metricActive').textContent = activeVisible;
            document.getElementById('metricInactive').textContent = inactiveVisible;
            document.getElementById('metricLineas').textContent = uniqueLineas.size;
        }

        function openAddProgramModal() {
            Swal.fire({
                title: 'Nuevo programa',
                html: '<input id="programName" class="swal2-input" placeholder="Nombre del programa">',
                showCancelButton: true,
                confirmButtonText: 'Crear',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1a1a1a',
                cancelButtonColor: '#737373'
            });
        }

        function viewProgram(id) {
            window.location.href = 'program-detail.php?id=' + id;
        }

        function editProgram(id) {
            window.location.href = 'program-edit.php?id=' + id;
        }

        function deleteProgram(id) {
            Swal.fire({
                title: '¿Eliminar programa?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#737373',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría tu lógica AJAX para eliminar
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'El programa ha sido eliminado',
                        icon: 'success',
                        confirmButtonColor: '#1a1a1a'
                    });
                }
            });
        }
    </script>
</body>
</html>