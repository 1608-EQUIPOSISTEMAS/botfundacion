<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.2s;
    }

    .swal2-container {
        z-index: 9999999 !important;
    }


    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-container {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 24px 32px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .modal-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .modal-close {
        width: 36px;
        height: 20px;
        border: none;
        background: #f5f5f5;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #e5e5e5;
    }

    .modal-close i {
        font-size: 20px;
        color: #525252;
    }

    .modal-body {
        padding: 32px;
        overflow-y: auto;
        max-height: calc(90vh - 160px);
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select {
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        background: #fff;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
    }

    .toggle-switch {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-switch input[type="checkbox"] {
        position: relative;
        width: 48px;
        height: 28px;
        appearance: none;
        background: #e5e5e5;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .toggle-switch input[type="checkbox"]:checked {
        background: #22c55e;
    }

    .toggle-switch input[type="checkbox"]::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        top: 3px;
        left: 3px;
        transition: all 0.3s;
    }

    .toggle-switch input[type="checkbox"]:checked::before {
        left: 23px;
    }

    .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: #525252;
    }

    .modal-footer {
        padding: 20px 32px;
        border-top: 1px solid #e5e5e5;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
        background: #fff;
        margin-top: auto;
    }

    .btn-primary,
    .btn-secondary {
        padding: 12px 28px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #1a1a1a;
        color: #fff;
    }

    .btn-primary:hover {
        background: #000;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #525252;
    }

    .btn-secondary:hover {
        background: #e5e5e5;
    }

     textarea {
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        background: #fff;
        transition: all 0.2s;
        font-family: inherit;
        resize: vertical;
        width: 100%;
    }

    textarea:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
    }

    .file-input-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    input[type="file"] {
        padding: 12px 16px;
        border: 2px dashed #e5e5e5;
        border-radius: 10px;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.2s;
    }

    input[type="file"]:hover {
        border-color: #1a1a1a;
        background: #f5f5f5;
    }

    .current-file {
        font-size: 13px;
        color: #737373;
        padding: 8px 12px;
        background: #f0f9ff;
        border-radius: 6px;
        display: none;
    }

    .current-file.has-file {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .current-file i {
        color: #0284c7;
    }

    .current-file a {
        color: #0284c7;
        text-decoration: none;
        font-weight: 500;
    }

    .current-file a:hover {
        text-decoration: underline;
    }
</style>

<div id="addMessageModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Nuevo Mensaje</h2>
            <button class="modal-close" onclick="closeAddMessageModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <form id="addMessageForm">
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Tipo de mensaje <span style="color: #ef4444;">*</span></label>
                        <select name="message_type_id" id="message_type_id" required>
                            <option value="">Selecciona un tipo...</option>
                            <?php foreach ($message_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" 
                                    data-allows-content="<?php echo $type['allows_content']; ?>"
                                    data-requires-media="<?php echo $type['requires_media']; ?>">
                                <?php echo htmlspecialchars($type['description']); ?> 
                                (<?php echo strtoupper($type['type_code']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small id="messageTypeHelp" style="color: #737373; font-size: 12px; margin-top: 4px; display: none;"></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width" id="contentGroup">
                        <label>Contenido del mensaje</label>
                        <textarea name="content" id="message_content" 
                                  rows="4" 
                                  placeholder="Escribe el contenido del mensaje aquí..."></textarea>
                        <small style="color: #737373; font-size: 12px; margin-top: 4px;">
                            Puedes usar variables: {nombre}, {apellido}, {telefono}
                        </small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Delay (segundos)</label>
                        <input type="number" name="delay_seconds" id="delay_seconds" 
                               min="0" max="300" value="2" required>
                        <small style="color: #737373; font-size: 12px; margin-top: 4px;">
                            Tiempo de espera antes de enviar este mensaje
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Estado inicial</label>
                        <select name="is_active" id="is_active" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Notas internas (opcional)</label>
                        <textarea name="notes" id="message_notes" 
                                  rows="2" 
                                  placeholder="Notas internas para el equipo (no se envían al usuario)"></textarea>
                    </div>
                </div>

                <div id="mediaRequiredAlert" class="form-row" style="display: none;">
                    <div style="padding: 12px 16px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <i class="mdi mdi-alert" style="font-size: 20px; color: #f59e0b;"></i>
                            <div>
                                <strong style="color: #92400e; display: block; margin-bottom: 4px;">Archivos multimedia requeridos</strong>
                                <small style="color: #78350f; font-size: 13px;">
                                    Este tipo de mensaje requiere archivos multimedia. Podrás agregarlos después de crear el mensaje.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddMessageModal()">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="mdi mdi-check"></i>
                    Crear mensaje
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        function openNewMessageModal() {
            document.getElementById('addMessageModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Reset del formulario
            document.getElementById('addMessageForm').reset();
            document.getElementById('contentGroup').style.display = 'flex';
            document.getElementById('messageTypeHelp').style.display = 'none';
            document.getElementById('mediaRequiredAlert').style.display = 'none';
        }

        function closeAddMessageModal() {
            document.getElementById('addMessageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('addMessageModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddMessageModal();
            }
        });

        // Manejar cambio de tipo de mensaje
        document.getElementById('message_type_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const allowsContent = selectedOption.dataset.allowsContent === '1';
            const requiresMedia = selectedOption.dataset.requiresMedia === '1';
            
            const contentGroup = document.getElementById('contentGroup');
            const messageContent = document.getElementById('message_content');
            const messageTypeHelp = document.getElementById('messageTypeHelp');
            const mediaRequiredAlert = document.getElementById('mediaRequiredAlert');
            
            // Mostrar/ocultar campo de contenido
            if (allowsContent) {
                contentGroup.style.display = 'flex';
                messageContent.required = false;
            } else {
                contentGroup.style.display = 'none';
                messageContent.required = false;
                messageContent.value = '';
            }
            
            // Mostrar alerta si requiere archivos multimedia
            if (requiresMedia) {
                mediaRequiredAlert.style.display = 'block';
                messageTypeHelp.textContent = '⚠️ Este tipo de mensaje requiere archivos multimedia';
                messageTypeHelp.style.display = 'block';
                messageTypeHelp.style.color = '#f59e0b';
            } else {
                mediaRequiredAlert.style.display = 'none';
                messageTypeHelp.style.display = 'none';
            }
        });

        // Envío del formulario
        document.getElementById('addMessageForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creando mensaje...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('actions/messages/add-message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Mensaje creado!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#1a1a1a'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo crear el mensaje',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al crear el mensaje',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        });
</script>