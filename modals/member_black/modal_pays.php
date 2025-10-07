<style>
    /* ===== MODAL PAGOS - DISEÑO MINIMALISTA ===== */
    
    /* Modal dialog */
    #editarModalPago .modal-dialog {
        max-width: 800px;
        margin: 1.75rem auto;
    }

    /* Modal content */
    #editarModalPago .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s;
    }

    /* Header */
    #editarModalPago .modal-header-ux {
        padding: 24px 32px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #fff;
        border-radius: 16px 16px 0 0;
    }

    #editarModalPago .modal-title-ux {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    #editarModalPago .modal-subtitle-ux {
        font-size: 14px;
        color: #737373;
        margin: 4px 0 0 0;
        font-weight: 400;
    }

    #editarModalPago .close-ux {
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

    #editarModalPago .close-ux:hover {
        background: #e5e5e5;
    }

    #editarModalPago .close-ux i {
        font-size: 20px;
        color: #525252;
    }

    /* Body */
    #editarModalPago .modal-body-ux {
        padding: 32px;
        max-height: calc(90vh - 200px);
        overflow-y: auto;
        background: #ffffff;
    }

    /* Footer */
    #editarModalPago .modal-footer-ux {
        padding: 20px 32px;
        border-top: 1px solid #e5e5e5;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #fff;
        border-radius: 0 0 16px 16px;
    }

    #editarModalPago .btn-secondary-ux {
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

    #editarModalPago .btn-secondary-ux:hover {
        background: #e5e5e5;
    }

    /* Payment Methods Grid */
    #editarModalPago .payment-methods-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 0;
    }

    #editarModalPago .payment-card {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s;
    }

    #editarModalPago .payment-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #d4d4d4;
    }

    /* Payment Header */
    #editarModalPago .payment-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f5f5f5;
    }

    #editarModalPago .payment-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    #editarModalPago .payment-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    #editarModalPago .payment-icon.yape {
        background: linear-gradient(135deg, #722F8E, #9b59b6);
    }

    #editarModalPago .payment-icon.transferencia {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
    }

    #editarModalPago .payment-icon.tarjeta {
        background: linear-gradient(135deg, #059669, #10b981);
    }

    #editarModalPago .payment-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        text-transform: capitalize;
        margin: 0;
    }

    #editarModalPago .btn-edit-payment {
        padding: 8px 16px;
        background: #f5f5f5;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #525252;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #editarModalPago .btn-edit-payment:hover {
        background: #e5e5e5;
        color: #1a1a1a;
    }

    /* Payment Content */
    #editarModalPago .payment-content {
        font-size: 15px;
        color: #1a1a1a;
        line-height: 1.6;
    }

    #editarModalPago .payment-content.imagen {
        text-align: center;
    }

    #editarModalPago .payment-content.imagen img {
        max-width: 200px;
        max-height: 200px;
        width: auto;
        height: auto;
        border-radius: 10px;
        margin-top: 8px;
        border: 1px solid #e5e5e5;
        cursor: pointer;
        transition: all 0.2s;
        object-fit: contain;
    }

    #editarModalPago .payment-content.imagen img:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Edit Form */
    #editarModalPago .payment-edit-form {
        display: none;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e5e5;
    }

    #editarModalPago .payment-edit-form.active {
        display: block;
    }

    #editarModalPago .edit-form-group {
        margin-bottom: 16px;
    }

    #editarModalPago .edit-form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    #editarModalPago .edit-form-group select,
    #editarModalPago .edit-form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        font-family: inherit;
        background: #fff;
        color: #1a1a1a;
        transition: all 0.2s;
    }

    #editarModalPago .edit-form-group select:focus,
    #editarModalPago .edit-form-group textarea:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.05);
    }

    #editarModalPago .edit-form-group textarea {
        min-height: 100px;
        resize: vertical;
        line-height: 1.6;
    }

    /* File Upload Area */
    #editarModalPago .file-upload-area {
        border: 2px dashed #d4d4d4;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s;
    }

    #editarModalPago .file-upload-area:hover {
        border-color: #a3a3a3;
        background: #fafafa;
    }

    #editarModalPago .file-upload-area i {
        font-size: 32px;
        color: #a3a3a3;
        margin-bottom: 8px;
        display: block;
    }

    #editarModalPago .file-upload-area p {
        margin: 0;
        font-size: 14px;
        color: #737373;
        line-height: 1.5;
    }

    #editarModalPago .image-preview-edit {
        max-width: 150px;
        max-height: 150px;
        margin: 16px auto 0;
        border-radius: 10px;
        border: 1px solid #e5e5e5;
        display: block;
    }

    /* Form Actions */
    #editarModalPago .edit-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 16px;
    }

    #editarModalPago .btn-save-payment,
    #editarModalPago .btn-cancel-payment {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #editarModalPago .btn-save-payment {
        background: #1a1a1a;
        color: white;
    }

    #editarModalPago .btn-save-payment:hover {
        background: #000;
        transform: translateY(-1px);
    }

    #editarModalPago .btn-cancel-payment {
        background: #f5f5f5;
        color: #525252;
    }

    #editarModalPago .btn-cancel-payment:hover {
        background: #e5e5e5;
    }

    /* Empty State */
    #editarModalPago .empty-payments {
        text-align: center;
        padding: 60px 20px;
        color: #737373;
    }

    #editarModalPago .empty-payments i {
        font-size: 48px;
        margin-bottom: 16px;
        color: #d4d4d4;
        display: block;
    }

    #editarModalPago .empty-payments p {
        margin: 0;
        font-size: 15px;
    }

    /* Loading State */
    #editarModalPago .loading-container {
        text-align: center;
        padding: 60px 20px;
    }

    #editarModalPago .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f5f5f5;
        border-top-color: #1a1a1a;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    #editarModalPago .loading-container p {
        margin: 0;
        font-size: 15px;
        color: #737373;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #editarModalPago .modal-body-ux,
        #editarModalPago .modal-header-ux,
        #editarModalPago .modal-footer-ux {
            padding: 20px;
        }

        #editarModalPago .payment-card {
            padding: 16px;
        }

        #editarModalPago .edit-form-actions {
            flex-direction: column-reverse;
        }

        #editarModalPago .btn-save-payment,
        #editarModalPago .btn-cancel-payment {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="modal fade" id="editarModalPago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            
            <div class="modal-header-ux">
                <div>
                    <h5 class="modal-title-ux">Métodos de Pago</h5>
                    <p class="modal-subtitle-ux">Gestiona las opciones de pago para este plan</p>
                </div>
                <button type="button" class="close-ux" data-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>

            <div class="modal-body-ux">
                <div id="payment-methods-container">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Cargando métodos de pago...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer-ux">
                <button type="button" class="btn-secondary-ux" data-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

