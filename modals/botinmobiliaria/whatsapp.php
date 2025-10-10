<!-- Modal WhatsApp QR -->
<div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-whatsapp mr-2"></i>
                    Conectar WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrContainer">
                    <div id="loadingQR">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2">Generando código QR...</p>
                    </div>
                    <div id="qrCode" style="display: none;"></div>
                    <div id="whatsappReady" style="display: none;">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 48px;"></i>
                        <h4 class="text-success mt-2">¡WhatsApp Conectado!</h4>
                        <p>El bot está listo para recibir mensajes</p>
                    </div>
                </div>
                <div class="mt-3">
                    <p><strong>Instrucciones:</strong></p>
                    <ol class="text-left">
                        <li>Abre WhatsApp en tu teléfono</li>
                        <li>Ve a Menú > Dispositivos vinculados</li>
                        <li>Toca "Vincular un dispositivo"</li>
                        <li>Escanea este código QR</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer">
                <button id="stopWhatsAppBtn" class="btn btn-danger">
                    Detener Bot
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>