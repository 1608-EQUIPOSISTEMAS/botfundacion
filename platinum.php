<?php
session_start();

// Incluir la conexión a la base de datos
require_once 'conexion/conexion.php';

/**
 * Función para convertir texto con formato WhatsApp a HTML.
 * Soporta: *negrita*, _cursiva_, ~tachado~ y ```monospace```.
 * También protege contra inyección de código (XSS).
 */
function formatWhatsappText($text) {
    // 1. Escapar cualquier HTML para seguridad.
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // 2. Convertir los formatos de WhatsApp a etiquetas HTML.
    $text = preg_replace('/\*([^\*]+)\*/', '<b>$1</b>', $text); // Negrita
    $text = preg_replace('/\_([^_]+)\_/', '<i>$1</i>', $text);   // Cursiva
    $text = preg_replace('/\~([^\~]+)\~/', '<s>$1</s>', $text); // Tachado
    $text = preg_replace('/```([^`]+)```/', '<code>$1</code>', $text); // Monospace

    // 3. Convertir saltos de línea a <br> para párrafos.
    $text = nl2br($text);

    return $text;
}


// --- Obtener los datos del plan (hardcoded a id = 1) ---
try {
    $sql = "SELECT id, nombre, ruta_post, beneficio, ruta_pdf, precio FROM members WHERE id = 3 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombreplan = $member ? htmlspecialchars($member['nombre']) : 'Plan no encontrado';
    
    // --- [MEJORA UX] Procesar beneficios para mostrarlos como lista ---
    $benefits_list = [];
    if ($member && !empty($member['beneficio'])) {
        $benefits_list = array_filter(array_map('trim', explode("\n", $member['beneficio'])));
    }

} catch (PDOException $e) {
    $member = null;
    $error_message = "Error al obtener los datos del plan: " . $e->getMessage();
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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family: 'Inter', sans-serif;
            --bg-main: #F4F7FE;
            --bg-card: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --border-color: #E2E8F0;
            --color-primary: #4F46E5;
            --color-primary-light: #C7D2FE;
            --color-success: #10B981;
            --border-radius: 16px;
            --shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -2px rgb(0 0 0 / 0.07);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--bg-main);
            background-image: radial-gradient(var(--border-color) 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-primary);
            font-family: var(--font-family);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .content-wrapper {
            padding: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .plan-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: flex-start;
        }

        .plan-main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .plan-sidebar-assets {
            position: sticky;
            top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .plan-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        .plan-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }
        .plan-card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .plan-card-header h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .plan-card-body {
            padding: 1.5rem;
        }

        /* --- ESTILOS DEL HERO MÁS COMPACTO --- */
        .plan-hero {
            padding: 2rem;
            border-radius: var(--border-radius);
            background: linear-gradient(45deg, #ffffffff, #ffffffff);
            border: 1px solid var(--border-color);
        }
        .plan-hero h1 {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.02em;
        }
        .plan-hero .price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0 0 1.5rem 0;
        }

        /* --- LISTA DE BENEFICIOS CON ESTILOS PARA CÓDIGO --- */
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .benefits-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.6;
        }
        .benefits-list li i {
            color: var(--color-success);
            font-size: 1.4rem;
            margin-top: 2px;
        }
        /* Estilo para el texto monospace (```) */
        .benefits-list code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .asset-preview {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        .asset-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .file-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: var(--font-family);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-primary {
            background-color: var(--color-primary);
            color: white;
        }
        .btn-primary:hover {
            background-color: #4338CA;
            color: white;
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #F1F5F9;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background: #E2E8F0;
        }
        
        @media (max-width: 1024px) {
            .plan-layout {
                grid-template-columns: 1fr;
            }
            .plan-sidebar-assets {
                position: static;
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
                <div class="content-wrapper" style="background: none;">
                    
                    <?php if ($member): ?>
                        <div class="plan-layout">
                            <div class="plan-main-content">
                                <section class="plan-hero">
                                    <div class="plan-card-header" style="padding: 0; padding-bottom: 1rem;">
                                    <h3>
                                        <i class="mdi mdi-cash-multiple" style="color: #10B981;"></i>
                                        PRECIO DEL <?php echo htmlspecialchars($member['nombre']); ?>
                                    </h3>
                                    </div>

                                    <p class="price" style="padding-top: 1rem;"><?php echo formatWhatsappText($member['precio']); ?></p>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#editarModal"
                                        data-id="<?= htmlspecialchars($member['id']) ?>"
                                        data-nombre="<?= htmlspecialchars($member['nombre']) ?>"
                                        data-precio="<?= htmlspecialchars($member['precio']) ?>"
                                        data-ruta-post="<?= htmlspecialchars($member['ruta_post']) ?>"
                                        data-beneficio="<?= htmlspecialchars($member['beneficio']) ?>"
                                        data-ruta-pdf="<?= htmlspecialchars($member['ruta_pdf']) ?>">
                                        <i class="mdi mdi-pencil-outline"></i>
                                        Gestionar Plan
                                    </button>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#editarModalopciones"
                                        data-id="<?= htmlspecialchars($member['id']) ?>">
                                        <i class="mdi mdi-pencil-outline"></i>
                                        Gestionar Opciones
                                    </button>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#editarModalrespuestas"
                                        data-id="<?= htmlspecialchars($member['id']) ?>">
                                        <i class="mdi mdi-pencil-outline"></i>
                                        Gestionar Respuestas
                                    </button>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#editarModalPago"
                                        data-id="<?= htmlspecialchars($member['id']) ?>">
                                        <i class="mdi mdi-pencil-outline"></i>
                                        Gestionar Pagos
                                    </button>
                                </section>

                                <section class="plan-card">
                                    <div class="plan-card-header">
                                        <h3><i class="mdi mdi-star-circle-outline" style="color: #FBBF24;"></i>Beneficios Incluidos</h3>
                                    </div>
                                    <div class="plan-card-body">
                                        <?php if (!empty($benefits_list)): ?>
                                            <ul class="benefits-list">
                                                <?php foreach ($benefits_list as $benefit): ?>
                                                    <li>
                                                        <i class="mdi mdi-check-circle-outline"></i>
                                                        <span><?php echo formatWhatsappText($benefit); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-secondary">No hay beneficios detallados para este plan.</p>
                                        <?php endif; ?>
                                    </div>
                                </section>
                            </div>

                            <aside class="plan-sidebar-assets">
                                <section class="plan-card">
                                    <div class="plan-card-header">
                                        <h3><i class="mdi mdi-image-outline"></i>Imagen Promocional</h3>
                                    </div>
                                    <div class="plan-card-body">
                                        <div class="asset-preview">
                                            <img src="<?php echo htmlspecialchars($member['ruta_post']); ?>" alt="Vista previa del plan" loading="lazy">
                                        </div>
                                    </div>
                                </section>

                                <section class="plan-card">
                                    <div class="plan-card-header">
                                        <h3><i class="mdi mdi-file-pdf-box-outline"></i>Documento del Plan</h3>
                                    </div>
                                    <div class="plan-card-body">
                                        <div class="file-actions">
                                            <a href="<?php echo htmlspecialchars($member['ruta_pdf']); ?>" target="_blank" class="btn btn-primary">
                                                <i class="mdi mdi-eye-outline"></i> Ver
                                            </a>
                                            <a href="<?php echo htmlspecialchars($member['ruta_pdf']); ?>" download class="btn btn-primary">
                                                <i class="mdi mdi-download-outline"></i> Descargar
                                            </a>
                                        </div>
                                    </div>
                                </section>
                            </aside>
                        </div>
                    <?php else: ?>
                        <?php endif; ?>
                </div>
                <?php include 'includes/footer.php'; ?>
            </main>
        </div>
    </div>

    <?php include 'modals/platinum/editar.php'; ?>
    <?php include 'modals/platinum/modal_opciones.php'; ?>
    <?php include 'modals/platinum/modal_respuestas.php'; ?>
    
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        <?php if (isset($error_message)): ?>
        Swal.fire({
            title: 'Error de Base de Datos',
            text: '<?php echo addslashes($error_message); ?>',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        <?php endif; ?>

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

        // Event listener para el submit del formulario del modal.
        // Asumiendo que el formulario tiene un id="editForm" y se llama a esta función en el onsubmit.
        window.handleEditSubmit = function(event) {
            event.preventDefault();
            
            Swal.fire({
                title: '¿Confirmar cambios?',
                text: "La información del plan será actualizada.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // --- RECOMENDACIÓN: Aquí iría la lógica AJAX para enviar el formulario ---
                    // Por ahora, simulamos el éxito como en el código original.
                    Swal.fire({
                        title: 'Guardando...',
                        text: 'Por favor, espera.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    setTimeout(() => {
                        $('#editarModal').modal('hide');
                        Swal.close();
                        Toast.fire({
                            icon: 'success',
                            title: 'Plan actualizado con éxito'
                        }).then(() => {
                            location.reload(); // Recargar para ver los cambios
                        });
                    }, 1500);
                }
            });
        }
        
        // Vista previa de imagen ampliada
        const imagePreview = document.querySelector('.image-preview');
        if (imagePreview) {
            imagePreview.addEventListener('click', function() {
                Swal.fire({
                    imageUrl: this.src,
                    imageAlt: 'Vista ampliada de la imagen del plan',
                    showConfirmButton: false,
                    showCloseButton: true,
                    backdrop: `rgba(0,0,0,0.8)`
                });
            });
        }
    });
    </script>
</body>
</html>