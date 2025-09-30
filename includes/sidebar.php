<?php
function tienePermiso($permiso) {
    if (!isset($_SESSION['permisos_asignados'])) {
        return false;
    }
    
    $permisos = $_SESSION['permisos_asignados'];
    
    // Si tiene permiso "all", tiene acceso a todo
    if (in_array('all', $permisos)) {
        return true;
    }
    
    // Verificar si tiene el permiso específico
    return in_array($permiso, $permisos);
}

/**
 * Verifica si el usuario tiene al menos uno de los permisos dados
 * @param array $permisos - Array de permisos a verificar
 * @return bool
 */
function tieneAlgunPermiso($permisos) {
    foreach ($permisos as $permiso) {
        if (tienePermiso($permiso)) {
            return true;
        }
    }
    return false;
}

/**
 * Verifica si el usuario tiene todos los permisos dados
 * @param array $permisos - Array de permisos a verificar
 * @return bool
 */
function tieneTodosPermisos($permisos) {
    foreach ($permisos as $permiso) {
        if (!tienePermiso($permiso)) {
            return false;
        }
    }
    return true;
}

/**
 * Verifica si el usuario tiene un rol específico
 * @param string $rol - Nombre del rol
 * @return bool
 */
function tieneRol($rol) {
    return isset($_SESSION['rol_nombre']) && $_SESSION['rol_nombre'] === $rol;
}
?>

<!-- ============================================ -->
<!-- SIDEBAR CON CONTROL DE PERMISOS -->
<!-- ============================================ -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-category">Información</li>
        
        <!-- INICIO/DASHBOARD - Todos pueden ver -->
        <?php if(tienePermiso('dashboard') || tienePermiso('all')): ?>
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                <span class="menu-title">Inicio</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- CONECTAR BOT -->
        <?php if(tienePermiso('members') || tienePermiso('all')): ?>
        <li class="nav-item">
            <a class="nav-link" href="bot.php">
                <span class="icon-bg"><i class="mdi mdi-link-variant menu-icon"></i></span>
                <span class="menu-title">Conectar</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- FUNDACIÓN - Solo si tiene permiso de fundación -->
        <?php if(tienePermiso('fundacion')): ?>
        <li class="nav-item">
            <a class="nav-link" href="bot2.php">
                <span class="icon-bg"><i class="mdi mdi-link-variant menu-icon"></i></span>
                <span class="menu-title">Primera Linea</span>
            </a>
        </li>
                <li class="nav-item">
            <a class="nav-link" href="bot3.php">
                <span class="icon-bg"><i class="mdi mdi-link-variant menu-icon"></i></span>
                <span class="menu-title">Segunda Linea</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="fundacion.php">
                <span class="icon-bg"><i class="mdi mdi-heart menu-icon"></i></span>
                <span class="menu-title">Fundación</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#online" aria-expanded="false" aria-controls="online">
            <span class="icon-bg"><i class="mdi mdi-file-document-box"></i></span>
            <span class="menu-title">Registros</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="online">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="información.php">Información</a></li>
                <li class="nav-item"><a class="nav-link" href="dashboardinfo.php">Dashboard</a></li>
            </ul>
            </div>
        </li>
        <?php endif; ?>

        <!-- MEMBERS - Dropdown -->
        <?php if(tienePermiso('members') || tienePermiso('all')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="icon-bg"><i class="mdi mdi-lock menu-icon"></i></span>
                <span class="menu-title">Members</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                    <?php if(tienePermiso('members') || tienePermiso('all')): ?>
                    <li class="nav-item"><a class="nav-link" href="black.php">Black</a></li>
                    <li class="nav-item"><a class="nav-link" href="gold.php">Gold</a></li>
                    <li class="nav-item"><a class="nav-link" href="platinum.php">Platinum</a></li>
                    <li class="nav-item"><a class="nav-link" href="plus.php">Plus</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>
        <?php endif; ?>

        <!-- ONLINE - Solo si tiene permiso online -->
        <?php if(tienePermiso('online') || tienePermiso('all')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#online" aria-expanded="false" aria-controls="online">
            <span class="icon-bg"><i class="mdi mdi-wifi menu-icon"></i></span>
            <span class="menu-title">Online</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="online">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="asd.php">Black</a></li>
                <li class="nav-item"><a class="nav-link" href="asd.php">Gold</a></li>
                <li class="nav-item"><a class="nav-link" href="asdsd.php">Platinum</a></li>
                <li class="nav-item"><a class="nav-link" href="asdsa.php">Plus</a></li>
            </ul>
            </div>
        </li>
        <?php endif; ?>

        <!-- REPORTES - Para admins o usuarios con permiso -->
        <?php if(tienePermiso('reportes') || tieneRol('admin')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reportes" aria-expanded="false" aria-controls="reportes">
                <span class="icon-bg"><i class="mdi mdi-chart-bar menu-icon"></i></span>
                <span class="menu-title">Reportes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reportes">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="reportes-ventas.php">Ventas</a></li>
                    <li class="nav-item"><a class="nav-link" href="reportes-usuarios.php">Usuarios</a></li>
                </ul>
            </div>
        </li>
        <?php endif; ?>

        <!-- CONFIGURACIÓN - Solo admins -->
        <?php if(tieneRol('admin') || tienePermiso('configuracion')): ?>
        <li class="nav-item">
            <a class="nav-link" href="configuracion.php">
                <span class="icon-bg"><i class="mdi mdi-settings menu-icon"></i></span>
                <span class="menu-title">Configuración</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- GESTIÓN DE USUARIOS - Solo admins -->
        <?php if(tieneRol('admin') || tienePermiso('usuarios')): ?>
        <li class="nav-item">
            <a class="nav-link" href="usuarios.php">
                <span class="icon-bg"><i class="mdi mdi-account-multiple menu-icon"></i></span>
                <span class="menu-title">Usuarios</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- LOGS - Solo admins -->
        <?php if(tieneRol('admin') || tienePermiso('logs')): ?>
        <li class="nav-item">
            <a class="nav-link" href="logs.php">
                <span class="icon-bg"><i class="mdi mdi-file-document menu-icon"></i></span>
                <span class="menu-title">Logs del Sistema</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- SEPARADOR -->
        <li class="nav-item nav-category">Cuenta</li>

        <!-- PERFIL - Todos pueden ver su perfil -->
        <li class="nav-item sidebar-user-actions">
            <div class="sidebar-user-menu">
                <a href="perfil.php" class="nav-link">
                    <i class="mdi mdi-account-circle menu-icon"></i>
                    <span class="menu-title">Mi Perfil</span>
                </a>
            </div>
        </li>

        <!-- CERRAR SESIÓN - Todos pueden cerrar sesión -->
        <li class="nav-item sidebar-user-actions">
            <div class="sidebar-user-menu">
                <a href="#" onclick="confirmarLogout(); return false;" class="nav-link text-danger">
                    <i class="mdi mdi-logout menu-icon"></i>
                    <span class="menu-title">Cerrar Sesión</span>
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- ============================================ -->
<!-- SWEETALERT2 PARA LOGOUT -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
.swal2-popup {
    border-radius: 20px !important;
    padding: 30px !important;
}

.swal2-icon {
    margin: 20px auto !important;
}

.logout-icon {
    font-size: 80px;
    animation: rotate 2s ease-in-out infinite;
}

@keyframes rotate {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
}
</style>

<script>
async function confirmarLogout() {
    const result = await Swal.fire({
        title: '¿Cerrar Sesión?',
        html: `
            <div style="text-align: center; padding: 20px;">
                <div class="logout-icon">👋</div>
                <p style="font-size: 1.2rem; color: #555; margin: 20px 0;">
                    <strong><?php echo isset($_SESSION['nombre_completo']) ? htmlspecialchars($_SESSION['nombre_completo']) : 'Usuario'; ?></strong>
                </p>
                <p style="color: #777;">
                    ¿Estás seguro que deseas cerrar tu sesión?
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✓ Sí, Salir',
        cancelButtonText: '✗ Cancelar',
        reverseButtons: true,
        width: '450px',
        padding: '20px',
        background: '#fff',
        backdrop: 'rgba(0, 0, 0, 0.5)',
        showClass: {
            popup: 'animate__animated animate__zoomIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__zoomOut animate__faster'
        },
        customClass: {
            popup: 'border-0 shadow-lg',
            confirmButton: 'btn btn-danger btn-lg mx-2 px-4',
            cancelButton: 'btn btn-outline-secondary btn-lg mx-2 px-4'
        },
        buttonsStyling: false,
        allowOutsideClick: false,
        allowEscapeKey: true,
        focusCancel: true
    });

    if (result.isConfirmed) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        await Toast.fire({
            icon: 'success',
            title: 'Cerrando sesión...'
        });

        await Swal.fire({
            title: '¡Hasta Pronto! 👋',
            text: 'Gracias por usar nuestro sistema',
            icon: 'success',
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            showClass: {
                popup: 'animate__animated animate__fadeIn'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOut'
            }
        });

        window.location.href = 'includes/logout.php';
    }
}
</script>