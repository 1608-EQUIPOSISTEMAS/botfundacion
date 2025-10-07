<?php
session_start();
require_once 'conexion/conexion.php';

function formatWhatsappText($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\*([^\*]+)\*/', '<b>$1</b>', $text);
    $text = preg_replace('/\_([^_]+)\_/', '<i>$1</i>', $text);
    $text = preg_replace('/\~([^\~]+)\~/', '<s>$1</s>', $text);
    $text = preg_replace('/```([^`]+)```/', '<code>$1</code>', $text);
    $text = nl2br($text);
    return $text;
}

try {
    $sql = "SELECT id, nombre, ruta_post, beneficio, ruta_pdf, precio FROM members WHERE id = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nombreplan = $member ? htmlspecialchars($member['nombre']) : 'Plan no encontrado';
    
    $benefits_list = [];
    if ($member && !empty($member['beneficio'])) {
        $benefits_list = array_filter(array_map('trim', explode("\n", $member['beneficio'])));
    }
} catch (PDOException $e) {
    $member = null;
    $error_message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $nombreplan; ?></title>
    
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
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

        /* Header con nombre del plan */
        .plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .plan-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .plan-badge {
            padding: 8px 20px;
            background: #1a1a1a;
            color: #fff;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Grid principal: info a la izquierda, recursos a la derecha */
        .plan-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
            align-items: start;
        }

        /* Cards */
        .plan-card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f5f5f5;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            font-size: 20px;
            color: #737373;
        }

        .card-body {
            padding: 24px;
        }

        /* Precio destacado */
        .price-display {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            line-height: 1.3;
        }
        
        .price-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        

        /* Beneficios */
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .benefits-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 15px;
            line-height: 1.6;
            color: #1a1a1a;
        }

        .benefits-list li i {
            color: #22c55e;
            font-size: 20px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .benefits-list code {
            font-family: 'SF Mono', Monaco, monospace;
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Acciones de gestión - Grid de 2 columnas */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .btn-action {
            padding: 12px 20px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #525252;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-action:hover {
            border-color: #1a1a1a;
            background: #fafafa;
            transform: translateY(-1px);
        }

        .btn-action i {
            font-size: 16px;
        }

        .btn-editar:hover {
            border-color: #eab308;
            background: #fafafa;
            transform: translateY(-1px);
        }

        .btn-opciones:hover {
            border-color: #1a1a1a;
            background: #fafafa;
            transform: translateY(-1px);
        }

        .btn-respuestas:hover {
            border-color: #46a96cff;
            background: #fafafa;
            transform: translateY(-1px);
        }

        .btn-pagos:hover {
            border-color: #ffb300ff;
            background: #fafafa;
            transform: translateY(-1px);
        }

        /* Sidebar de recursos */
        .sidebar-sticky {
            position: sticky;
            top: 80px;
        }

        /* Preview de imagen */
        .image-preview {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e5e5;
            cursor: pointer;
            transition: all 0.2s;
        }

        .image-preview:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* PDF Actions */
        .pdf-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .btn-pdf {
            padding: 12px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            background: #fff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            color: #525252;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .main-panel {
                margin-left: 0;
            }

            .plan-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-sticky {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 24px 16px;
            }

            .plan-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .price-display {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php include 'includes/header.php'; ?>
        
        <div class="container-fluid page-body-wrapper">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="main-panel">
                <div class="content-wrapper">
                    
                    Modificación del <?php if ($member): ?>
                        
                        <!-- Header -->
                        <div class="plan-header">
                            <h1><?php echo htmlspecialchars($member['nombre']); ?></h1>
                            <span class="plan-badge">Plan Activo</span>
                        </div>

                        <!-- Layout Principal -->
                        <div class="plan-layout">
                            
                            <!-- Columna Izquierda: Info del Plan -->
                            <div class="plan-main">
                                <div class="plan-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="mdi mdi-cog-outline"></i>
                                            Gestión del Plan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="actions-grid">
                                            <button class="btn-action btn-editar" data-toggle="modal" data-target="#editarModal"
                                                data-id="<?= htmlspecialchars($member['id']) ?>"
                                                data-nombre="<?= htmlspecialchars($member['nombre']) ?>"
                                                data-precio="<?= htmlspecialchars($member['precio']) ?>"
                                                data-ruta-post="<?= htmlspecialchars($member['ruta_post']) ?>"
                                                data-beneficio="<?= htmlspecialchars($member['beneficio']) ?>"
                                                data-ruta-pdf="<?= htmlspecialchars($member['ruta_pdf']) ?>">
                                                <i class="mdi mdi-pencil"></i>
                                                Editar Plan
                                            </button>

                                            <button class="btn-action btn-opciones" data-toggle="modal" data-target="#editarModalopciones"
                                                data-id="<?= htmlspecialchars($member['id']) ?>">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                                Opciones
                                            </button>

                                            <button class="btn-action  btn-respuestas" data-toggle="modal" data-target="#editarModalrespuestas"
                                                data-id="<?= htmlspecialchars($member['id']) ?>">
                                                <i class="mdi mdi-message-reply-text"></i>
                                                Respuestas
                                            </button>

                                            <button class="btn-action btn-pagos" data-toggle="modal" data-target="#editarModalPago"
                                                data-id="<?= htmlspecialchars($member['id']) ?>">
                                                <i class="mdi mdi-credit-card"></i>
                                                Pagos
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="plan-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="mdi mdi-currency-usd"></i>
                                            Precio
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="price-display">
                                            <?php echo formatWhatsappText($member['precio']); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Beneficios -->
                                <div class="plan-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="mdi mdi-star-outline"></i>
                                            Beneficios Incluidos
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($benefits_list)): ?>
                                            <ul class="benefits-list">
                                                <?php foreach ($benefits_list as $benefit): ?>
                                                    <li>
                                                        <span><?php echo formatWhatsappText($benefit); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p style="color: #737373; margin: 0;">No hay beneficios registrados</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha: Recursos -->
                            <aside class="plan-sidebar">
                                <div class="sidebar-sticky">
                                    
                                    <!-- Imagen Promocional -->
                                    <div class="plan-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="mdi mdi-image"></i>
                                                Imagen Promocional
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="image-preview" onclick="previewImage('<?php echo htmlspecialchars($member['ruta_post']); ?>')">
                                                <img src="<?php echo htmlspecialchars($member['ruta_post']); ?>" 
                                                     alt="<?php echo htmlspecialchars($member['nombre']); ?>" 
                                                     loading="lazy">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PDF del Plan -->
                                    <div class="plan-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="mdi mdi-file-pdf-box"></i>
                                                Documento
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="pdf-actions">
                                                <a href="<?php echo htmlspecialchars($member['ruta_pdf']); ?>" 
                                                   target="_blank" 
                                                   class="btn-pdf view">
                                                    <i class="mdi mdi-eye"></i>
                                                    Ver
                                                </a>
                                                <a href="<?php echo htmlspecialchars($member['ruta_pdf']); ?>" 
                                                   download 
                                                   class="btn-pdf download">
                                                    <i class="mdi mdi-download"></i>
                                                    Descargar
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </aside>

                        </div>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="mdi mdi-alert-circle-outline"></i>
                            <h3>Plan no encontrado</h3>
                            <p>No se pudo cargar la información del plan</p>
                        </div>
                    <?php endif; ?>

                </div>
                <?php include 'includes/footer.php'; ?>
            </main>
        </div>
    </div>

    <?php include 'modals/member_black/modal_edit.php'; ?>
    <?php include 'modals/member_black/modal_options.php'; ?>
    <?php include 'modals/member_black/modal_answers.php'; ?>
    <?php include 'modals/member_black/modal_pays.php'; ?>
    
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Preview de imagen en modal
        function previewImage(src) {
            Swal.fire({
                imageUrl: src,
                imageAlt: 'Vista ampliada',
                showConfirmButton: false,
                showCloseButton: true,
                background: '#000',
                backdrop: 'rgba(0,0,0,0.9)',
                customClass: {
                    image: 'img-fluid'
                }
            });
        }

        // Toast notifications
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        <?php if (isset($error_message)): ?>
        Swal.fire({
            title: 'Error',
            text: '<?php echo addslashes($error_message); ?>',
            icon: 'error',
            confirmButtonColor: '#1a1a1a'
        });
        <?php endif; ?>

        // Cargar datos en modal de edición
        $('#editarModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const modal = $(this);
            modal.find('#member-id').val(button.data('id'));
            modal.find('#member-nombre').val(button.data('nombre'));
            modal.find('#member-ruta-post').val(button.data('ruta-post'));
            modal.find('#member-beneficio').val(button.data('beneficio'));
            modal.find('#member-precio').val(button.data('precio'));
            modal.find('#member-ruta-pdf').val(button.data('ruta-pdf'));
        });

        let currentMemberId = null;
        
        $('#editarModalPago').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            currentMemberId = button.data('id');
            
            if (!currentMemberId) {
                $('#payment-methods-container').html(`
                    <div class="empty-payments">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        <p>Error: No se pudo obtener el ID de la membresía</p>
                    </div>
                `);
                return;
            }
            
            loadPaymentMethods();
        });

        function loadPaymentMethods() {
            $('#payment-methods-container').html(`
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p style="color: var(--color-text-subtle);">Cargando métodos de pago...</p>
                </div>
            `);
            
            $.ajax({
                url: 'actions/members/get-pays.php',
                type: 'GET',
                data: { member_id: currentMemberId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<div class="payment-methods-grid">';
                        
                        response.data.forEach(function(method) {
                            let iconClass = 'transferencia';
                            let icon = 'mdi-bank';
                            
                            if (method.metodo === 'yape') {
                                iconClass = 'yape';
                                icon = 'mdi-cellphone';
                            } else if (method.metodo === 'tarjeta') {
                                iconClass = 'tarjeta';
                                icon = 'mdi-credit-card';
                            }
                            
                            html += `
                                <div class="payment-card" data-payment-id="${method.id}">
                                    <div class="payment-header">
                                        <div class="payment-header-left">
                                            <div class="payment-icon ${iconClass}">
                                                <i class="mdi ${icon}"></i>
                                            </div>
                                            <h4 class="payment-title">${method.metodo}</h4>
                                        </div>
                                        <button class="btn-edit-payment" onclick="toggleEditForm(${method.id}, '${method.tipo}', '${method.metodo}')">
                                            <i class="mdi mdi-pencil"></i>
                                            Editar
                                        </button>
                                    </div>
                                    <div class="payment-content ${method.tipo}" id="content-${method.id}">
                            `;
                            
                            if (method.tipo === 'imagen') {
                                html += `<img src="${method.contenido}" alt="${method.metodo}" loading="lazy" onclick="previewImage('${method.contenido}')">`;
                            } else {
                                html += method.contenido.replace(/\n/g, '<br>');
                            }
                            
                            html += `
                                    </div>
                                    <div class="payment-edit-form" id="form-${method.id}"></div>
                                </div>
                            `;
                        });
                        
                        html += '</div>';
                        $('#payment-methods-container').html(html);
                        
                    } else {
                        $('#payment-methods-container').html(`
                            <div class="empty-payments">
                                <i class="mdi mdi-credit-card-off-outline"></i>
                                <p>No hay métodos de pago configurados para este plan</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#payment-methods-container').html(`
                        <div class="empty-payments">
                            <i class="mdi mdi-alert-circle-outline"></i>
                            <p>Error al cargar los métodos de pago</p>
                        </div>
                    `);
                }
            });
        }

        function toggleEditForm(paymentId, currentTipo, metodo) {
            const form = $(`#form-${paymentId}`);
            const content = $(`#content-${paymentId}`);
            
            if (form.hasClass('active')) {
                form.removeClass('active').html('');
                content.show();
            } else {
                $('.payment-edit-form').removeClass('active').html('');
                $('.payment-content').show();
                
                let formHtml = `
                    <div class="edit-form-group">
                        <label>Tipo de Contenido</label>
                        <select class="form-control-ux" id="tipo-${paymentId}" onchange="updateFormContent(${paymentId}, '${metodo}')">
                            <option value="texto" ${currentTipo === 'texto' ? 'selected' : ''}>Texto</option>
                            <option value="imagen" ${currentTipo === 'imagen' ? 'selected' : ''}>Imagen</option>
                        </select>
                    </div>
                    <div id="content-field-${paymentId}"></div>
                    <div class="edit-form-actions">
                        <button class="btn-cancel-payment" onclick="toggleEditForm(${paymentId})">
                            <i class="mdi mdi-close"></i>
                            Cancelar
                        </button>
                        <button class="btn-save-payment" onclick="savePaymentMethod(${paymentId})">
                            <i class="mdi mdi-content-save"></i>
                            Guardar
                        </button>
                    </div>
                `;
                
                form.html(formHtml).addClass('active');
                content.hide();
                
                updateFormContent(paymentId, metodo);
            }
        }

        function updateFormContent(paymentId, metodo) {
            const tipo = $(`#tipo-${paymentId}`).val();
            const contentField = $(`#content-field-${paymentId}`);
            
            if (tipo === 'imagen') {
                contentField.html(`
                    <div class="edit-form-group">
                        <label>Seleccionar Nueva Imagen</label>
                        <label for="upload-payment-${paymentId}" class="file-upload-area">
                            <i class="mdi mdi-cloud-upload"></i>
                            <p>Click para seleccionar imagen</p>
                            <p style="font-size: 0.8rem; margin-top: 0.5rem;">JPG, PNG (Máx. 5MB)</p>
                        </label>
                        <input type="file" id="upload-payment-${paymentId}" accept="image/*" style="display: none;" onchange="previewUploadImage(${paymentId})">
                        <img id="preview-${paymentId}" class="image-preview-edit" style="display: none;">
                    </div>
                `);
            } else {
                const currentText = $(`#content-${paymentId}`).text();
                contentField.html(`
                    <div class="edit-form-group">
                        <label>Contenido</label>
                        <textarea class="form-control-ux" id="contenido-${paymentId}" rows="5">${currentText}</textarea>
                    </div>
                `);
            }
        }

        function previewUploadImage(paymentId) {
            const file = $(`#upload-payment-${paymentId}`)[0].files[0];
            const preview = $(`#preview-${paymentId}`);
            
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'La imagen no debe superar los 5MB',
                        confirmButtonColor: '#3b82f6'
                    });
                    $(`#upload-payment-${paymentId}`).val('');
                    preview.hide();
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        }

        function savePaymentMethod(paymentId) {
            const tipo = $(`#tipo-${paymentId}`).val();
            const formData = new FormData();
            
            formData.append('id', paymentId);
            formData.append('tipo', tipo);
            
            if (tipo === 'imagen') {
                const fileInput = $(`#upload-payment-${paymentId}`)[0];
                if (!fileInput.files || !fileInput.files[0]) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Falta imagen',
                        text: 'Por favor selecciona una imagen',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                formData.append('imagen', fileInput.files[0]);
            } else {
                const contenido = $(`#contenido-${paymentId}`).val().trim();
                if (!contenido) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo vacío',
                        text: 'El contenido no puede estar vacío',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                formData.append('contenido', contenido);
            }
            
            Swal.fire({
                title: 'Guardando...',
                text: 'Actualizando método de pago',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'actions/members/edit-pays.php',
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
                            title: 'Actualizado',
                            text: 'Método de pago actualizado correctamente',
                            confirmButtonColor: '#3b82f6',
                            timer: 2000
                        }).then(() => {
                            loadPaymentMethods();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudo actualizar',
                            confirmButtonColor: '#3b82f6'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Servidor',
                        text: 'No se pudo conectar con el servidor',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            });
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