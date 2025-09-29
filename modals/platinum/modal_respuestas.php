<!-- Modal Gestionar Respuestas -->
<div class="modal fade" id="editarModalrespuestas" tabindex="-1" role="dialog" aria-labelledby="editarModalRespuestasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content modal-ux">
            
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
                                    Tipo de Respuesta <span style="color:red;">*</span>
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
                                    Mensaje de Respuesta <span style="color:red;">*</span>
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
                                    Tipo de Respuesta <span style="color:red;">*</span>
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
                                    Mensaje de Respuesta <span style="color:red;">*</span>
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
                                    Tipo de Respuesta <span style="color:red;">*</span>
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
                                    Mensaje de Respuesta <span style="color:red;">*</span>
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
                                    Tipo de Respuesta <span style="color:red;">*</span>
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
                                    Mensaje de Respuesta <span style="color:red;">*</span>
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
    
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo respuestas configuradas',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: 'acciones/members/obtener_respuestas.php',
        type: 'GET',
        data: { member_id: memberId },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                // Limpiar campos
                for (let i = 1; i <= 4; i++) {
                    $(`#respuesta-id-${i}`).val('');
                    $(`#tipo-respuesta-${i}`).val('');
                    $(`#mensaje-${i}`).val('');
                }
                
                // Llenar con datos
                if (response.data && response.data.length > 0) {
                    response.data.forEach(respuesta => {
                        const num = respuesta.opcion_numero;
                        $(`#respuesta-id-${num}`).val(respuesta.id);
                        $(`#tipo-respuesta-${num}`).val(respuesta.tipo_respuesta);
                        $(`#mensaje-${num}`).val(respuesta.mensaje);
                    });
                }
                
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'success',
                        title: 'Respuestas cargadas correctamente'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin respuestas',
                    text: 'Este plan aún no tiene respuestas configuradas.',
                    confirmButtonColor: '#3b82f6'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonColor: '#3b82f6'
            });
        }
    });
});

// Guardar respuestas
function submitRespuestasForm() {
    const form = document.getElementById('formGestionarRespuestas');
    const formData = new FormData(form);
    
    // Validar campos
    let vacios = false;
    for (let i = 1; i <= 4; i++) {
        if (!$(`#tipo-respuesta-${i}`).val() || !$(`#mensaje-${i}`).val().trim()) {
            vacios = true;
            break;
        }
    }
    
    if (vacios) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Completa el tipo y mensaje para las 4 respuestas',
            confirmButtonColor: '#3b82f6'
        });
        return;
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
                url: 'acciones/members/guardar_respuestas.php',
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudieron guardar las respuestas',
                            confirmButtonColor: '#3b82f6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Servidor',
                        text: 'Ocurrió un error al intentar comunicar con el servidor',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            });
        }
    });
}
});

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
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
                        textarea.value = ""; // limpiar si venía del bloqueo
                    }
                }
            };

            // Ejecutar en caso de que ya venga con valor
            toggleTextarea();

            // Escuchar cambios
            select.addEventListener("change", toggleTextarea);
        }
    }

    // Ejecutar al cargar la página
    aplicarReglasRespuestas();

    // También cuando se abra el modal
    $('#editarModalrespuestas').on('shown.bs.modal', function () {
        aplicarReglasRespuestas();
    });
});
</script>
