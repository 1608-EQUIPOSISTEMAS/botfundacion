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
        height: 36px;
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

    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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

    .form-group input::placeholder {
        color: #9ca3af;
    }

    /* File Input */
    .form-group input[type="file"] {
        padding: 8px 12px;
        cursor: pointer;
    }

    .form-group input[type="file"]::file-selector-button {
        padding: 8px 16px;
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 12px;
    }

    .form-group input[type="file"]::file-selector-button:hover {
        background: #e5e5e5;
    }

    .file-info {
        font-size: 13px;
        color: #737373;
        margin-top: 6px;
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

    .structure-section {
        margin-top: 20px;
        padding: 20px;
        background: #fafafa;
        border-radius: 12px;
        border: 1px solid #e5e5e5;
    }

    .structure-header-modal {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 16px;
    }

    .structure-header-modal i {
        font-size: 18px;
        color: #525252;
    }

    #coursesContainer {
        margin-top: 12px;
    }

    .course-select-group {
        margin-bottom: 16px;
    }

    .course-select-group:last-child {
        margin-bottom: 0;
    }

    .course-select-group label {
        font-size: 13px;
        font-weight: 600;
        color: #525252;
        margin-bottom: 8px;
        display: block;
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

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Modal Agregar Versión -->
<div id="modalAddVersion" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Agregar Nueva Versión</h2>
            <button class="modal-close" onclick="closeAddVersionModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <form id="formAddVersion" onsubmit="saveVersion(event)">
            <div class="modal-body">
                <input type="hidden" name="program_id" value="<?php echo $program_id; ?>">
                <input type="hidden" name="requires_structure" value="<?php echo $show_structure ? '1' : '0'; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Código de Versión *</label>
                        <input type="text" name="version_code" required placeholder="Ej: V1.0, 2024-01">
                    </div>

                    <div class="form-group">
                        <label>Sesiones *</label>
                        <input type="number" name="sessions" required min="1" placeholder="Número de sesiones">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Categoría Curso *</label>
                        <select name="cat_category_course" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            try {
                                $sql = "SELECT hh.catalog_id, hh.description FROM catalog pp
                                        INNER JOIN catalog hh ON hh.catalog_parent_id = pp.catalog_id AND hh.active = 1
                                        WHERE pp.alias = 'we_program_type' ORDER BY description";
                                $stmt = $pdo->query($sql);
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['catalog_id']}'>{$row['description']}</option>";
                                }
                            } catch(PDOException $e) {}
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Modalidad *</label>
                        <select name="cat_type_modality" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            try {
                                $sql = "SELECT hh.catalog_id, hh.description FROM catalog pp
                                        INNER JOIN catalog hh ON hh.catalog_parent_id = pp.catalog_id AND hh.active = 1
                                        WHERE pp.alias = 'we_modality_type' ORDER BY description DESC";
                                $stmt = $pdo->query($sql);
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['catalog_id']}'>{$row['description']}</option>";
                                }
                            } catch(PDOException $e) {}
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Brochure (PDF)</label>
                        <input type="file" name="brochure_pdf" accept=".pdf" onchange="updateFileName(this)">
                        <span class="file-info">Ningún archivo seleccionado</span>
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="activeToggleVersion" name="active" checked>
                            <label for="activeToggleVersion">
                                <span class="toggle-label">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <?php if ($show_structure): ?>
                <div class="structure-section">
                    <div class="structure-header-modal">
                        <i class="mdi mdi-file-tree"></i>
                        <span>Estructura del Programa</span>
                    </div>
                    
                    <div class="form-group">
                        <label>¿Cuántos cursos compondrán este programa? *</label>
                        <input type="number" id="num_courses" name="num_courses" 
                               min="1" max="10" value="1" onchange="generateCourseSelects()" 
                               placeholder="Entre 1 y 10 cursos" required
                               style="padding: 12px 16px; border: 1px solid #e5e5e5; border-radius: 10px; font-size: 15px;">
                    </div>

                    <div id="coursesContainer"></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="modal-footer">
                <button type="button" style="color:#525252;" class="btn-secondary" onclick="closeAddVersionModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Versión</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Abrir modal
    function openAddVersionModal() {
        document.getElementById('modalAddVersion').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        <?php if ($show_structure): ?>
        generateCourseSelects();
        <?php endif; ?>
    }

    // Cerrar modal
    function closeAddVersionModal() {
        document.getElementById('modalAddVersion').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('formAddVersion').reset();
        document.getElementById('coursesContainer').innerHTML = '';
        
        // Resetear info del archivo
        const fileInput = document.querySelector('input[name="brochure_pdf"]');
        if (fileInput) {
            const fileInfo = fileInput.nextElementSibling;
            if (fileInfo) {
                fileInfo.textContent = 'Ningún archivo seleccionado';
                fileInfo.style.color = '#737373';
            }
        }
    }

    // Actualizar nombre del archivo
    function updateFileName(input) {
        const fileInfo = input.nextElementSibling;
        if (input.files.length > 0) {
            fileInfo.textContent = input.files[0].name;
            fileInfo.style.color = '#1a1a1a';
        } else {
            fileInfo.textContent = 'Ningún archivo seleccionado';
            fileInfo.style.color = '#737373';
        }
    }

    // Generar selectores de cursos
    function generateCourseSelects() {
        const input = document.getElementById('num_courses');
        let value = parseInt(input.value);

        if (value > 10) {
            input.value = 10;
            Swal.fire({
                icon: 'warning',
                title: 'Límite alcanzado',
                text: 'El máximo permitido es 10 cursos.',
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'Entendido'
            });
            value = 10;
        } else if (value < 1) {
            input.value = 1;
            Swal.fire({
                icon: 'info',
                title: 'Valor no válido',
                text: 'Debe haber al menos 1 curso.',
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'Ok'
            });
            value = 1;
        }

        const container = document.getElementById('coursesContainer');
        container.innerHTML = '';
        
        for (let i = 1; i <= value; i++) {
            const div = document.createElement('div');
            div.className = 'course-select-group';
            div.innerHTML = `
                <label>Curso ${i} *</label>
                <select name="courses[]" required style="width: 100%; padding: 12px 16px; border: 1px solid #e5e5e5; border-radius: 10px; font-size: 15px; background: #fff; font-family: inherit;">
                    <option value="">Seleccionar curso...</option>
                    <?php
                    try {
                        $sql = "SELECT program_id, program_name FROM programs 
                                WHERE cat_type_program NOT IN (542, 544, 545, 891) 
                                ORDER BY program_name";
                        $stmt = $pdo->query($sql);
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['program_id']}'>{$row['program_name']}</option>";
                        }
                    } catch(PDOException $e) {}
                    ?>
                </select>
            `;
            container.appendChild(div);
        }
    }

    // Guardar versión
    function saveVersion(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        formData.set('active', document.getElementById('activeToggleVersion').checked ? '1' : '0');
        
        const btn = event.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Guardando...';
        
        Swal.fire({
            title: 'Guardando versión...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('actions/program-view/save_version.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Versión agregada correctamente',
                    icon: 'success',
                    confirmButtonColor: '#1a1a1a'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'No se pudo guardar la versión',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                btn.disabled = false;
                btn.textContent = 'Guardar Versión';
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            btn.disabled = false;
            btn.textContent = 'Guardar Versión';
        });
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAddVersion')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddVersionModal();
        }
    });
</script>