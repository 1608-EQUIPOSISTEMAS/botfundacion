<?php
function tienePermiso($permiso) {
    if (!isset($_SESSION['permisos_asignados'])) {
        return false;
    }
    
    $permisos = $_SESSION['permisos_asignados'];
    
    if (in_array('all', $permisos)) {
        return true;
    }
    
    return in_array($permiso, $permisos);
}

function tieneAlgunPermiso($permisos) {
    foreach ($permisos as $permiso) {
        if (tienePermiso($permiso)) {
            return true;
        }
    }
    return false;
}

function tieneTodosPermisos($permisos) {
    foreach ($permisos as $permiso) {
        if (!tienePermiso($permiso)) {
            return false;
        }
    }
    return true;
}

function tieneRol($rol) {
    return isset($_SESSION['rol_nombre']) && $_SESSION['rol_nombre'] === $rol;
}
?>

<!-- ESTILOS MINIMALISTAS -->
<style>
    :root {
        --sidebar-bg: #ffffff;
        --sidebar-width: 260px;
        --text-primary: #1a1a1a;
        --text-secondary: #666666;
        --accent-color: #0066ff;
        --hover-bg: #f5f7fa;
        --border-color: #e8eaed;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-minimal {
        position: fixed;
        left: 0;
        top: 60px;
        width: 260px;
        height: calc(100vh - 60px);
        background: #ffffff;
        border-right: 1px solid #e8eaed;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0;
        z-index: 100;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(0);
    }

    .sidebar-minimal::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-minimal::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }

    .sidebar-minimal .nav {
        padding: 16px 0;
    }

    .sidebar-minimal .nav-section {
        padding: 24px 20px 8px 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
    }

    .sidebar-minimal .nav-item {
        margin: 2px 12px;
    }

    .sidebar-minimal .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 500;
        border-radius: 8px;
        transition: var(--transition);
        text-decoration: none;
        position: relative;
    }

    .sidebar-minimal .nav-link:hover {
        background: var(--hover-bg);
        color: var(--accent-color);
    }

    .sidebar-minimal .nav-link.active {
        background: var(--hover-bg);
        color: var(--accent-color);
    }

    .sidebar-minimal .nav-link i {
        font-size: 18px;
        width: 20px;
        margin-right: 12px;
        opacity: 0.8;
    }

    .sidebar-minimal .nav-link:hover i {
        opacity: 1;
    }

    /* Dropdowns minimalistas */
    .sidebar-minimal .nav-dropdown {
        margin: 2px 12px;
    }

    .sidebar-minimal .nav-dropdown > .nav-link {
        padding: 10px 12px;
    }

    .sidebar-minimal .nav-dropdown > .nav-link::after {
        content: '';
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%) rotate(0deg);
        width: 6px;
        height: 6px;
        border-right: 1.5px solid currentColor;
        border-bottom: 1.5px solid currentColor;
        transition: var(--transition);
    }

    .sidebar-minimal .nav-dropdown.active > .nav-link::after {
        transform: translateY(-50%) rotate(45deg);
    }

    .sidebar-minimal .nav-dropdown-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding: 0;
    }

    .sidebar-minimal .nav-dropdown.active .nav-dropdown-menu {
        max-height: 500px;
        padding: 4px 0;
    }

    .sidebar-minimal .nav-dropdown-item {
        padding: 8px 12px 8px 44px;
        color: var(--text-secondary);
        font-size: 13px;
        display: block;
        border-radius: 6px;
        transition: var(--transition);
        text-decoration: none;
    }

    .sidebar-minimal .nav-dropdown-item:hover {
        color: var(--accent-color);
        background: var(--hover-bg);
    }

    /* Logout especial */
    .sidebar-minimal .nav-logout {
        margin: 24px 12px 12px 12px;
        border-top: 1px solid var(--border-color);
        padding-top: 16px;
    }

    .sidebar-minimal .nav-logout .nav-link {
        color: #dc3545;
        font-weight: 600;
    }

    .sidebar-minimal .nav-logout .nav-link:hover {
        background: #fff5f5;
    }

    /* Badge para contadores */
    .nav-badge {
        margin-left: auto;
        background: var(--accent-color);
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }

    .me-3 {
        margin-right: 1rem; 
    }
</style>

<!-- SIDEBAR MINIMALISTA -->
<nav class="sidebar-minimal" id="sidebar">
    <ul class="nav flex-column">
        
        <?php if(tienePermiso('dashboard') || tienePermiso('all')): ?>
        <!-- SECCIÓN PRINCIPAL -->
        <li class="nav-section">Principal</li>
        <li class="nav-item">
            <a class="nav-link active" href="dashboard.php">
                <i class="mdi mdi-view-dashboard"></i>
                <span>Inicio</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(tienePermiso('members') || tienePermiso('all')): ?>
        <li class="nav-item">
            <a class="nav-link" href="bot.php">
                <i class="mdi mdi-robot"></i>
                <span>Bot Principal</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="programs.php">
                <i class="mdi mdi-book-open-page-variant"></i>
                <span>Programas</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- SECCIÓN FUNDACIÓN -->
        <?php if(tienePermiso('fundacion') || tienePermiso('all')): ?>
        <li class="nav-section">Fundación</li>
        
        <li class="nav-item">
            <a class="nav-link" href="bot2.php">
                <i class="mdi mdi-circle-outline"></i>
                <span>Primera Línea</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="bot3.php">
                <i class="mdi mdi-circle-outline"></i>
                <span>Segunda Línea</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="bot4.php">
                <i class="mdi mdi-circle-outline"></i>
                <span>Tercera Línea</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="fundacion.php">
                <i class="mdi mdi-heart-outline"></i>
                <span>Fundación</span>
            </a>
        </li>

        <li class="nav-dropdown">
            <a class="nav-link" href="#" onclick="toggleDropdown(this); return false;">
                <i class="mdi mdi-file-chart"></i>
                <span>Reportes</span>
            </a>
            <div class="nav-dropdown-menu">
                <a class="nav-dropdown-item" href="información.php">Pagos</a>
                <a class="nav-dropdown-item" href="dashboardinfo.php">Dashboard</a>
            </div>
        </li>
        <?php endif; ?>

        <!-- SECCIÓN INMOBILIARIA -->
        <?php if(tienePermiso('inmobiliaria') || tienePermiso('all')): ?>
        <li class="nav-section">Inmobiliaria</li>

        <li class="nav-item">
            <a class="nav-link" href="bot_realestate.php">
                <i class="mdi mdi-circle-outline"></i>
                <span>Primera Línea</span>
            </a>
        </li>

        
        <li class="nav-item">
            <a class="nav-link" href="bot_realestate2.php">
                <i class="mdi mdi-circle-outline"></i>
                <span>Segunda Línea</span>
            </a>
        </li>

        <li class="nav-dropdown">
            <a class="nav-link" href="#" onclick="toggleDropdown(this); return false;">
                <i class="mdi mdi-file-chart"></i>
                <span>Inmobiliaria</span>
            </a>
            <div class="nav-dropdown-menu">
                <a class="nav-dropdown-item" href="dashboard_realestate.php">Analisis</a>
                <a class="nav-dropdown-item" href="bloqueados_realestate.php">Bloqueados</a>
            </div>
        </li>
        <?php endif; ?>

        <!-- SECCIÓN MEMBERS -->
        <?php if(tienePermiso('members') || tienePermiso('all')): ?>
        <li class="nav-section">Members</li>
        
        <li class="nav-dropdown">
            <a class="nav-link" href="#" onclick="toggleDropdown(this); return false;">
                <i class="mdi mdi-account-group"></i>
                <span>Membresías</span>
            </a>
            <div class="nav-dropdown-menu">
                <a class="nav-dropdown-item" href="member_black.php">Black</a>
                <a class="nav-dropdown-item" href="member_gold.php">Gold</a>
                <a class="nav-dropdown-item" href="member_platinum.php">Platinum</a>
                <a class="nav-dropdown-item" href="member_plus.php">Plus</a>
            </div>
        </li>
        <?php endif; ?>

        <!-- SECCIÓN ONLINE -->
        <?php if(tienePermiso('online') || tienePermiso('all')): ?>
        <li class="nav-dropdown">
            <a class="nav-link" href="#" onclick="toggleDropdown(this); return false;">
                <i class="mdi mdi-access-point"></i>
                <span>Online</span>
            </a>
            <div class="nav-dropdown-menu">
                <a class="nav-dropdown-item" href="asd.php">Black</a>
                <a class="nav-dropdown-item" href="asd.php">Gold</a>
                <a class="nav-dropdown-item" href="asdsd.php">Platinum</a>
                <a class="nav-dropdown-item" href="asdsa.php">Plus</a>
            </div>
        </li>
        <?php endif; ?>

        <!-- SECCIÓN ADMINISTRACIÓN -->
        <?php if(tienePermiso('reportes') || tieneRol('admin')): ?>
        <li class="nav-section">Administración</li>
        
        <li class="nav-dropdown">
            <a class="nav-link" href="#" onclick="toggleDropdown(this); return false;">
                <i class="mdi mdi-file-chart"></i>
                <span>Reportes</span>
            </a>
            <div class="nav-dropdown-menu">
                <a class="nav-dropdown-item" href="reportes-ventas.php">Ventas</a>
                <a class="nav-dropdown-item" href="reportes-usuarios.php">Usuarios</a>
            </div>
        </li>
        <?php endif; ?>

        <?php if(tieneRol('admin') || tienePermiso('usuarios')): ?>
        <li class="nav-item">
            <a class="nav-link" href="usuarios.php">
                <i class="mdi mdi-account-multiple"></i>
                <span>Usuarios</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(tieneRol('admin') || tienePermiso('configuracion')): ?>
        <li class="nav-item">
            <a class="nav-link" href="configuracion.php">
                <i class="mdi mdi-wrench"></i>
                <span>Configuración</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(tieneRol('admin') || tienePermiso('logs')): ?>
        <li class="nav-item">
            <a class="nav-link" href="logs.php">
                <i class="mdi mdi-folder-text-outline"></i>
                <span>Logs</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- LOGOUT -->
        <li class="nav-logout">
            <a class="nav-link" href="#" onclick="confirmarLogout(); return false;">
                <i class="mdi mdi-file-search"></i>
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>

<!-- SWEETALERT2 MINIMALISTA -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Toggle para dropdowns
function toggleDropdown(element) {
    const parent = element.parentElement;
    const wasActive = parent.classList.contains('active');
    
    // Cerrar otros dropdowns
    document.querySelectorAll('.nav-dropdown.active').forEach(item => {
        if (item !== parent) {
            item.classList.remove('active');
        }
    });
    
    // Toggle del actual
    parent.classList.toggle('active');
}

// Logout minimalista
async function confirmarLogout() {
    const result = await Swal.fire({
        title: '¿Cerrar sesión?',
        text: '<?php echo isset($_SESSION['nombre_completo']) ? htmlspecialchars($_SESSION['nombre_completo']) : 'Usuario'; ?>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Salir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        customClass: {
            popup: 'swal-minimal',
            confirmButton: 'btn-minimal-confirm', // Agrega una clase para margen
            cancelButton: 'btn-minimal-cancel me-3'
        },
        buttonsStyling: false
    });

    if (result.isConfirmed) {
        const loading = Swal.fire({
            title: 'Cerrando sesión...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        setTimeout(() => {
            window.location.href = 'includes/logout.php';
        }, 800);
    }
}

// Marcar link activo
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-minimal .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
</script>

<style>
/* SweetAlert Minimalista */
.swal-minimal {
    border-radius: 12px !important;
    padding: 24px !important;
}

.btn-minimal-confirm,
.btn-minimal-cancel {
    padding: 10px 24px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    transition: all 0.2s !important;
}

.btn-minimal-confirm {
    background: #dc3545 !important;
    color: white !important;
}

.btn-minimal-confirm:hover {
    background: #c82333 !important;
}

.btn-minimal-cancel {
    background: #f5f7fa !important;
    color: #666 !important;
}

.btn-minimal-cancel:hover {
    background: #e8eaed !important;
}
</style>