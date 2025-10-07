<style>
    /* ===== OVERRIDE DE BOOTSTRAP MODAL CON DISEÑO MINIMALISTA ===== */
    
    /* Modal backdrop */
    .modal-backdrop.show {
        opacity: 0.5;
    }

    /* Modal dialog - Bootstrap lo centra, solo ajustamos tamaño */
    #editarModal .modal-dialog {
        max-width: 800px;
        margin: 1.75rem auto;
    }

    /* Modal content - Aquí va el diseño minimalista */
    #editarModal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Header */
    #editarModal .modal-header {
        padding: 24px 32px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #fff;
        border-radius: 16px 16px 0 0;
    }

    #editarModal .modal-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
        flex: 1;
    }

    #editarModal .modal-subtitle {
        font-size: 14px;
        color: #737373;
        margin: 4px 0 0 0;
        font-weight: 400;
        display: block;
    }

    #editarModal .close {
        width: 36px;
        height: 36px;
        border: none;
        background: #f5f5f5;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
        opacity: 1;
        margin: 0;
        padding: 0;
    }

    #editarModal .close:hover {
        background: #e5e5e5;
        opacity: 1;
    }

    #editarModal .close i {
        font-size: 20px;
        color: #525252;
    }

    /* Body - Con scroll controlado */
    #editarModal .modal-body {
        padding: 32px;
        max-height: calc(90vh - 200px);
        overflow-y: auto;
    }

    /* Form Structure */
    .form-section-members {
        margin-bottom: 32px;
    }

    .form-section-members:last-child {
        margin-bottom: 0;
    }

    .form-section-members h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-members h3 i {
        font-size: 18px;
        color: #525252;
    }

    .form-grid-members {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group-members {
        display: flex;
        flex-direction: column;
    }

    .form-group-members.full-width {
        grid-column: 1 / -1;
    }

    /* Labels */
    .form-label-members {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label-members i {
        font-size: 16px;
        color: #525252;
    }

    /* Inputs & Textareas */
    .form-control-members {
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        background: #fff;
        color: #1a1a1a;
        transition: all 0.2s;
        font-family: inherit;
        resize: vertical;
    }

    .form-control-members:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
    }

    .form-control-members::placeholder {
        color: #a3a3a3;
    }

    .textarea-lg-members {
        min-height: 120px;
        line-height: 1.6;
    }

    #member-beneficio.textarea-lg-members {
        min-height: 180px;
    }

    .form-hint-members {
        font-size: 13px;
        color: #737373;
        margin-top: 6px;
    }

    /* File Upload */
    .file-upload-wrapper-members {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 16px;
        background: #ffffff; /* Fondo blanco sólido */
        border: 2px dashed #d4d4d4;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .file-upload-wrapper-members:hover {
        border-color: #a3a3a3;
        background: #fafafa;
    }

    .file-input-display-members {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        color: #737373;
        cursor: not-allowed;
    }

    .btn-upload-members {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #fff;
        color: #1a1a1a;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
        white-space: nowrap;    
    }

    .btn-upload-members:hover {
        background: #f5f5f5;
        border-color: #1a1a1a;
    }

    .btn-upload-members i {
        font-size: 16px;
    }

    /* Previews */
    .preview-container-members {
    margin-top: 12px;
    padding: 12px 16px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff; /* Fondo blanco */
    }

    .image-preview-members {
        max-width: 80px;
        height: auto;
        border-radius: 6px;
        object-fit: cover;
    }

    .badge-file-members {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #1a1a1a;
        font-size: 14px;
        font-weight: 500;
    }

    .badge-file-members i {
        color: #22c55e;
    }

    /* Footer */
    #editarModal .modal-footer {
        padding: 20px 32px;
        border-top: 1px solid #e5e5e5;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #fff;
        border-radius: 0 0 16px 16px;
    }

    /* Buttons */
    .btn-secondary-members {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #f5f5f5;
        color: #525252;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary-members:hover {
        background: #e5e5e5;
    }

    .btn-primary-members {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: #1a1a1a;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary-members:hover {
        background: #000;
        transform: translateY(-1px);
    }

    #editarModal .modal-body {
        padding: 32px;
        max-height: calc(90vh - 200px);
        overflow-y: auto;
        background: #ffffff; /* Fondo blanco explícito */
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid-members {
            grid-template-columns: 1fr;
        }

        #editarModal .modal-body,
        #editarModal .modal-header,
        #editarModal .modal-footer {
            padding: 20px;
        }

        #editarModal .modal-footer {
            flex-direction: column-reverse;
            gap: 12px;
        }

        .btn-secondary-members,
        .btn-primary-members {
            width: 100%;
            justify-content: center;
        }

        .file-upload-wrapper-members {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-upload-members {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <div style="flex: 1;">
                    <h5 class="modal-title" id="editarModalLabel">Editar Plan de Suscripción</h5>
                    <span class="modal-subtitle">Administra la información clave, precio y beneficios de este plan.</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="formEditarPlan" enctype="multipart/form-data">
                    
                    <input type="hidden" id="member-id" name="id">

                    <div class="form-section-members">
                        <h3><i class="mdi mdi-information-outline"></i> Detalles del Plan</h3>

                        <div class="form-grid-members">
                            
                            <div class="form-group-members">
                                <label class="form-label-members" for="member-nombre">
                                    <i class="mdi mdi-tag-outline"></i>
                                    Nombre del Plan <span style="color:#ef4444;">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-members" 
                                    id="member-nombre" 
                                    name="nombre" 
                                    placeholder="Ej: Plan Premium Black"
                                    required
                                >
                            </div>

                            <div class="form-group-members">
                                <label class="form-label-members" for="member-precio">
                                    <i class="mdi mdi-currency-usd"></i>
                                    Precio y Frecuencia <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea
                                    class="form-control-members textarea-lg-members"
                                    id="member-precio"
                                    name="precio"
                                    placeholder="Ej: $99.00 / mes"
                                    required
                                    rows="3"
                                ></textarea>
                                <small class="form-hint-members">Texto exacto de cómo se mostrará el precio.</small>
                            </div>

                            <div class="form-group-members full-width">
                                <label class="form-label-members" for="member-beneficio">
                                    <i class="mdi mdi-star-outline"></i>
                                    Lista de Beneficios <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea 
                                    class="form-control-members textarea-lg-members" 
                                    id="member-beneficio" 
                                    name="beneficio" 
                                    rows="8"
                                    placeholder="Escribe cada beneficio en una línea separada"
                                    required
                                ></textarea>
                                <small class="form-hint-members">Campo amplio para facilitar la edición de listas extensas.</small>
                            </div>

                        </div>
                    </div>

                    <div class="form-section-members">
                        <h3><i class="mdi mdi-attachment"></i> Archivos Multimedia</h3>
                        
                        <div class="form-group-members full-width" style="margin-bottom: 20px;">
                            <label class="form-label-members" for="member-ruta-post">
                                <i class="mdi mdi-image-outline"></i>
                                Imagen de Portada
                            </label>
                            <div class="file-upload-wrapper-members">
                                <input 
                                    type="text" 
                                    class="file-input-display-members" 
                                    id="member-ruta-post" 
                                    name="ruta_post_display" 
                                    placeholder="Ningún archivo seleccionado"
                                    readonly
                                >
                                <label for="upload-image" class="btn-upload-members">
                                    <i class="mdi mdi-cloud-upload"></i>
                                    Seleccionar
                                </label>
                                <input 
                                    type="file" 
                                    id="upload-image" 
                                    name="imagen" 
                                    accept="image/*"
                                    style="display: none;"
                                    onchange="handleImageUpload(this)"
                                >
                            </div>
                            <small class="form-hint-members">JPG, PNG, WEBP (Máx. 5MB)</small>
                            
                            <div id="image-preview-container" class="preview-container-members" style="display: none;">
                                <img id="image-preview" src="" alt="Preview" class="image-preview-members">
                                <p class="form-hint-members" style="margin:0; color:#1a1a1a; font-weight:500;">Imagen cargada</p>
                            </div>
                        </div>

                        <div class="form-group-members full-width">
                            <label class="form-label-members" for="member-ruta-pdf">
                                <i class="mdi mdi-file-pdf-outline"></i>
                                Documento PDF
                            </label>
                            <div class="file-upload-wrapper-members">
                                <input 
                                    type="text" 
                                    class="file-input-display-members" 
                                    id="member-ruta-pdf" 
                                    name="ruta_pdf_display" 
                                    placeholder="Ningún archivo seleccionado"
                                    readonly
                                >
                                <label for="upload-pdf" class="btn-upload-members">
                                    <i class="mdi mdi-cloud-upload"></i>
                                    Seleccionar
                                </label>
                                <input 
                                    type="file" 
                                    id="upload-pdf" 
                                    name="pdf" 
                                    accept=".pdf"
                                    style="display: none;"
                                    onchange="handlePdfUpload(this)"
                                >
                            </div>
                            <small class="form-hint-members">PDF (Máx. 10MB)</small>
                            
                            <div id="pdf-info" class="preview-container-members" style="display: none;">
                                <span class="badge-file-members">
                                    <i class="mdi mdi-check-circle"></i>
                                    <span id="pdf-name"></span>
                                </span>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-members" data-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                    Cancelar
                </button>
                <button type="button" class="btn-primary-members" onclick="submitEditForm()">
                    <i class="mdi mdi-content-save"></i>
                    Guardar Cambios
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>    
    $('#editarModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const planId = button.data('id');
        
        $('#image-preview-container').hide();
        $('#image-preview').attr('src', '');
        $('#pdf-info').hide();
        $('#pdf-name').text('');
        $('#upload-image').val('');
        $('#upload-pdf').val('');
        
        if (!planId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo obtener el ID del plan',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
        
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: 'actions/members/get-member.php',
            type: 'GET',
            data: { id: planId },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    const data = response.data;
                    
                    $('#member-id').val(data.id);
                    $('#member-nombre').val(data.nombre);
                    $('#member-precio').val(data.precio);
                    $('#member-beneficio').val(data.beneficio);
                    $('#member-ruta-post').val(data.ruta_post);
                    $('#member-ruta-pdf').val(data.ruta_pdf);
                    
                    if (data.ruta_post && data.ruta_post.trim() !== '') {
                        $('#image-preview').attr('src', data.ruta_post);
                        $('#image-preview-container').show();
                    } 
                    
                    if (data.ruta_pdf && data.ruta_pdf.trim() !== '') {
                        const pdfName = data.ruta_pdf.substring(data.ruta_pdf.lastIndexOf('/') + 1);
                        $('#pdf-name').text(pdfName || 'Documento enlazado');
                        $('#pdf-info').show();
                    } 
                    
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({
                            icon: 'success',
                            title: 'Datos cargados'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron cargar los datos',
                        confirmButtonColor: '#1a1a1a'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#1a1a1a'
                });
            }
        });
    });

    function handleImageUpload(input) {
        const file = input.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'La imagen no debe superar los 5MB',
                    confirmButtonColor: '#1a1a1a'
                });
                input.value = '';
                $('#member-ruta-post').val('');
                $('#image-preview-container').hide();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview-container').show();
            };
            reader.readAsDataURL(file);
            $('#member-ruta-post').val(file.name);

            if (typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: 'Imagen cargada: ' + file.name
                });
            }
        }
    }

    function handlePdfUpload(input) {
        const file = input.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El PDF no debe superar los 10MB',
                    confirmButtonColor: '#1a1a1a'
                });
                input.value = '';
                $('#member-ruta-pdf').val('');
                $('#pdf-info').hide();
                return;
            }

            $('#member-ruta-pdf').val(file.name);
            $('#pdf-name').text(file.name);
            $('#pdf-info').show();

            if (typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: 'PDF cargado: ' + file.name
                });
            }
        }
    }

    function submitEditForm() {
        const form = document.getElementById('formEditarPlan');
        const formData = new FormData(form);
        
        const nombre = $('#member-nombre').val().trim();
        const precio = $('#member-precio').val().trim();
        const beneficio = $('#member-beneficio').val().trim();
        
        if (!nombre || !precio || !beneficio) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos marcados con (*)',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Guardar cambios?',
            text: 'Se actualizará la configuración del plan',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor: '#737373',
            confirmButtonText: 'Sí, Guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: 'actions/members/edit-member.php',
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
                                title: '¡Actualizado!',
                                text: response.message || 'Plan actualizado correctamente',
                                confirmButtonColor: '#1a1a1a',
                                timer: 2000
                            }).then(() => {
                                $('#editarModal').modal('hide'); 
                                location.reload(); 
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo actualizar el plan',
                                confirmButtonColor: '#1a1a1a'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Servidor',
                            text: 'No se pudo comunicar con el servidor',
                            confirmButtonColor: '#1a1a1a'
                        });
                    }
                });
            }
        });
    }
</script>