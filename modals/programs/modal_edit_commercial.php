<style>
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

<!-- Modal Editar Información Comercial -->
<div id="editCommercialModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Editar Información Comercial</h2>
            <button class="modal-close" onclick="closeEditCommercialModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <form id="editCommercialForm" enctype="multipart/form-data">
            <input type="hidden" name="program_id" id="commercial_program_id">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Saludo inicial</label>
                        <textarea name="initial_greeting" id="commercial_initial_greeting" 
                                  rows="3" placeholder="Mensaje de bienvenida del programa"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Beneficios</label>
                        <textarea name="benefits" id="commercial_benefits" 
                                  rows="4" placeholder="Beneficios del programa (uno por línea)"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Sinónimos (JSON)</label>
                        <textarea name="json_references" id="commercial_json_references" 
                                  rows="3" placeholder='["Sinónimo 1", "Sinónimo 2", "Sinónimo 3"]'></textarea>
                        <small style="color: #737373; font-size: 12px; margin-top: 4px;">Formato JSON: ["valor1", "valor2"]</small>
                    </div>
                </div>

                <!-- BROCHURE -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Brochure (PDF)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="brochure_file" id="brochure_file" accept=".pdf">
                            <div id="current_brochure" class="current-file"></div>
                        </div>
                    </div>
                </div>

                <!-- AUDIO -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Audio (MP3)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="voice_file" id="voice_file" accept=".mp3">
                            <div id="current_voice" class="current-file"></div>
                        </div>
                    </div>
                </div>

                <!-- VIDEO -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Video (MP4)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="video_file" id="video_file" accept=".mp4">
                            <div id="current_video" class="current-file"></div>
                        </div>
                    </div>
                </div>

                <!-- IMAGEN -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Imagen (JPG, PNG, GIF, WebP)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="img_file" id="img_file" accept="image/*">
                            <div id="current_img" class="current-file"></div>
                        </div>
                    </div>
                </div>

                <!-- PÁGINA DE VENTAS (URL) -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>URL de Página de Ventas</label>
                        <input type="url" name="sales_page_url" id="commercial_sales_page_url" 
                               placeholder="https://ejemplo.com/programa">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" style="color:black;" class="btn-secondary" onclick="closeEditCommercialModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editProgramcommercial(programId) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`actions/programs/get-program-commercial.php?id=${programId}`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    const d = data.data;
                    
                    document.getElementById('commercial_program_id').value = d.program_id;
                    document.getElementById('commercial_initial_greeting').value = d.initial_greeting || '';
                    document.getElementById('commercial_benefits').value = d.benefits || '';
                    document.getElementById('commercial_json_references').value = d.json_references || '';
                    document.getElementById('commercial_sales_page_url').value = d.sales_page_url || '';
                    
                    // Mostrar archivos actuales
                    showCurrentFile('current_brochure', d.brochure_url, 'Brochure actual');
                    showCurrentFile('current_voice', d.voice_url, 'Audio actual');
                    showCurrentFile('current_video', d.video_url, 'Video actual');
                    showCurrentFile('current_img', d.img_url, 'Imagen actual');
                    
                    document.getElementById('editCommercialModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo cargar la información',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar los datos',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
    }

    function showCurrentFile(elementId, fileUrl, label) {
        const element = document.getElementById(elementId);
        if (fileUrl && fileUrl.trim() !== '') {
            const filename = fileUrl.split('/').pop();
            element.innerHTML = `<i class="mdi mdi-file"></i> <a href="${fileUrl}" target="_blank">${label}: ${filename}</a>`;
            element.classList.add('has-file');
        } else {
            element.innerHTML = '';
            element.classList.remove('has-file');
        }
    }

    function closeEditCommercialModal() {
        document.getElementById('editCommercialModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('editCommercialForm').reset();
        
        // Limpiar preview de archivos
        ['current_brochure', 'current_voice', 'current_video', 'current_img'].forEach(id => {
            document.getElementById(id).classList.remove('has-file');
            document.getElementById(id).innerHTML = '';
        });
    }

    document.getElementById('editCommercialModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditCommercialModal();
        }
    });

    document.getElementById('editCommercialForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar JSON
        const jsonField = document.getElementById('commercial_json_references');
        if (jsonField.value.trim()) {
            try {
                JSON.parse(jsonField.value);
            } catch (e) {
                Swal.fire({
                    title: 'Error de formato',
                    text: 'Los sinónimos deben estar en formato JSON válido',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }
        }
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Guardando...',
            text: 'Subiendo archivos...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('actions/programs/edit-program-commercial.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Respuesta:', text);
                throw new Error('Respuesta inválida del servidor');
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Información comercial actualizada',
                    icon: 'success',
                    confirmButtonColor: '#1a1a1a'
                }).then(() => {
                    location.reload();
                });
            } else {
                let errorHTML = `<div style="text-align: left;">
                    <p style="margin-bottom: 12px;"><strong>${data.message || 'Error desconocido'}</strong></p>`;
                
                if (data.error_details) {
                    errorHTML += `<details style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px;">
                        <summary style="cursor: pointer; font-weight: 600; margin-bottom: 8px;">Ver detalles</summary>
                        <pre style="margin: 0; white-space: pre-wrap;">${data.error_details}</pre>
                    </details>`;
                }
                
                errorHTML += '</div>';
                
                Swal.fire({
                    title: 'Error',
                    html: errorHTML,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: error.message,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
    });
</script>