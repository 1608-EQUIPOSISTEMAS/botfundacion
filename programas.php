<?php
session_start();
require_once 'conexion/conexion.php';

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
        $user_permissions = ['members'];
        $role_name = 'Comercial';
        $role_class = 'comercial';
        break;
    default:
        $user_permissions = [];
        $role_name = 'Sin Permisos';
        $role_class = 'default';
}

// Obtener los datos de la tabla programs
$programs_data = [];
try {
    $sql = "SELECT p.program_id, p.program_name, p.active, c.description as categoria, p.certified_hours, pv.sessions as secciones from
programs p
inner join catalog c on p.cat_category = c.catalog_id
inner join program_versions pv on p.program_id  = pv.program_id and pv.program_version_id = (select MAX(pv2.program_version_id) from program_versions pv2 where pv2.program_id = p.program_id)
where p.active = 1
ORDER BY p.program_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $programs_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener programas: " . $e->getMessage());
    $programs_data = [];
}

// Calcular métricas
$total_programs = count($programs_data);
$active_programs = count(array_filter($programs_data, fn($p) => $p['active'] === '1'));
$inactive_programs = $total_programs - $active_programs;
$total_categories = count(array_unique(array_column($programs_data, 'categoria')));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connect Plus - Programas</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafafa;
            color: #1a1a1a;
        }

        .content-wrapper {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header minimalista */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 28px;
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

        /* Cards de métricas minimalistas */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
        }

        .metric-card:hover {
            border-color: #d4d4d4;
        }

        .metric-icon {
            font-size: 24px;
            color: #737373;
            margin-bottom: 12px;
            line-height: 1;
        }

        .metric-icon.icon-active {
            color: #22c55e;
        }

        .metric-icon.icon-inactive {
            color: #ef4444;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
            line-height: 1;
        }

        .metric-label {
            font-size: 13px;
            color: #737373;
            font-weight: 400;
        font-weight: 400;
        }

        .metric-indicator {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .indicator-active {
            background: #22c55e;
        }

        .indicator-inactive {
            background: #ef4444;
        }

        .indicator-neutral {
            background: #737373;
        }

        /* Barra de acciones */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 15px;
            background: #fff;
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #1a1a1a;
        }

        .search-box::before {
            content: "🔍";
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.5;
        }

        .btn-new {
            padding: 12px 24px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-new:hover {
            background: #000;
            transform: translateY(-1px);
        }

        /* Grid de programas */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .program-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s;
            position: relative;
            cursor: pointer;
        }

        .program-card:hover {
            border-color: #1a1a1a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .program-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .program-title {
            font-size: 17px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .program-category {
            font-size: 13px;
            color: #737373;
        }

        /* Horas certificadas */
        .program-hours {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #737373;
            margin-top: 12px;
            padding: 8px 12px;
            background: #fafafa;
            border-radius: 6px;
            width: fit-content;
        }

        .program-hours i {
            font-size: 16px;
        }

        .program-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-active {
            background: #22c55e;
        }

        .status-inactive {
            background: #ef4444;
        }

        .program-actions {
            display: flex;
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

        /* Información del programa */
        .program-info {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f5f5f5;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            padding: 8px;
            background: #fafafa;
            border-radius: 6px;
        }

        .info-icon {
            font-size: 18px;
            line-height: 1;
        }

        .info-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1;
        }

        .info-label {
            font-size: 11px;
            color: #737373;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-action {
            flex: 1;
            padding: 8px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            background: #fff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #1a1a1a;
        }

        .btn-action:hover {
            border-color: #1a1a1a;
            background: #fafafa;
        }

        .btn-delete:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .btn-edit:hover {
            border-color: #bde331ff;
            color: #bde331ff;
            background: #fef2f2;
        }

        .btn-view:hover {
            border-color: #5a6de6ff;
            color: #5a6de6ff;
            background: #fef2f2;
        }

        /* Estado vacío ultra minimalista */
        .empty-state {
            text-align: center;
            padding: 120px 20px;
        }

        .empty-state-title {
            font-size: 20px;
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
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-bar {
                flex-direction: column;
            }

            .search-box {
                max-width: 100%;
            }

            .programs-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Filtro de categorías minimalista */
        .filter-pills {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 8px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            background: #fff;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-pill:hover,
        .filter-pill.active {
            border-color: #1a1a1a;
            background: #1a1a1a;
            color: #fff;
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
                        <p>Administra los programas disponibles en el sistema</p>
                    </div>

                    <?php if (empty($programs_data)): ?>
                        <!-- Estado vacío -->
                        <div class="empty-state">
                            <h2 class="empty-state-title">No hay programas</h2>
                            <p class="empty-state-text">Crea tu primer programa para comenzar</p>
                            <button class="btn-new" onclick="openAddProgramModal()">
                                Nuevo programa
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Cards de métricas -->
                        <div class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-icon">
                                    <i class="mdi mdi-folder-outline"></i>
                                </div>
                                <div class="metric-value" id="metricTotal"><?php echo $total_programs; ?></div>
                                <div class="metric-label">Total de programas</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-icon icon-active">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                </div>
                                <div class="metric-value" id="metricActive"><?php echo $active_programs; ?></div>
                                <div class="metric-label">Programas activos</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-icon icon-inactive">
                                    <i class="mdi mdi-close-circle-outline"></i>
                                </div>
                                <div class="metric-value" id="metricInactive"><?php echo $inactive_programs; ?></div>
                                <div class="metric-label">Programas inactivos</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-icon">
                                    <i class="mdi mdi-shape-outline"></i>
                                </div>
                                <div class="metric-value" id="metricCategories"><?php echo $total_categories; ?></div>
                                <div class="metric-label">Categorías</div>
                            </div>
                        </div>

                        <!-- Barra de acciones -->
                        <div class="actions-bar">
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Buscar programas...">
                            </div>
                            <button class="btn-new" onclick="openAddProgramModal()">
                                Nuevo programa
                            </button>
                        </div>

                        <!-- Filtros de categoría -->
                        <div class="filter-pills">
                            <button class="filter-pill active" data-category="all">Todos</button>
                            <?php 
                            $categories = array_unique(array_column($programs_data, 'categoria'));
                            foreach ($categories as $cat): 
                            ?>
                                <button class="filter-pill" data-category="<?php echo htmlspecialchars($cat); ?>">
                                    <?php echo htmlspecialchars($cat); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Grid de programas -->
                        <div class="programs-grid" id="programsGrid">
                            <?php foreach ($programs_data as $row): ?>
                                <div class="program-card" 
                                    data-category="<?php echo htmlspecialchars($row['categoria']); ?>"
                                    data-name="<?php echo htmlspecialchars(strtolower($row['program_name'])); ?>">
                                    <div class="program-card-header">
                                        <div>
                                            <h3 class="program-title"><?php echo htmlspecialchars($row['program_name']); ?></h3>
                                            <p class="program-category"><?php echo htmlspecialchars($row['categoria']); ?></p>
                                        </div>
                                        <div class="program-status <?php echo $row['active'] === '1' ? 'status-active' : 'status-inactive'; ?>"></div>
                                    </div>

                                    <!-- Nueva sección de información -->
                                    <div class="program-info">
                                        <div class="info-item">
                                            <span class="info-icon"><i class="mdi mdi-format-list-bulleted"></i></span>
                                            <div class="info-content">
                                                <span class="info-value"><?php echo htmlspecialchars($row['secciones'] ?? '0'); ?></span>
                                                <span class="info-label">Sesiones</span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-icon"><i class="mdi mdi-clock-outline"></i></span>
                                            <div class="info-content">
                                                <span class="info-value"><?php echo htmlspecialchars($row['certified_hours'] ?? '0'); ?></span>
                                                <span class="info-label">Horas</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="program-actions">
                                        <button class="btn-action btn-view" onclick="viewProgram(<?php echo $row['program_id']; ?>)">
                                            Ver
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editProgram(<?php echo $row['program_id']; ?>)">
                                            Editar
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteProgram(<?php echo $row['program_id']; ?>)">
                                            Eliminar
                                        </button>
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
        // Búsqueda en tiempo real
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.program-card');
                
                cards.forEach(card => {
                    const name = card.dataset.name;
                    if (name.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                updateMetrics();
            });
        }

        // Filtro de categorías con actualización de métricas
        const filterPills = document.querySelectorAll('.filter-pill');
        filterPills.forEach(pill => {
            pill.addEventListener('click', function() {
                filterPills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                const cards = document.querySelectorAll('.program-card');
                
                cards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                updateMetrics();
            });
        });

        // Función para actualizar las métricas dinámicamente
        function updateMetrics() {
            const visibleCards = Array.from(document.querySelectorAll('.program-card'))
                .filter(card => card.style.display !== 'none');
            
            const totalVisible = visibleCards.length;
            const activeVisible = visibleCards.filter(card => 
                card.querySelector('.status-active')
            ).length;
            const inactiveVisible = totalVisible - activeVisible;
            
            // Contar categorías únicas de los programas visibles
            const uniqueCategories = new Set(
                visibleCards.map(card => card.dataset.category)
            );
            const categoriesCount = uniqueCategories.size;
            
            // Actualizar los valores en las métricas con animación
            animateValue('metricTotal', totalVisible);
            animateValue('metricActive', activeVisible);
            animateValue('metricInactive', inactiveVisible);
            animateValue('metricCategories', categoriesCount);
        }

        // Función para animar el cambio de valores
        function animateValue(elementId, newValue) {
            const element = document.getElementById(elementId);
            const currentValue = parseInt(element.textContent) || 0;
            
            if (currentValue === newValue) return;
            
            const duration = 300;
            const stepTime = 20;
            const steps = duration / stepTime;
            const increment = (newValue - currentValue) / steps;
            let current = currentValue;
            let step = 0;
            
            const timer = setInterval(() => {
                step++;
                current += increment;
                
                if (step >= steps) {
                    element.textContent = newValue;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.round(current);
                }
            }, stepTime);
        }

        function openAddProgramModal() {
            Swal.fire({
                title: 'Nuevo programa',
                html: `
                    <input id="programName" class="swal2-input" placeholder="Nombre del programa">
                    <select id="programCategory" class="swal2-input">
                        <option value="">Selecciona categoría</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'Crear',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1a1a1a',
                cancelButtonColor: '#737373'
            });
        }

        function viewProgram(id) {
            console.log('Ver programa:', id);
        }

        function editProgram(id) {
            Swal.fire({
                title: 'Editar programa',
                text: 'ID: ' + id,
                icon: 'info',
                confirmButtonColor: '#1a1a1a'
            });
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