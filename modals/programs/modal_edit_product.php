<!-- Modal Editar Programa -->
<div id="editProgramModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Editar Programa</h2>
            <button class="modal-close" onclick="closeEditProgramModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <form id="editProgramForm">
            <input type="hidden" name="program_id" id="edit_program_id">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Nombre del programa *</label>
                        <input type="text" name="program_name" id="edit_program_name" required placeholder="Ej: Liderazgo Estratégico">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre comercial *</label>
                        <input type="text" name="commercial_name" id="edit_commercial_name" required placeholder="Ej: LIDERAZGO ESTRATÉGICO">
                    </div>

                    <div class="form-group">
                        <label>Nombre abreviado *</label>
                        <input type="text" name="abbreviation_name" id="edit_abbreviation_name" required placeholder="Ej: LID. ESTRATÉGICO">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Alias *</label>
                        <input type="text" name="alias" id="edit_alias" required placeholder="Ej: LE-CZ-01">
                    </div>

                    <div class="form-group">
                        <label>Horas certificadas *</label>
                        <input type="number" name="certified_hours" id="edit_certified_hours" required min="1" placeholder="40">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Línea de negocio *</label>
                        <select name="cat_category" id="edit_cat_category" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            try {
                                $sql = "SELECT hh.catalog_id, hh.description FROM catalog pp
                                        INNER JOIN catalog hh ON hh.catalog_parent_id = pp.catalog_id AND hh.active = 1
                                        WHERE pp.alias = 'we_program_category' ORDER BY description";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['catalog_id']}'>{$row['description']}</option>";
                                }
                            } catch(PDOException $e) {
                                error_log("Error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Categoría *</label>
                        <select name="cat_type_program" id="edit_cat_type_program" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            try {
                                $sql = "SELECT hh.catalog_id, hh.description FROM catalog pp
                                        INNER JOIN catalog hh ON hh.catalog_parent_id = pp.catalog_id AND hh.active = 1
                                        WHERE pp.alias = 'we_program_type' ORDER BY description";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['catalog_id']}'>{$row['description']}</option>";
                                }
                            } catch(PDOException $e) {
                                error_log("Error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Modalidad *</label>
                        <select name="cat_model_modality" id="edit_cat_model_modality" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            try {
                                $sql = "SELECT hh.catalog_id, hh.description FROM catalog pp
                                        INNER JOIN catalog hh ON hh.catalog_parent_id = pp.catalog_id AND hh.active = 1
                                        WHERE pp.alias = 'we_modality_type' ORDER BY description DESC";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['catalog_id']}'>{$row['description']}</option>";
                                }
                            } catch(PDOException $e) {
                                error_log("Error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="editActiveToggle" name="active">
                            <label for="editActiveToggle">
                                <span class="toggle-label">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" style="color:black;" class="btn-secondary" onclick="closeEditProgramModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editProgramproduct(programId) {
        // Mostrar loading
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Obtener datos del programa
        fetch(`actions/programs/get-program.php?id=${programId}`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Llenar el formulario con los datos
                    document.getElementById('edit_program_id').value = data.data.program_id;
                    document.getElementById('edit_program_name').value = data.data.program_name;
                    document.getElementById('edit_commercial_name').value = data.data.commercial_name || '';
                    document.getElementById('edit_abbreviation_name').value = data.data.abbreviation_name || '';
                    document.getElementById('edit_alias').value = data.data.alias || '';
                    document.getElementById('edit_certified_hours').value = data.data.certified_hours;
                    document.getElementById('edit_cat_category').value = data.data.cat_category;
                    document.getElementById('edit_cat_type_program').value = data.data.cat_type_program;
                    document.getElementById('edit_cat_model_modality').value = data.data.cat_model_modality;
                    document.getElementById('editActiveToggle').checked = data.data.active == 1;
                    
                    // Abrir modal
                    document.getElementById('editProgramModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo cargar el programa',
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
                    text: 'Error al cargar los datos del programa',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
    }

    function closeEditProgramModal() {
        document.getElementById('editProgramModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('editProgramForm').reset();
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('editProgramModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditProgramModal();
        }
    });

    // Envío del formulario de editar
    document.getElementById('editProgramForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('active', document.getElementById('editActiveToggle').checked ? '1' : '0');
        
        Swal.fire({
            title: 'Guardando cambios...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('actions/programs/edit-program.php', {
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
                    text: 'Programa actualizado correctamente',
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
                    title: 'Error al actualizar',
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