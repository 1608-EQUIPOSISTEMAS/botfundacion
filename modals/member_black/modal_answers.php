<style>
    /* ===== MODAL RESPUESTAS - DISEÑO MINIMALISTA ===== */
    
    #editarModalrespuestas .modal-dialog {
        max-width: 1000px;
        margin: 1.75rem auto;
    }

    #editarModalrespuestas .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s;
    }

    #editarModalrespuestas .modal-header-ux {
        padding: 24px 32px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #fff;
        border-radius: 16px 16px 0 0;
    }

    #editarModalrespuestas .modal-title-ux {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    #editarModalrespuestas .modal-subtitle-ux {
        font-size: 14px;
        color: #737373;
        margin: 4px 0 0 0;
        font-weight: 400;
    }

    #editarModalrespuestas .close-ux {
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
    }

    #editarModalrespuestas .close-ux:hover {
        background: #e5e5e5;
    }

    #editarModalrespuestas .close-ux i {
        font-size: 20px;
        color: #525252;
    }

    #editarModalrespuestas .modal-body-ux {
        padding: 32px;
        max-height: calc(90vh - 200px);
        overflow-y: auto;
        background: #ffffff;
    }

    #editarModalrespuestas .form-section-ux {
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #f5f5f5;
    }

    #editarModalrespuestas .form-section-ux:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    #editarModalrespuestas .form-section-ux h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #editarModalrespuestas .form-section-ux h3 i {
        font-size: 18px;
        color: #525252;
    }

    #editarModalrespuestas .form-grid-ux {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    #editarModalrespuestas .form-group-ux {
        display: flex;
        flex-direction: column;
    }

    #editarModalrespuestas .form-label-ux {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #editarModalrespuestas .form-label-ux i {
        font-size: 16px;
        color: #525252;
    }

    #editarModalrespuestas .form-control-ux {
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        background: #fff;
        color: #1a1a1a;
        transition: all 0.2s;
        font-family: inherit;
    }

    #editarModalrespuestas .form-control-ux:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
    }

    #editarModalrespuestas .form-control-ux::placeholder {
        color: #a3a3a3;
    }

    #editarModalrespuestas .form-control-ux:disabled {
        background: #f5f5f5;
        color: #737373;
        cursor: not-allowed;
    }

    #editarModalrespuestas select.form-control-ux {
        cursor: pointer;
    }

    #editarModalrespuestas textarea.form-control-ux {
        resize: vertical;
        line-height: 1.6;
    }

    #editarModalrespuestas .textarea-ux-lg {
        min-height: 90px;
    }

    #editarModalrespuestas .form-hint-ux {
        font-size: 13px;
        color: #737373;
        margin-top: 6px;
    }

    #editarModalrespuestas .modal-footer-ux {
        padding: 20px 32px;
        border-top: 1px solid #e5e5e5;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #fff;
        border-radius: 0 0 16px 16px;
    }

    #editarModalrespuestas .btn-secondary-ux {
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

    #editarModalrespuestas .btn-secondary-ux:hover {
        background: #e5e5e5;
    }

    #editarModalrespuestas .btn-primary-ux {
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

    #editarModalrespuestas .btn-primary-ux:hover {
        background: #000;
        transform: translateY(-1px);
    }

    @media (max-width: 992px) {
        #editarModalrespuestas .form-grid-ux {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        #editarModalrespuestas .modal-body-ux,
        #editarModalrespuestas .modal-header-ux,
        #editarModalrespuestas .modal-footer-ux {
            padding: 20px;
        }

        #editarModalrespuestas .modal-footer-ux {
            flex-direction: column-reverse;
            gap: 12px;
        }

        #editarModalrespuestas .btn-secondary-ux,
        #editarModalrespuestas .btn-primary-ux {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Modal Gestionar Respuestas -->
<div class="modal fade" id="editarModalrespuestas" tabindex="-1" role="dialog" aria-labelledby="editarModalRespuestasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            
            <div class="modal-header-ux">
                <div>
                    <h5 class="modal-title-ux" id="editarModalRespuestasLabel">Gestionar Respuestas de las Opciones</h5>
                    <p class="modal-subtitle-ux">Define qué responderá el bot cuando el usuario seleccione cada opción (1-4).</p>
                </div>
                <button type="button" class="close-ux" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>

            <div class="modal-body-ux">
                <form id="formGestionarRespuestas">
                    
                    <input type="hidden" id="member-id-respuestas" name="member_id">

                    <!-- Respuesta Opción 1 -->
                    <div class="form-section-ux">
                        <h3><i class="mdi mdi-numeric-1-circle"></i> Respuesta a Opción 1</h3>
                        
                        <input type="hidden" name="respuestas[0][id]" id="respuesta-id-1">
                        <input type="hidden" name="respuestas[0][opcion_numero]" value="1">
                        
                        <div class="form-grid-ux">
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-shape-outline"></i>
                                    Tipo de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <select class="form-control-ux" name="respuestas[0][tipo_respuesta]" id="tipo-respuesta-1" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="texto">Texto simple</option>
                                    <option value="horario">Respuesta con horario</option>
                                    <option value="submenu">Submenú (Métodos de pago)</option>
                                </select>
                                <small class="form-hint-ux">Define el comportamiento de esta respuesta</small>
                            </div>
                            
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-message-text-outline"></i>
                                    Mensaje de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea 
                                    class="form-control-ux textarea-ux-lg" 
                                    name="respuestas[0][mensaje]" 
                                    id="mensaje-1"
                                    placeholder="Mensaje que enviará el bot..."
                                    required
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Respuesta Opción 2 -->
                    <div class="form-section-ux">
                        <h3><i class="mdi mdi-numeric-2-circle"></i> Respuesta a Opción 2</h3>
                        
                        <input type="hidden" name="respuestas[1][id]" id="respuesta-id-2">
                        <input type="hidden" name="respuestas[1][opcion_numero]" value="2">
                        
                        <div class="form-grid-ux">
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-shape-outline"></i>
                                    Tipo de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <select class="form-control-ux" name="respuestas[1][tipo_respuesta]" id="tipo-respuesta-2" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="texto">Texto simple</option>
                                    <option value="horario">Respuesta con horario</option>
                                    <option value="submenu">Submenú (Métodos de pago)</option>
                                </select>
                            </div>
                            
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-message-text-outline"></i>
                                    Mensaje de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea 
                                    class="form-control-ux textarea-ux-lg" 
                                    name="respuestas[1][mensaje]" 
                                    id="mensaje-2"
                                    placeholder="Mensaje que enviará el bot..."
                                    required
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Respuesta Opción 3 -->
                    <div class="form-section-ux">
                        <h3><i class="mdi mdi-numeric-3-circle"></i> Respuesta a Opción 3</h3>
                        
                        <input type="hidden" name="respuestas[2][id]" id="respuesta-id-3">
                        <input type="hidden" name="respuestas[2][opcion_numero]" value="3">
                        
                        <div class="form-grid-ux">
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-shape-outline"></i>
                                    Tipo de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <select class="form-control-ux" name="respuestas[2][tipo_respuesta]" id="tipo-respuesta-3" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="texto">Texto simple</option>
                                    <option value="horario">Respuesta con horario</option>
                                    <option value="submenu">Submenú (Métodos de pago)</option>
                                </select>
                            </div>
                            
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-message-text-outline"></i>
                                    Mensaje de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea 
                                    class="form-control-ux textarea-ux-lg" 
                                    name="respuestas[2][mensaje]" 
                                    id="mensaje-3"
                                    placeholder="Mensaje que enviará el bot..."
                                    required
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Respuesta Opción 4 -->
                    <div class="form-section-ux">
                        <h3><i class="mdi mdi-numeric-4-circle"></i> Respuesta a Opción 4</h3>
                        
                        <input type="hidden" name="respuestas[3][id]" id="respuesta-id-4">
                        <input type="hidden" name="respuestas[3][opcion_numero]" value="4">
                        
                        <div class="form-grid-ux">
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-shape-outline"></i>
                                    Tipo de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <select class="form-control-ux" name="respuestas[3][tipo_respuesta]" id="tipo-respuesta-4" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="texto">Texto simple</option>
                                    <option value="horario">Respuesta con horario</option>
                                    <option value="submenu">Submenú (Métodos de pago)</option>
                                </select>
                            </div>
                            
                            <div class="form-group-ux">
                                <label class="form-label-ux">
                                    <i class="mdi mdi-message-text-outline"></i>
                                    Mensaje de Respuesta <span style="color:#ef4444;">*</span>
                                </label>
                                <textarea 
                                    class="form-control-ux textarea-ux-lg" 
                                    name="respuestas[3][mensaje]" 
                                    id="mensaje-4"
                                    placeholder="Mensaje que enviará el bot..."
                                    required
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer-ux">
                <button type="button" class="btn-secondary-ux" data-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                    Cancelar
                </button>
                <button type="button" class="btn-primary-ux" onclick="submitRespuestasForm()">
                    <i class="mdi mdi-content-save"></i>
                    Guardar Respuestas
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        // Reglas de bloqueo para tipo "horario"
        function aplicarReglasRespuestas() {
            for (let i = 1; i <= 4; i++) {
                const select = document.getElementById(`tipo-respuesta-${i}`);
                const textarea = document.getElementById(`mensaje-${i}`);

                if (!select || !textarea) continue;

                const toggleTextarea = () => {
                    if (select.value === "horario") {
                        textarea.disabled = true;
                        textarea.value = "⚠️ Este mensaje se gestiona desde el apartado de horarios.";
                    } else {
                        textarea.disabled = false;
                        if (textarea.value.includes("⚠️ Este mensaje se gestiona desde")) {
                            textarea.value = "";
                        }
                    }
                };

                toggleTextarea();
                select.addEventListener("change", toggleTextarea);
            }
        }

        // Cargar respuestas cuando se abre el modal
        $('#editarModalrespuestas').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const memberId = button.data('id');
            
            if (!memberId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener el ID del plan',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            $('#member-id-respuestas').val(memberId);
            
            // Limpiar campos primero
            for (let i = 1; i <= 4; i++) {
                $(`#respuesta-id-${i}`).val('');
                $(`#tipo-respuesta-${i}`).val('');
                $(`#mensaje-${i}`).val('').prop('disabled', false);
            }
            
            Swal.fire({
                title: 'Cargando...',
                text: 'Obteniendo respuestas configuradas',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'actions/members/get-answers.php',
                type: 'GET',
                data: { member_id: memberId },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    
                    if (response.success && response.data && response.data.length > 0) {
                        response.data.forEach(respuesta => {
                            const num = respuesta.opcion_numero;
                            $(`#respuesta-id-${num}`).val(respuesta.id);
                            $(`#tipo-respuesta-${num}`).val(respuesta.tipo_respuesta);
                            $(`#mensaje-${num}`).val(respuesta.mensaje);
                        });
                        
                        aplicarReglasRespuestas();
                        
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({
                                icon: 'success',
                                title: 'Respuestas cargadas'
                            });
                        }
                    } else {
                        aplicarReglasRespuestas();
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error al cargar respuestas:', error);
                    aplicarReglasRespuestas();
                }
            });
        });
        
        // Aplicar reglas cuando el modal se muestra completamente
        $('#editarModalrespuestas').on('shown.bs.modal', function () {
            aplicarReglasRespuestas();
        });
    });

    // Guardar respuestas
    function submitRespuestasForm() {
        const form = document.getElementById('formGestionarRespuestas');
        
        // **CRÍTICO: Habilitar todos los textareas antes de enviar**
        for (let i = 1; i <= 4; i++) {
            const textarea = document.getElementById(`mensaje-${i}`);
            if (textarea && textarea.disabled) {
                textarea.disabled = false;
            }
        }
        
        const formData = new FormData(form);
        
        // Validar campos
        let vacios = false;
        let errorMsg = '';
        
        for (let i = 1; i <= 4; i++) {
            const tipoSelect = $(`#tipo-respuesta-${i}`);
            const mensajeTextarea = $(`#mensaje-${i}`);
            
            if (!tipoSelect.val()) {
                vacios = true;
                errorMsg = `Falta seleccionar el tipo de respuesta ${i}`;
                break;
            }
            
            const mensajeVal = mensajeTextarea.val().trim();
            
            // Solo validar mensaje si NO es el texto de advertencia
            if (!mensajeVal || mensajeVal.includes('⚠️ Este mensaje se gestiona desde')) {
                // Si es tipo horario, poner un mensaje placeholder válido
                if (tipoSelect.val() === 'horario') {
                    mensajeTextarea.val('HORARIO_GESTIONADO');
                } else {
                    vacios = true;
                    errorMsg = `Falta el mensaje de la respuesta ${i}`;
                    break;
                }
            }
        }
        
        if (vacios) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: errorMsg || 'Completa todos los campos requeridos',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        // DEBUG: Ver qué se está enviando
        console.log('=== DATOS A ENVIAR ===');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        Swal.fire({
            title: '¿Guardar respuestas?',
            text: 'Se actualizarán las respuestas para cada opción del plan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, Guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Procesando respuestas...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: 'actions/members/edit-answers.php',
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
                                text: response.message || 'Respuestas actualizadas correctamente.',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000
                            }).then(() => {
                                $('#editarModalrespuestas').modal('hide');
                                location.reload();
                            });
                        } else {
                            let errorHTML = `<div style="text-align: left;">
                                <p style="margin-bottom: 12px;"><strong>${response.message || 'Error desconocido'}</strong></p>`;
                            
                            if (response.error_type) {
                                errorHTML += `<p style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Tipo: ${response.error_type}</p>`;
                            }
                            
                            if (response.error_details) {
                                errorHTML += `<details style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px;">
                                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: 8px;">Ver detalles técnicos</summary>
                                    <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word; font-family: monospace;">${response.error_details}</pre>
                                </details>`;
                            }
                            
                            errorHTML += '</div>';
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al guardar',
                                html: errorHTML,
                                confirmButtonColor: '#3b82f6',
                                width: '500px'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        
                        let errorDetails = '';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorDetails = response.message || response.error || 'Error desconocido';
                        } catch (e) {
                            errorDetails = xhr.responseText || error;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Servidor',
                            html: `
                                <div style="text-align: left;">
                                    <p style="margin-bottom: 12px;"><strong>No se pudo completar la operación</strong></p>
                                    <details style="padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px;">
                                        <summary style="cursor: pointer; font-weight: 600; margin-bottom: 8px;">Ver error técnico</summary>
                                        <div style="margin-top: 8px;">
                                            <p style="margin-bottom: 8px;"><strong>Status:</strong> ${status}</p>
                                            <p style="margin-bottom: 8px;"><strong>HTTP Code:</strong> ${xhr.status}</p>
                                            <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word; max-height: 200px; overflow-y: auto;">${errorDetails}</pre>
                                        </div>
                                    </details>
                                </div>
                            `,
                            confirmButtonColor: '#3b82f6',
                            width: '600px'
                        });
                    }
                });
            }
        });
    }
</script>