// WhatsApp Integration System pour École La Victoire
class WhatsAppIntegration {
    constructor() {
        this.baseUrl = '/php/';
        this.businessNumber = '212539960000'; // Numéro WhatsApp Business de l'école
        this.currentData = null;
    }
    
    // Fonction principale pour préparer et envoyer
    async prepareAndSend(inscriptionData) {
        try {
            // Stocker les données pour usage ultérieur
            this.currentData = inscriptionData;
            
            // Afficher loading
            Swal.fire({
                title: 'Préparation des documents...',
                html: `
                    <div class="loading-animation">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Génération des PDFs en cours...</p>
                        <small class="text-muted">Récépissé + Liste des fournitures</small>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            
            // 1. Générer les PDFs
            const pdfResponse = await this.generatePDFs(inscriptionData);
            
            if (!pdfResponse.success) {
                throw new Error(pdfResponse.error || 'Erreur génération PDF');
            }
            
            // 2. Préparer le message WhatsApp
            const message = this.formatWhatsAppMessage(inscriptionData);
            
            // Fermer loading
            Swal.close();
            
            // 3. Afficher le modal WhatsApp
            this.showWhatsAppModal(inscriptionData, message, pdfResponse);
            
        } catch (error) {
            Swal.close();
            console.error('Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de préparer les documents: ' + error.message,
                confirmButtonColor: '#003C71'
            });
        }
    }
    
    // Générer les PDFs via API
    async generatePDFs(data) {
        const response = await fetch(this.baseUrl + 'generate-all-pdfs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error('Erreur serveur: ' + response.status);
        }
        
        return await response.json();
    }
    
    // Formater le message WhatsApp
    formatWhatsAppMessage(data) {
        const services = [];
        if (data.transport_scolaire === 'true') {
            services.push(`Transport (${data.transport_quartier || 'Quartier non spécifié'})`);
        }
        if (data.cantine_scolaire === 'true') {
            services.push('Cantine');
        }
        
        const studentName = `${data.student_firstname || data.prenom || ''} ${data.student_lastname || data.nom || ''}`.trim();
        
        return `🎓 *École La Victoire*
✅ Inscription confirmée

📋 *Informations:*
• Élève: ${studentName}
• N° Dossier: ${data.dossier_number || 'En cours...'}
• Classe: ${this.formatLevel(data.school_level || data.niveau)}
${services.length > 0 ? `• Services: ${services.join(' + ')}` : ''}

📎 *Documents joints:*
1️⃣ Récépissé d'inscription (2 copies)
2️⃣ Liste des fournitures scolaires

⚠️ *Important:*
Veuillez imprimer et signer les documents joints.
Les apporter le jour de la rentrée.

📞 Contact: +212 5 39 96 XX XX
🌐 www.groupelavictoire.com

Merci de votre confiance ! 🙏`;
    }
    
    // Afficher le modal WhatsApp
    showWhatsAppModal(data, message, pdfResponse) {
        const phoneNumber = this.cleanPhone(data.parent_mobile || data.mother_mobile || data.father_mobile);
        
        // Créer le modal s'il n'existe pas
        if (!document.getElementById('whatsappModal')) {
            this.createWhatsAppModal();
        }
        
        // Remplir les données du modal
        document.getElementById('whatsappMessage').value = message;
        document.getElementById('downloadUrl').href = pdfResponse.urls.package;
        document.getElementById('studentInfo').innerHTML = `
            <strong>${data.student_firstname || data.prenom || ''} ${data.student_lastname || data.nom || ''}</strong><br>
            <span class="text-muted">N° ${pdfResponse.dossier_number} - Classe ${this.formatLevel(data.school_level || data.niveau)}</span>
        `;
        
        // Stocker les données globalement
        window.whatsappData = {
            phone: phoneNumber,
            message: message,
            packageUrl: pdfResponse.urls.package,
            files: pdfResponse.files
        };
        
        // Afficher le modal
        $('#whatsappModal').modal('show');
    }
    
    // Créer le modal WhatsApp dynamiquement
    createWhatsAppModal() {
        const modalHTML = `
            <div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fab fa-whatsapp"></i> Envoyer les documents manuellement
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            <!-- Informations élève -->
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-graduate fa-2x mr-3"></i>
                                    <div id="studentInfo">
                                        <!-- Rempli dynamiquement -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Étapes manuelles -->
                            <div class="manual-steps mt-4" id="manualSteps">
                                <h6>Guide d'envoi en 3 étapes :</h6>
                                
                                <div class="step-cards">
                                    <!-- Étape 1 -->
                                    <div class="step-card mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="step-number bg-primary text-white rounded-circle mr-3">1</div>
                                            <div class="step-content flex-grow-1">
                                                <h6 class="mb-1">Télécharger les documents</h6>
                                                <button class="btn btn-primary btn-sm" id="downloadBtn" onclick="downloadDocuments()">
                                                    <i class="fas fa-download"></i> Télécharger le dossier ZIP
                                                </button>
                                                <small class="text-muted d-block mt-1">
                                                    Contient : Récépissé + Fournitures
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Étape 2 -->
                                    <div class="step-card mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="step-number bg-primary text-white rounded-circle mr-3">2</div>
                                            <div class="step-content flex-grow-1">
                                                <h6 class="mb-2">Copier le message</h6>
                                                <textarea id="whatsappMessage" class="form-control mb-2" rows="4" readonly></textarea>
                                                <button class="btn btn-primary btn-sm" onclick="copyMessage()">
                                                    <i class="fas fa-copy"></i> <span id="copyText">Copier</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Étape 3 -->
                                    <div class="step-card mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="step-number bg-success text-white rounded-circle mr-3">3</div>
                                            <div class="step-content flex-grow-1">
                                                <h6 class="mb-1">Ouvrir WhatsApp et envoyer</h6>
                                                <button class="btn btn-success btn-sm" onclick="openWhatsApp()">
                                                    <i class="fab fa-whatsapp"></i> Ouvrir la conversation
                                                </button>
                                                <small class="text-muted d-block mt-1">
                                                    Collez le message et joignez les 2 PDFs du ZIP.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Fermer
                            </button>
                            <a id="downloadUrl" href="#" class="btn btn-primary">
                                <i class="fas fa-download"></i> Télécharger directement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Ajouter le modal au body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
    
    // Utilitaires
    formatLevel(level) {
        const levels = {
            'PS': 'Petite Section',
            'MS': 'Moyenne Section',
            'GS': 'Grande Section',
            'CP': 'CP',
            'CE1': 'CE1',
            'CE2': 'CE2',
            'CM1': 'CM1',
            'CM2': 'CM2',
            '6EME': '6ème année'
        };
        return levels[level] || level;
    }
    
    cleanPhone(phone) {
        if (!phone) return '';
        let cleaned = phone.replace(/[^0-9]/g, '');
        if (cleaned.startsWith('0')) {
            cleaned = '212' + cleaned.substring(1);
        }
        return cleaned;
    }
}

// Instance globale
const whatsappIntegration = new WhatsAppIntegration();

// Fonctions globales pour les boutons du modal
function showManualSteps() {
    // Cette fonction n'est plus utile car les étapes sont toujours visibles
}

function downloadDocuments() {
    if (window.whatsappData && window.whatsappData.packageUrl) {
        // Créer un lien de téléchargement temporaire
        const link = document.createElement('a');
        link.href = window.whatsappData.packageUrl;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Feedback visuel
        const btn = document.getElementById('downloadBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Téléchargé !';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 3000);
    }
}

function copyMessage() {
    const textarea = document.getElementById('whatsappMessage');
    textarea.select();
    document.execCommand('copy');
    
    // Feedback visuel
    const copyBtn = event.target.closest('button');
    const copyText = document.getElementById('copyText');
    copyText.textContent = 'Copié !';
    copyBtn.classList.add('btn-success');
    copyBtn.classList.remove('btn-primary');
    
    setTimeout(() => {
        copyText.textContent = 'Copier';
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-primary');
    }, 2000);
}

function openWhatsApp() {
    if (window.whatsappData && window.whatsappData.phone) {
        const url = `https://wa.me/${window.whatsappData.phone}`;
        window.open(url, '_blank');
        
        // Suggestion d'envoi des fichiers
        setTimeout(() => {
            Swal.fire({
                title: 'N\'oubliez pas !',
                html: `
                    <div class="reminder-content">
                        <i class="fas fa-paperclip fa-2x text-warning mb-3"></i>
                        <p>Pensez à joindre les 2 PDFs à votre message WhatsApp :</p>
                        <ul class="text-left">
                            <li>📄 recepisse_inscription.pdf</li>
                            <li>📋 liste_fournitures.pdf</li>
                        </ul>
                        <small class="text-muted">Ils se trouvent dans le dossier ZIP que vous avez téléchargé</small>
                    </div>
                `,
                confirmButtonText: 'Compris !',
                confirmButtonColor: '#25D366'
            });
        }, 2000);
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Numéro manquant',
            text: 'Aucun numéro de téléphone trouvé dans les données d\'inscription',
            confirmButtonColor: '#003C71'
        });
    }
}

// CSS pour les styles du modal (ajouté dynamiquement)
const whatsappStyles = `
<style>
.step-number {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    min-width: 35px;
}

.send-option {
    transition: all 0.3s ease;
}

.send-option:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.step-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #007bff;
}

.preview-card {
    transition: all 0.3s ease;
}

.preview-card:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}

.loading-animation {
    text-align: center;
    padding: 20px;
}

.success-animation {
    text-align: center;
    padding: 20px;
}

.reminder-content {
    text-align: center;
}

.reminder-content ul {
    display: inline-block;
    text-align: left;
}

#whatsappModal .modal-dialog {
    max-width: 800px;
}

#whatsappModal .modal-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

@media (max-width: 768px) {
    .step-cards .step-card {
        margin-bottom: 1rem;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
}
</style>
`;

// Ajouter les styles
document.head.insertAdjacentHTML('beforeend', whatsappStyles); 