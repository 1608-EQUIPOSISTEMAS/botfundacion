<style>
    :root {
        --header-height: 60px;
        --header-bg: #ffffff;
        --text-primary: #1a1a1a;
        --text-secondary: #666666;
        --accent-color: #0066ff;
        --border-color: #e8eaed;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .header-minimal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        background: var(--header-bg);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        z-index: 1000;
    }

    /* Logo y Brand */
    .header-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .header-logo {
        height: 32px;
        width: auto;
    }

    .header-brand-text {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    /* Menu Toggle */
    .header-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        background: transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
        color: var(--text-primary);
    }

    .header-toggle:hover {
        background: #f5f7fa;
    }

    .header-toggle i {
        font-size: 20px;
    }

    /* Actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }

    .header-action-btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        background: transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
        color: var(--text-secondary);
    }

    .header-action-btn:hover {
        background: #f5f7fa;
        color: var(--text-primary);
    }

    .header-action-btn i {
        font-size: 18px;
    }

    /* Badge de notificaciones */
    .header-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border: 2px solid var(--header-bg);
        border-radius: 50%;
    }

    /* Profile Dropdown */
    .header-profile {
        position: relative;
    }

    .header-profile-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px 6px 6px;
        border: none;
        background: transparent;
        border-radius: 24px;
        cursor: pointer;
        transition: var(--transition);
    }

    .header-profile-btn:hover {
        background: #f5f7fa;
    }

    .header-profile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .header-profile-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .header-profile-chevron {
        font-size: 16px;
        color: var(--text-secondary);
        transition: var(--transition);
    }

    .header-profile.active .header-profile-chevron {
        transform: rotate(180deg);
    }

    /* Dropdown Menu */
    .header-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 240px;
        background: var(--header-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: var(--transition);
        overflow: hidden;
    }

    .header-profile.active .header-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .header-dropdown-header {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .header-dropdown-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-dropdown-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .header-dropdown-info h6 {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 2px 0;
    }

    .header-dropdown-info p {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
    }

    .header-dropdown-menu {
        padding: 8px;
    }

    .header-dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        color: var(--text-primary);
        text-decoration: none;
        border-radius: 8px;
        transition: var(--transition);
        font-size: 14px;
    }

    .header-dropdown-item:hover {
        background: #f5f7fa;
    }

    .header-dropdown-item i {
        font-size: 16px;
        width: 20px;
        color: var(--text-secondary);
    }

    .header-dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 8px 0;
    }

    .header-dropdown-item.danger {
        color: #dc3545;
    }

    .header-dropdown-item.danger:hover {
        background: #fff5f5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-profile-name {
            display: none;
        }
        
        .header-profile-btn {
            padding: 6px;
        }
    }
</style>

<header class="header-minimal">
    <!-- Left Section -->
    <div style="display: flex; align-items: center; gap: 16px;">
        <button class="header-toggle" onclick="toggleSidebar()">
            <i class="mdi mdi-menu"></i>
        </button>
        
        <a href="index.php" class="header-brand">
            <img src="assets/images/we.png" alt="W|E" class="header-logo">
            <span class="header-brand-text">W|E</span>
        </a>
    </div>

    <!-- Right Section -->
    <div class="header-actions">
        <!-- Notificaciones -->
        <button class="header-action-btn" onclick="showNotifications()">
            <i class="mdi mdi-bell-outline"></i>
            <span class="header-badge"></span>
        </button>

        <!-- Mensajes -->
        <button class="header-action-btn" onclick="showMessages()">
            <i class="mdi mdi-email-outline"></i>
        </button>

        <!-- Profile Dropdown -->
        <div class="header-profile" id="profileDropdown">
            <button class="header-profile-btn" onclick="toggleProfile()">
                <img src="assets/images/faces/face28.png" alt="User" class="header-profile-avatar">
                <span class="header-profile-name">Henry Klein</span>
                <i class="mdi mdi-chevron-down header-profile-chevron"></i>
            </button>

            <div class="header-dropdown">
                <div class="header-dropdown-header">
                    <div class="header-dropdown-user">
                        <img src="assets/images/faces/face28.png" alt="User" class="header-dropdown-avatar">
                        <div class="header-dropdown-info">
                            <h6>Henry Klein</h6>
                            <p>henry@example.com</p>
                        </div>
                    </div>
                </div>

                <div class="header-dropdown-menu">
                    <a href="profile.php" class="header-dropdown-item">
                        <i class="mdi mdi-account-outline"></i>
                        <span>Mi Perfil</span>
                    </a>
                    
                    <a href="settings.php" class="header-dropdown-item">
                        <i class="mdi mdi-wrench-outline"></i>
                        <span>Configuración</span>
                    </a>

                    <div class="header-dropdown-divider"></div>

                    <a href="#" onclick="confirmarLogout(); return false;" class="header-dropdown-item danger">
                        <i class="mdi mdi-logout"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Toggle Profile Dropdown
function toggleProfile() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('active');
    
    // Cerrar al hacer click fuera
    if (dropdown.classList.contains('active')) {
        setTimeout(() => {
            document.addEventListener('click', closeProfileOnClickOutside);
        }, 0);
    }
}

function closeProfileOnClickOutside(e) {
    const dropdown = document.getElementById('profileDropdown');
    if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
        document.removeEventListener('click', closeProfileOnClickOutside);
    }
}

// Toggle Sidebar (debe estar conectado con tu sidebar)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}

// Placeholders para funcionalidad futura
function showNotifications() {
    console.log('Mostrar notificaciones');
    // Aquí irá tu lógica de notificaciones
}

function showMessages() {
    console.log('Mostrar mensajes');
    // Aquí irá tu lógica de mensajes
}
</script>