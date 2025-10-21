<!-- Modal Gestión de Multimedia -->
<div id="manageMediaModal" class="modal-overlay" style="display: none;">
    <div class="modal-container media-modal">
        <div class="modal-header">
            <div>
                <h2>Gestión de Archivos Multimedia</h2>
                <p style="font-size: 13px; color: #737373; margin-top: 4px;">Mensaje #<span id="media_message_id_display"></span></p>
            </div>
            <button class="modal-close" onclick="closeManageMediaModal()">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        
        <input type="hidden" id="media_message_id">
        <input type="hidden" id="media_message_type">
        
        <div class="modal-body">
            <!-- Tab Navigation -->
            <div class="media-tabs">
                <button class="media-tab active" data-type="IMAGE" onclick="switchMediaTab('IMAGE')">
                    <i class="mdi mdi-image"></i>
                    Imágenes
                    <span class="tab-count" id="count_IMAGE">0</span>
                </button>
                <button class="media-tab" data-type="AUDIO" onclick="switchMediaTab('AUDIO')">
                    <i class="mdi mdi-music"></i>
                    Audios
                    <span class="tab-count" id="count_AUDIO">0</span>
                </button>
                <button class="media-tab" data-type="DOCUMENT" onclick="switchMediaTab('DOCUMENT')">
                    <i class="mdi mdi-file-document"></i>
                    Documentos
                    <span class="tab-count" id="count_DOCUMENT">0</span>
                </button>
                <button class="media-tab" data-type="VIDEO" onclick="switchMediaTab('VIDEO')">
                    <i class="mdi mdi-video"></i>
                    Videos
                    <span class="tab-count" id="count_VIDEO">0</span>
                </button>
            </div>

            <!-- Upload Area -->
            <div class="upload-area" id="uploadArea">
                <input type="file" id="mediaFileInput" style="display: none;" multiple>
                <div class="upload-content" onclick="document.getElementById('mediaFileInput').click()">
                    <i class="mdi mdi-cloud-upload" style="font-size: 48px; color: #d4d4d4;"></i>
                    <h3 style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin: 12px 0 4px 0;">
                        Arrastra archivos aquí o haz clic para seleccionar
                    </h3>
                    <p style="font-size: 13px; color: #737373;" id="uploadAreaHint">
                        Imágenes: JPG, PNG, GIF, WebP (máx. 5MB cada una)
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div id="uploadProgress" class="upload-progress" style="display: none;">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <p style="font-size: 13px; color: #737373; margin-top: 8px; text-align: center;" id="progressText">
                    Subiendo archivos...
                </p>
            </div>

            <!-- Media Grid -->
            <div class="media-grid" id="mediaGrid">
                <!-- Los archivos se cargarán dinámicamente aquí -->
            </div>

            <!-- Empty State -->
            <div class="empty-media-state" id="emptyMediaState" style="display: none;">
                <i class="mdi mdi-image-off" style="font-size: 64px; color: #e5e5e5;"></i>
                <p style="font-size: 15px; color: #737373; margin-top: 16px;">
                    No hay archivos multimedia
                </p>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeManageMediaModal()">Cerrar</button>
        </div>
    </div>
</div>

<style>
    .media-modal {
        max-width: 900px;
    }

    /* Tabs */
    .media-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        padding: 4px;
        background: #f9fafb;
        border-radius: 10px;
    }

    .media-tab {
        flex: 1;
        padding: 12px 16px;
        border: none;
        background: transparent;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #737373;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
    }

    .media-tab:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .media-tab.active {
        background: #1a1a1a;
        color: #fff;
    }

    .media-tab i {
        font-size: 18px;
    }

    .tab-count {
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .media-tab.active .tab-count {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Upload Area */
    .upload-area {
        margin-bottom: 24px;
        border: 2px dashed #e5e5e5;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }

    .upload-area:hover {
        border-color: #1a1a1a;
        background: #f5f5f5;
    }

    .upload-area.drag-over {
        border-color: #1a1a1a;
        background: #f0f9ff;
    }

    .upload-content {
        pointer-events: none;
    }

    /* Progress */
    .upload-progress {
        margin-bottom: 24px;
    }

    .progress-bar {
        height: 6px;
        background: #f5f5f5;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #1a1a1a 0%, #525252 100%);
        width: 0%;
        transition: width 0.3s ease;
    }

    /* Media Grid */
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .media-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f9fafb;
        border: 1px solid #e5e5e5;
        transition: all 0.2s;
        aspect-ratio: 1;
    }

    .media-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #1a1a1a;
    }

    .media-item.image {
        aspect-ratio: 1;
    }

    .media-item.audio,
    .media-item.document,
    .media-item.video {
        aspect-ratio: 16 / 9;
    }

    .media-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-icon-preview {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px;
    }

    .media-icon-preview i {
        font-size: 48px;
        color: #737373;
    }

    .media-icon-preview .file-name {
        font-size: 12px;
        color: #525252;
        font-weight: 500;
        text-align: center;
        word-break: break-word;
        line-height: 1.3;
    }

    .media-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .media-item:hover .media-actions {
        opacity: 1;
    }

    .media-action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.95);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .media-action-btn:hover {
        transform: scale(1.1);
    }

    .media-action-btn.view {
        background: rgba(14, 165, 233, 0.95);
    }

    .media-action-btn.view i {
        color: #fff;
        font-size: 16px;
    }

    .media-action-btn.delete {
        background: rgba(239, 68, 68, 0.95);
    }

    .media-action-btn.delete i {
        color: #fff;
        font-size: 16px;
    }

    .media-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 8px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
        color: #fff;
        font-size: 11px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .media-item:hover .media-info {
        opacity: 1;
    }

    /* Empty State */
    .empty-media-state {
        text-align: center;
        padding: 60px 20px;
    }

    /* Audio Player */
    .audio-player {
        width: 100%;
        padding: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .media-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
        }

        .media-tabs {
            flex-wrap: wrap;
        }

        .media-tab {
            min-width: calc(50% - 4px);
        }
    }
</style>

<script>
    // Variables globales para media
let currentMediaType = 'IMAGE';
let currentMessageId = null;

// Abrir modal de gestión de media
function manageMedia(messageId) {
    currentMessageId = messageId;
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener información del mensaje y sus archivos
    fetch(`actions/messages/get-message-media.php?message_id=${messageId}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                document.getElementById('media_message_id').value = messageId;
                document.getElementById('media_message_id_display').textContent = messageId;
                document.getElementById('media_message_type').value = data.message_type;
                
                // Actualizar contadores
                updateMediaCounts(data.media);
                
                // Mostrar modal
                document.getElementById('manageMediaModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Cargar archivos del tipo actual
                loadMediaForType(currentMediaType, data.media);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
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
                text: 'No se pudo cargar la información',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
}

function closeManageMediaModal() {
    document.getElementById('manageMediaModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentMessageId = null;
    currentMediaType = 'IMAGE';
}

// Cerrar modal al hacer clic fuera
document.getElementById('manageMediaModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeManageMediaModal();
    }
});

// Cambiar entre tipos de media
function switchMediaTab(type) {
    currentMediaType = type;
    
    // Actualizar tabs
    document.querySelectorAll('.media-tab').forEach(tab => {
        if (tab.dataset.type === type) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Actualizar hint del área de upload
    updateUploadHint(type);
    
    // Actualizar accept del input
    updateFileInputAccept(type);
    
    // Recargar archivos
    if (currentMessageId) {
        fetch(`actions/messages/get-message-media.php?message_id=${currentMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadMediaForType(type, data.media);
                }
            });
    }
}

function updateUploadHint(type) {
    const hints = {
        'IMAGE': 'Imágenes: JPG, PNG, GIF, WebP (máx. 5MB cada una)',
        'AUDIO': 'Audios: MP3, WAV, OGG (máx. 10MB cada uno)',
        'DOCUMENT': 'Documentos: PDF, DOC, DOCX, XLS, XLSX (máx. 10MB cada uno)',
        'VIDEO': 'Videos: MP4, MOV, AVI (máx. 50MB cada uno)'
    };
    
    document.getElementById('uploadAreaHint').textContent = hints[type] || '';
}

function updateFileInputAccept(type) {
    const accepts = {
        'IMAGE': 'image/jpeg,image/png,image/gif,image/webp',
        'AUDIO': 'audio/mpeg,audio/wav,audio/ogg',
        'DOCUMENT': 'application/pdf,.doc,.docx,.xls,.xlsx',
        'VIDEO': 'video/mp4,video/quicktime,video/x-msvideo'
    };
    
    document.getElementById('mediaFileInput').accept = accepts[type] || '';
}

function updateMediaCounts(mediaArray) {
    const counts = {
        'IMAGE': 0,
        'AUDIO': 0,
        'DOCUMENT': 0,
        'VIDEO': 0
    };
    
    mediaArray.forEach(media => {
        if (counts[media.media_type] !== undefined) {
            counts[media.media_type]++;
        }
    });
    
    Object.keys(counts).forEach(type => {
        document.getElementById(`count_${type}`).textContent = counts[type];
    });
}

function loadMediaForType(type, mediaArray) {
    const filteredMedia = mediaArray.filter(m => m.media_type === type);
    const mediaGrid = document.getElementById('mediaGrid');
    const emptyState = document.getElementById('emptyMediaState');
    
    if (filteredMedia.length === 0) {
        mediaGrid.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    mediaGrid.innerHTML = '';
    
    filteredMedia.forEach(media => {
        const item = createMediaItem(media);
        mediaGrid.appendChild(item);
    });
}

function createMediaItem(media) {
    const div = document.createElement('div');
    div.className = `media-item ${media.media_type.toLowerCase()}`;
    div.dataset.id = media.id;
    
    let previewHTML = '';
    
    if (media.media_type === 'IMAGE') {
        previewHTML = `<img src="${media.file_path}" alt="${media.file_name}" class="media-preview">`;
    } else if (media.media_type === 'AUDIO') {
        previewHTML = `
            <div class="media-icon-preview">
                <i class="mdi mdi-music"></i>
                <div class="file-name">${media.file_name}</div>
            </div>`;
    } else if (media.media_type === 'DOCUMENT') {
        previewHTML = `
            <div class="media-icon-preview">
                <i class="mdi mdi-file-document"></i>
                <div class="file-name">${media.file_name}</div>
            </div>`;
    } else if (media.media_type === 'VIDEO') {
        previewHTML = `
            <div class="media-icon-preview">
                <i class="mdi mdi-video"></i>
                <div class="file-name">${media.file_name}</div>
            </div>`;
    }
    
    div.innerHTML = `
        ${previewHTML}
        <div class="media-actions">
            <button class="media-action-btn view" onclick="viewMedia('${media.file_path}')" title="Ver">
                <i class="mdi mdi-eye"></i>
            </button>
            <button class="media-action-btn delete" onclick="deleteMedia(${media.id})" title="Eliminar">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
        <div class="media-info">
            ${formatFileSize(media.file_size)}
        </div>
    `;
    
    return div;
}

function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function viewMedia(filePath) {
    window.open(filePath, '_blank');
}

function deleteMedia(mediaId) {
    Swal.fire({
        title: '¿Eliminar archivo?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#737373',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('actions/messages/delete-media.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `media_id=${mediaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Remover del DOM
                    const item = document.querySelector(`.media-item[data-id="${mediaId}"]`);
                    if (item) {
                        item.remove();
                    }
                    
                    // Actualizar contador
                    const countElement = document.getElementById(`count_${currentMediaType}`);
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = Math.max(0, currentCount - 1);
                    
                    // Verificar si quedó vacío
                    const mediaGrid = document.getElementById('mediaGrid');
                    if (mediaGrid.children.length === 0) {
                        document.getElementById('emptyMediaState').style.display = 'block';
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo eliminar el archivo',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

// Upload de archivos
const uploadArea = document.getElementById('uploadArea');
const mediaFileInput = document.getElementById('mediaFileInput');

// Drag & Drop
uploadArea?.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea?.addEventListener('dragleave', () => {
    uploadArea.classList.remove('drag-over');
});

uploadArea?.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        uploadFiles(files);
    }
});

// Input file change
mediaFileInput?.addEventListener('change', function() {
    if (this.files.length > 0) {
        uploadFiles(this.files);
    }
});

function uploadFiles(files) {
    if (!currentMessageId) {
        Swal.fire({
            title: 'Error',
            text: 'No hay un mensaje seleccionado',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
        return;
    }
    
    const formData = new FormData();
    formData.append('message_id', currentMessageId);
    formData.append('media_type', currentMediaType);
    
    // Agregar archivos
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    
    // Mostrar progress bar
    const progressDiv = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    progressDiv.style.display = 'block';
    progressFill.style.width = '0%';
    progressText.textContent = 'Subiendo archivos...';
    
    // Upload con XMLHttpRequest para tracking de progreso
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressFill.style.width = percentComplete + '%';
            progressText.textContent = `Subiendo... ${Math.round(percentComplete)}%`;
        }
    });
    
    xhr.addEventListener('load', () => {
        progressDiv.style.display = 'none';
        
        try {
            const data = JSON.parse(xhr.responseText);
            
            if (data.success) {
                Swal.fire({
                    title: '¡Archivos subidos!',
                    text: `${data.uploaded_count} archivo(s) subido(s) correctamente`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Recargar archivos
                manageMedia(currentMessageId);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            Swal.fire({
                title: 'Error',
                text: 'Error al procesar la respuesta del servidor',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
        
        // Limpiar input
        mediaFileInput.value = '';
    });
    
    xhr.addEventListener('error', () => {
        progressDiv.style.display = 'none';
        Swal.fire({
            title: 'Error',
            text: 'Error al subir los archivos',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
    
    xhr.open('POST', 'actions/messages/upload-media.php', true);
    xhr.send(formData);
}
</script>