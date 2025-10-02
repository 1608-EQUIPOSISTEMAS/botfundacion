<!-- Modal Editar Bot Foundation -->
<div class="modal fade" id="editarBotModal" tabindex="-1" role="dialog" aria-labelledby="editarBotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            
            <!-- Header -->
            <div class="modal-header modal-header-modern">
                <div>
                    <h5 class="modal-title modal-title-modern" id="editarBotModalLabel">Editar Configuración</h5>
                    <p class="modal-subtitle-modern">Actualiza la información del Bot Foundation</p>
                </div>
                <button type="button" class="close btn-close-modern" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="mdi mdi-close"></i></span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body modal-body-modern">
                <form id="formEditarBot" enctype="multipart/form-data">
                    
                    <input type="hidden" id="bot-id" name="id">

                    <!-- Mensajes -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="mdi mdi-message-text-outline"></i>
                            <span>Mensajes</span>
                        </div>

                        <div class="form-row-modern">
                            <div class="form-group-modern full">
                                <label class="label-modern">Mensaje de Bienvenida</label>
                                <textarea 
                                    class="input-modern form-control" 
                                    id="bot-welcome" 
                                    name="welcome" 
                                    rows="3"
                                    placeholder="Escribe el mensaje de bienvenida..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Recursos Visuales -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="mdi mdi-image-multiple-outline"></i>
                            <span>Recursos Visuales</span>
                        </div>

                        <div class="form-row-modern">
                            <!-- Presentación -->
                            <div class="form-group-modern">
                                <label class="label-modern">
                                    <span>Imagen Presentación</span>
                                    <span class="label-badge">Imagen</span>
                                </label>
                                <div class="file-input-wrapper">
                                    <input 
                                        type="file" 
                                        id="upload-presentation" 
                                        name="presentation_image" 
                                        accept="image/*"
                                        class="file-input-hidden"
                                        onchange="handleFileUpload(this, 'presentation-preview')"
                                    >
                                    <label for="upload-presentation" class="file-input-label">
                                        <i class="mdi mdi-cloud-upload-outline"></i>
                                        <span class="file-input-text">Seleccionar imagen</span>
                                    </label>
                                </div>
                                <input 
                                    type="text" 
                                    class="input-path" 
                                    id="bot-presentation-route" 
                                    placeholder="Ruta actual..."
                                    readonly
                                >
                                <div id="presentation-preview" class="image-preview-box">
                                    <img src="" alt="Preview">
                                </div>
                            </div>

                            <!-- Modalidad 1 -->
                            <div class="form-group-modern">
                                <label class="label-modern">
                                    <span>Modalidad 1</span>
                                    <span class="label-badge">Imagen</span>
                                </label>
                                <div class="file-input-wrapper">
                                    <input 
                                        type="file" 
                                        id="upload-modality1" 
                                        name="modality_first_image" 
                                        accept="image/*"
                                        class="file-input-hidden"
                                        onchange="handleFileUpload(this, 'modality1-preview')"
                                    >
                                    <label for="upload-modality1" class="file-input-label">
                                        <i class="mdi mdi-cloud-upload-outline"></i>
                                        <span class="file-input-text">Seleccionar imagen</span>
                                    </label>
                                </div>
                                <input 
                                    type="text" 
                                    class="input-path" 
                                    id="bot-modality-first-route" 
                                    placeholder="Ruta actual..."
                                    readonly
                                >
                                <div id="modality1-preview" class="image-preview-box">
                                    <img src="" alt="Preview">
                                </div>
                            </div>

                            <!-- Modalidad 2 -->
                            <div class="form-group-modern">
                                <label class="label-modern">
                                    <span>Modalidad 2</span>
                                    <span class="label-badge">Imagen</span>
                                </label>
                                <div class="file-input-wrapper">
                                    <input 
                                        type="file" 
                                        id="upload-modality2" 
                                        name="modality_second_image" 
                                        accept="image/*"
                                        class="file-input-hidden"
                                        onchange="handleFileUpload(this, 'modality2-preview')"
                                    >
                                    <label for="upload-modality2" class="file-input-label">
                                        <i class="mdi mdi-cloud-upload-outline"></i>
                                        <span class="file-input-text">Seleccionar imagen</span>
                                    </label>
                                </div>
                                <input 
                                    type="text" 
                                    class="input-path" 
                                    id="bot-modality-second-route" 
                                    placeholder="Ruta actual..."
                                    readonly
                                >
                                <div id="modality2-preview" class="image-preview-box">
                                    <img src="" alt="Preview">
                                </div>
                            </div>

                            <!-- Inversión -->
                            <div class="form-group-modern">
                                <label class="label-modern">
                                    <span>Inversión</span>
                                    <span class="label-badge">Imagen</span>
                                </label>
                                <div class="file-input-wrapper">
                                    <input 
                                        type="file" 
                                        id="upload-inversion" 
                                        name="inversion_image" 
                                        accept="image/*"
                                        class="file-input-hidden"
                                        onchange="handleFileUpload(this, 'inversion-preview')"
                                    >
                                    <label for="upload-inversion" class="file-input-label">
                                        <i class="mdi mdi-cloud-upload-outline"></i>
                                        <span class="file-input-text">Seleccionar imagen</span>
                                    </label>
                                </div>
                                <input 
                                    type="text" 
                                    class="input-path" 
                                    id="bot-inversion-route" 
                                    placeholder="Ruta actual..."
                                    readonly
                                >
                                <div id="inversion-preview" class="image-preview-box">
                                    <img src="" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brochure PDF -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="mdi mdi-file-pdf-box"></i>
                            <span>Brochure</span>
                        </div>

                        <div class="form-row-modern">
                            <div class="form-group-modern full">
                                <label class="label-modern">
                                    <span>Brochure PDF</span>
                                    <span class="label-badge badge-pdf">PDF</span>
                                </label>
                                <div class="file-input-wrapper">
                                    <input 
                                        type="file" 
                                        id="upload-brochure" 
                                        name="brochure_pdf" 
                                        accept=".pdf"
                                        class="file-input-hidden"
                                        onchange="handlePdfUpload(this, 'brochure-info')"
                                    >
                                    <label for="upload-brochure" class="file-input-label pdf">
                                        <i class="mdi mdi-file-pdf-box"></i>
                                        <span class="file-input-text">Seleccionar PDF</span>
                                    </label>
                                </div>
                                <input 
                                    type="text" 
                                    class="input-path" 
                                    id="bot-brochure-route" 
                                    placeholder="Ruta actual del PDF..."
                                    readonly
                                >
                                <div id="brochure-info" class="pdf-info-box">
                                    <i class="mdi mdi-check-circle"></i>
                                    <span class="pdf-name"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="mdi mdi-text-box-outline"></i>
                            <span>Información Adicional</span>
                        </div>

                        <div class="form-row-modern">
                            <div class="form-group-modern">
                                <label class="label-modern">Sesión</label>
                                <textarea 
                                    class="input-modern form-control" 
                                    id="bot-sesion" 
                                    name="sesion" 
                                    rows="3"
                                    placeholder="Información sobre las sesiones..."
                                ></textarea>
                            </div>

                            <div class="form-group-modern">
                                <label class="label-modern">Palabras Clave</label>
                                <textarea 
                                    class="input-modern form-control" 
                                    id="bot-key-words" 
                                    name="key_words" 
                                    rows="3"
                                    placeholder="Palabras clave..."
                                ></textarea>
                            </div>
                        </div>

                        <div class="form-row-modern">
                            <div class="form-group-modern full">
                                <label class="label-modern">Mensaje Final</label>
                                <textarea 
                                    class="input-modern form-control" 
                                    id="bot-final-text" 
                                    name="final_text" 
                                    rows="3"
                                    placeholder="Mensaje final del bot..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer modal-footer-modern">
                <button type="button" class="btn btn-cancel-modern" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-save-modern" onclick="submitBotForm()">
                    <i class="mdi mdi-check"></i>
                    Guardar Cambios
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* Modal Base */

.modal {
    z-index: 99999 !important;
}
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.modal-header-modern {
    padding: 24px 32px;
    border-bottom: 1px solid #e5e5e5;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    background: #fafafa;
}

.modal-title-modern {
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.modal-subtitle-modern {
    font-size: 14px;
    color: #737373;
    margin: 4px 0 0 0;
}

.btn-close-modern {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #737373;
}

.btn-close-modern:hover {
    background: #e5e5e5;
    color: #1a1a1a;
}

.btn-close-modern i {
    font-size: 20px;
}

/* Modal Body */
.modal-body-modern {
    padding: 32px;
    max-height: 70vh;
    overflow-y: auto;
}

.modal-body-modern::-webkit-scrollbar {
    width: 8px;
}

.modal-body-modern::-webkit-scrollbar-thumb {
    background: #d4d4d4;
    border-radius: 4px;
}

/* Form Sections */
.form-section {
    margin-bottom: 32px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #737373;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f5f5f5;
}

.section-header i {
    font-size: 18px;
}

/* Form Layout */
.form-row-modern {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group-modern {
    display: flex;
    flex-direction: column;
}

.form-group-modern.full {
    grid-column: 1 / -1;
}

.label-modern {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.label-badge {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 2px 8px;
    background: #f0f4ff;
    color: #0066ff;
    border-radius: 4px;
}

.label-badge.badge-pdf {
    background: #fef2f2;
    color: #ef4444;
}

/* Inputs */
.input-modern {
    padding: 12px 14px;
    font-size: 14px;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    background: #fff;
    color: #1a1a1a;
    transition: all 0.2s;
    font-family: inherit;
    resize: vertical;
}

.input-modern:focus {
    outline: none;
    border-color: #1a1a1a;
    box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
}

.input-modern::placeholder {
    color: #a3a3a3;
}

.input-path {
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 11px;
    padding: 8px 12px;
    background: #fafafa;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    color: #737373;
    margin-top: 8px;
}

/* File Inputs */
.file-input-hidden {
    display: none;
}

.file-input-wrapper {
    margin-bottom: 8px;
}

.file-input-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    background: #fff;
    border: 2px dashed #e5e5e5;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    color: #737373;
    font-size: 14px;
    font-weight: 500;
}

.file-input-label:hover {
    border-color: #1a1a1a;
    background: #fafafa;
    color: #1a1a1a;
}

.file-input-label.pdf:hover {
    border-color: #ef4444;
    background: #fef2f2;
    color: #ef4444;
}

.file-input-label i {
    font-size: 20px;
}

/* Image Preview */
.image-preview-box {
    display: none;
    margin-top: 12px;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 8px;
    background: #fafafa;
}

.image-preview-box img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 6px;
}

/* PDF Info */
.pdf-info-box {
    display: none;
    margin-top: 12px;
    padding: 10px 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    color: #22c55e;
    font-size: 13px;
    font-weight: 500;
    align-items: center;
    gap: 8px;
}

.pdf-info-box i {
    font-size: 16px;
}

/* Footer */
.modal-footer-modern {
    padding: 20px 32px;
    border-top: 1px solid #e5e5e5;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #fafafa;
}

.btn-cancel-modern {
    padding: 10px 24px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    color: #525252;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel-modern:hover {
    background: #fafafa;
    border-color: #1a1a1a;
}

.btn-save-modern {
    padding: 10px 28px;
    background: #1a1a1a;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-save-modern:hover {
    background: #000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-save-modern i {
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-header-modern,
    .modal-body-modern,
    .modal-footer-modern {
        padding: 20px;
    }

    .form-row-modern {
        grid-template-columns: 1fr;
    }

    .modal-footer-modern {
        flex-direction: column-reverse;
    }

    .btn-cancel-modern,
    .btn-save-modern {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Cargar datos al abrir modal
    $('#editarBotModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const botId = button.data('id');
        
        if (!botId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo obtener el ID',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
        
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        $.ajax({
            url: 'acciones/fundacion/obtener.php',
            type: 'GET',
            data: { id: botId },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    const data = response.data;
                    
                    $('#bot-id').val(data.id);
                    $('#bot-welcome').val(data.welcome);
                    $('#bot-sesion').val(data.sesion);
                    $('#bot-key-words').val(data.key_words);
                    $('#bot-final-text').val(data.final_text);
                    
                    $('#bot-presentation-route').val(data.presentation_route);
                    $('#bot-brochure-route').val(data.brochure_route);
                    $('#bot-modality-first-route').val(data.modality_first_route);
                    $('#bot-modality-second-route').val(data.modality_second_route);
                    $('#bot-inversion-route').val(data.inversion_route);
                    
                    if (data.presentation_route) {
                        $('#presentation-preview img').attr('src', data.presentation_route);
                        $('#presentation-preview').show();
                    }
                    if (data.modality_first_route) {
                        $('#modality1-preview img').attr('src', data.modality_first_route);
                        $('#modality1-preview').show();
                    }
                    if (data.modality_second_route) {
                        $('#modality2-preview img').attr('src', data.modality_second_route);
                        $('#modality2-preview').show();
                    }
                    if (data.inversion_route) {
                        $('#inversion-preview img').attr('src', data.inversion_route);
                        $('#inversion-preview').show();
                    }
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    confirmButtonColor: '#1a1a1a'
                });
            }
        });
    });
});

// Handle Image Upload
function handleFileUpload(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'La imagen no debe superar los 5MB',
            confirmButtonColor: '#1a1a1a'
        });
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        $('#' + previewId + ' img').attr('src', e.target.result);
        $('#' + previewId).show();
    };
    reader.readAsDataURL(file);
}

// Handle PDF Upload
function handlePdfUpload(input, infoId) {
    const file = input.files[0];
    if (!file) return;
    
    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El PDF no debe superar los 10MB',
            confirmButtonColor: '#1a1a1a'
        });
        input.value = '';
        return;
    }

    $('#' + infoId + ' .pdf-name').text(file.name);
    $('#' + infoId).show();
}
</script>