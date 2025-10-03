<?php
session_start();
require_once 'conexion/conexion.php';

try {
    $sql = "SELECT id, welcome, presentation_route, brochure_route, modality_first_route, modality_second_route, sesion, inversion_route, key_words, final_text FROM bot_foundation WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bot = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $bot = null;
    $error_message = "Error al obtener datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>W|E - Bot Foundation</title>
    
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .main-panel {
            margin-left: 260px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
        }

        .content-wrapper {
            padding: 40px 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .header-content h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .breadcrumb {
            font-size: 14px;
            color: #737373;
        }

        .breadcrumb a {
            color: #0066ff;
            text-decoration: none;
        }

        .btn-edit-main {
            padding: 12px 28px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-edit-main:hover {
            background: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Section containers */
        .config-section {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            font-size: 18px;
            color: #a3a3a3;
        }

        /* Message box */
        .message-box {
            background: #f9f9f9;
            border-left: 3px solid #0066ff;
            padding: 20px;
            border-radius: 8px;
            font-size: 15px;
            line-height: 1.6;
            color: #1a1a1a;
        }

        /* Media grid - 3 columnas para imágenes */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 24px;
        }

        .media-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }

        .media-card:hover {
            border-color: #1a1a1a;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .media-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .media-header i {
            font-size: 20px;
            color: #737373;
        }

        .media-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .media-preview {
            position: relative;
            width: 100%;
            height: 280px;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .media-card:hover .media-preview img {
            transform: scale(1.05);
        }

        .media-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .media-card:hover .media-overlay {
            opacity: 1;
        }

        .media-action {
            width: 44px;
            height: 44px;
            background: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .media-action:hover {
            transform: scale(1.1);
        }

        .media-action i {
            font-size: 20px;
        }

        .action-view {
            color: #0066ff;
        }

        .action-download {
            color: #22c55e;
        }

        .media-footer {
            padding: 12px 20px;
            background: #fafafa;
        }

        .file-path {
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 11px;
            color: #737373;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* PDF Card - diferente de imágenes */
        .pdf-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.2s;
        }

        .pdf-card:hover {
            border-color: #ef4444;
            box-shadow: 0 4px 16px rgba(239,68,68,0.1);
            transform: translateY(-2px);
        }

        .pdf-icon {
            width: 64px;
            height: 64px;
            background: #fef2f2;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pdf-icon i {
            font-size: 32px;
            color: #ef4444;
        }

        .pdf-info {
            flex: 1;
        }

        .pdf-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .pdf-path {
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 12px;
            color: #737373;
            margin-bottom: 12px;
        }

        .pdf-actions {
            display: flex;
            gap: 8px;
        }

        .btn-pdf {
            padding: 8px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            background: #fff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-pdf:hover {
            transform: translateY(-1px);
        }

        .btn-pdf.view {
            border-color: #ef4444;
            color: #ef4444;
        }

        .btn-pdf.view:hover {
            background: #fef2f2;
        }

        .btn-pdf.download {
            border-color: #22c55e;
            color: #22c55e;
        }

        .btn-pdf.download:hover {
            background: #f0fdf4;
        }

        /* Text sections */
        .text-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .text-box {
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 20px;
        }

        .text-label {
            font-size: 12px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .text-label i {
            font-size: 16px;
        }

        .text-content {
            font-size: 14px;
            line-height: 1.6;
            color: #1a1a1a;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 120px 20px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 64px;
            color: #d4d4d4;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 15px;
            color: #737373;
        }

        /* Error state */
        .error-preview {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #ef4444;
        }

        .error-preview i {
            font-size: 32px;
        }

        .error-preview span {
            font-size: 12px;
        }

        /* Lightbox para imágenes */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .lightbox.active {
            display: flex;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 8px;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            background: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-close i {
            font-size: 24px;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .main-panel {
                margin-left: 0;
            }

            .media-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .text-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 24px 16px;
            }

            .page-header {
                flex-direction: column;
                gap: 20px;
            }

            .media-grid {
                grid-template-columns: 1fr;
            }

            .config-section {
                padding: 20px;
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
                            <h1>Bot Fundación</h1>
                        </div>
                        <?php if ($bot): ?>
                        <button class="btn-edit-main" data-toggle="modal" data-target="#editarBotModal" data-id="<?php echo $bot['id']; ?>">
                            <i class="mdi mdi-pencil"></i>
                            Editar Configuración
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($bot): ?>
                        
                        <!-- Mensaje de Bienvenida -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="mdi mdi-message-text"></i>
                                Mensaje de Bienvenida
                            </div>
                            <div class="message-box">
                                <?php echo nl2br(htmlspecialchars($bot['welcome'])); ?>
                            </div>
                        </div>

                        <!-- Recursos Visuales -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="mdi mdi-image-multiple"></i>
                                Recursos Visuales
                            </div>

                            <div class="media-grid">
                                <!-- Imagen Presentación -->
                                <div class="media-card">
                                    <div class="media-header">
                                        <i class="mdi mdi-presentation"></i>
                                        <span class="media-title">Presentación</span>
                                    </div>
                                    <div class="media-preview">
                                        <?php if (!empty($bot['presentation_route'])): ?>
                                            <img 
                                                src="<?php echo htmlspecialchars($bot['presentation_route']); ?>" 
                                                alt="Presentación"
                                                loading="lazy"
                                                onclick="openLightbox(this.src)"
                                                onerror="this.parentElement.innerHTML='<div class=\'error-preview\'><i class=\'mdi mdi-image-broken\'></i><span>No disponible</span></div>'"
                                            >
                                            <div class="media-overlay">
                                                <button class="media-action action-view" onclick="openLightbox('<?php echo htmlspecialchars($bot['presentation_route']); ?>')">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <a href="<?php echo htmlspecialchars($bot['presentation_route']); ?>" download class="media-action action-download">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="error-preview">
                                                <i class="mdi mdi-image-off"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-footer">
                                        <div class="file-path"><?php echo htmlspecialchars($bot['presentation_route']); ?></div>
                                    </div>
                                </div>

                                <!-- Modalidad 1 -->
                                <div class="media-card">
                                    <div class="media-header">
                                        <i class="mdi mdi-numeric-1-circle"></i>
                                        <span class="media-title">Modalidad 1</span>
                                    </div>
                                    <div class="media-preview">
                                        <?php if (!empty($bot['modality_first_route'])): ?>
                                            <img 
                                                src="<?php echo htmlspecialchars($bot['modality_first_route']); ?>" 
                                                alt="Modalidad 1"
                                                loading="lazy"
                                                onclick="openLightbox(this.src)"
                                                onerror="this.parentElement.innerHTML='<div class=\'error-preview\'><i class=\'mdi mdi-image-broken\'></i><span>No disponible</span></div>'"
                                            >
                                            <div class="media-overlay">
                                                <button class="media-action action-view" onclick="openLightbox('<?php echo htmlspecialchars($bot['modality_first_route']); ?>')">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <a href="<?php echo htmlspecialchars($bot['modality_first_route']); ?>" download class="media-action action-download">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="error-preview">
                                                <i class="mdi mdi-image-off"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-footer">
                                        <div class="file-path"><?php echo htmlspecialchars($bot['modality_first_route']); ?></div>
                                    </div>
                                </div>

                                <!-- Modalidad 2 -->
                                <div class="media-card">
                                    <div class="media-header">
                                        <i class="mdi mdi-numeric-2-circle"></i>
                                        <span class="media-title">Modalidad 2</span>
                                    </div>
                                    <div class="media-preview">
                                        <?php if (!empty($bot['modality_second_route'])): ?>
                                            <img 
                                                src="<?php echo htmlspecialchars($bot['modality_second_route']); ?>" 
                                                alt="Modalidad 2"
                                                loading="lazy"
                                                onclick="openLightbox(this.src)"
                                                onerror="this.parentElement.innerHTML='<div class=\'error-preview\'><i class=\'mdi mdi-image-broken\'></i><span>No disponible</span></div>'"
                                            >
                                            <div class="media-overlay">
                                                <button class="media-action action-view" onclick="openLightbox('<?php echo htmlspecialchars($bot['modality_second_route']); ?>')">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <a href="<?php echo htmlspecialchars($bot['modality_second_route']); ?>" download class="media-action action-download">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="error-preview">
                                                <i class="mdi mdi-image-off"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-footer">
                                        <div class="file-path"><?php echo htmlspecialchars($bot['modality_second_route']); ?></div>
                                    </div>
                                </div>

                                <!-- Inversión (IMAGEN, no PDF) -->
                                <div class="media-card">
                                    <div class="media-header">
                                        <i class="mdi mdi-chart-line"></i>
                                        <span class="media-title">Inversión</span>
                                    </div>
                                    <div class="media-preview">
                                        <?php if (!empty($bot['inversion_route'])): ?>
                                            <img 
                                                src="<?php echo htmlspecialchars($bot['inversion_route']); ?>" 
                                                alt="Inversión"
                                                loading="lazy"
                                                onclick="openLightbox(this.src)"
                                                onerror="this.parentElement.innerHTML='<div class=\'error-preview\'><i class=\'mdi mdi-image-broken\'></i><span>No disponible</span></div>'"
                                            >
                                            <div class="media-overlay">
                                                <button class="media-action action-view" onclick="openLightbox('<?php echo htmlspecialchars($bot['inversion_route']); ?>')">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <a href="<?php echo htmlspecialchars($bot['inversion_route']); ?>" download class="media-action action-download">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="error-preview">
                                                <i class="mdi mdi-image-off"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-footer">
                                        <div class="file-path"><?php echo htmlspecialchars($bot['inversion_route']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Brochure PDF (separado) -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="mdi mdi-file-pdf"></i>
                                Brochure
                            </div>
                            
                            <div class="pdf-card">
                                <div class="pdf-icon">
                                    <i class="mdi mdi-file-pdf-box"></i>
                                </div>
                                <div class="pdf-info">
                                    <div class="pdf-title">Brochure de Fundación</div>
                                    <div class="pdf-path"><?php echo htmlspecialchars($bot['brochure_route']); ?></div>
                                    <div class="pdf-actions">
                                        <a href="<?php echo htmlspecialchars($bot['brochure_route']); ?>" target="_blank" class="btn-pdf view">
                                            <i class="mdi mdi-eye"></i>
                                            Ver PDF
                                        </a>
                                        <a href="<?php echo htmlspecialchars($bot['brochure_route']); ?>" download class="btn-pdf download">
                                            <i class="mdi mdi-download"></i>
                                            Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Textos adicionales -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="mdi mdi-text"></i>
                                Información Adicional
                            </div>

                            <div class="text-grid">
                                <div class="text-box">
                                    <div class="text-label">
                                        <i class="mdi mdi-calendar-clock"></i>
                                        Sesión
                                    </div>
                                    <div class="text-content">
                                        <?php echo nl2br(htmlspecialchars($bot['sesion'])); ?>
                                    </div>
                                </div>

                                <div class="text-box">
                                    <div class="text-label">
                                        <i class="mdi mdi-key"></i>
                                        Palabras Clave
                                    </div>
                                    <div class="text-content">
                                        <?php echo nl2br(htmlspecialchars($bot['key_words'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Texto Final -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="mdi mdi-text"></i>
                                Mensaje Final
                            </div>
                            <div class="message-box">
                                <?php echo nl2br(htmlspecialchars($bot['final_text'])); ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="mdi mdi-robot-confused"></i>
                            <h3>Sin configuración</h3>
                            <p>No se encontró la configuración del bot</p>
                        </div>
                    <?php endif; ?>

                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="mdi mdi-close"></i>
        </button>
        <img src="" alt="Preview" id="lightboxImage">
    </div>

    <?php include 'modals/fundacion/editar.php'; ?>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Lightbox
        function openLightbox(src) {
            document.getElementById('lightboxImage').src = src;
            document.getElementById('lightbox').classList.add('active');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
        }

        // Submit form
        function submitBotForm() {
            const form = document.getElementById('formEditarBot');
            const formData = new FormData(form);
            
            Swal.fire({
                title: '¿Guardar cambios?',
                text: 'Se actualizará la configuración del bot',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                cancelButtonColor: '#737373',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Guardando...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    $.ajax({
                        url: 'acciones/fundacion/editar.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Guardado!',
                                    text: response.message || 'Configuración actualizada',
                                    confirmButtonColor: '#1a1a1a',
                                    timer: 2000
                                }).then(() => {
                                    $('#editarBotModal').modal('hide');
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'No se pudo actualizar',
                                    confirmButtonColor: '#1a1a1a'
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de conexión',
                                text: 'No se pudo conectar con el servidor',
                                confirmButtonColor: '#1a1a1a'
                            });
                        }
                    });
                }
            });
        }

        // Close lightbox on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>
</body>
</html>