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
</style>

<!-- Modal Agregar Programa -->
<div id="addProgramModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Nuevo Programa</h2>
            <button class="modal-close" onclick="closeAddProgramModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <form id="addProgramForm">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group full-width">
                        <label>Nombre del programa *</label>
                        <input type="text" name="program_name" required placeholder="Ej: Liderazgo Estratégico">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre comercial *</label>
                        <input type="text" name="commercial_name" required placeholder="Ej: LIDERAZGO ESTRATÉGICO">
                    </div>

                    <div class="form-group">
                        <label>Nombre abreviado *</label>
                        <input type="text" name="abbreviation_name" required placeholder="Ej: LID. ESTRATÉGICO">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Alias *</label>
                        <input type="text" name="alias" required placeholder="Ej: LE-CZ-01">
                    </div>

                    <div class="form-group">
                        <label>Horas certificadas *</label>
                        <input type="number" name="certified_hours" required min="1" placeholder="40">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Línea de negocio *</label>
                        <select name="cat_category" required>
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
                        <select name="cat_type_program" required>
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
                        <select name="cat_model_modality" required>
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
                        <label>Número de sesiones *</label>
                        <input type="number" name="sessions" required min="1" placeholder="8">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Código de versión *</label>
                        <input type="text" name="version_code" required placeholder="V1.0" value="V1.0">
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="activeToggle" name="active" checked>
                            <label for="activeToggle">
                                <span class="toggle-label">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="color:black;" class="btn-secondary" onclick="closeAddProgramModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Crear programa</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddProgramModal() {
        document.getElementById('addProgramModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAddProgramModal() {
        document.getElementById('addProgramModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('addProgramForm').reset();
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('addProgramModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddProgramModal();
        }
    });

    // Envío del formulario
    document.getElementById('addProgramForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('active', document.getElementById('activeToggle').checked ? '1' : '0');
        
        // Mostrar loading
        Swal.fire({
            title: 'Creando programa...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('actions/programs/add-program.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Verificar si la respuesta es OK
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status} - ${response.statusText}`);
            }
            return response.text();
        })
        .then(text => {
            // Intentar parsear como JSON
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Respuesta recibida:', text);
                throw new Error('La respuesta del servidor no es JSON válido');
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message || 'Programa creado correctamente',
                    icon: 'success',
                    confirmButtonColor: '#1a1a1a'
                }).then(() => {
                    location.reload();
                });
            } else {
                // Construir mensaje de error detallado
                let errorHTML = `<div style="text-align: left;">
                    <p style="margin-bottom: 12px;"><strong>${data.message || 'Error desconocido'}</strong></p>`;
                
                if (data.error_type) {
                    errorHTML += `<p style="color: #737373; font-size: 13px; margin-bottom: 8px;">Tipo: ${data.error_type}</p>`;
                }
                
                if (data.error_details) {
                    errorHTML += `<details style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px;">
                        <summary style="cursor: pointer; font-weight: 600; margin-bottom: 8px;">Ver detalles técnicos</summary>
                        <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word;">${data.error_details}</pre>
                    </details>`;
                }
                
                if (data.error_code) {
                    errorHTML += `<p style="color: #737373; font-size: 11px; margin-top: 8px;">Código: ${data.error_code}</p>`;
                }
                
                errorHTML += '</div>';
                
                Swal.fire({
                    title: 'Error al crear programa',
                    html: errorHTML,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    width: '500px'
                });
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            
            Swal.fire({
                title: 'Error de conexión',
                html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 12px;">No se pudo procesar la solicitud</p>
                        <details style="padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px;">
                            <summary style="cursor: pointer; font-weight: 600; margin-bottom: 8px;">Ver error técnico</summary>
                            <pre style="margin: 0; white-space: pre-wrap;">${error.message}</pre>
                        </details>
                    </div>
                `,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
    });
</script>