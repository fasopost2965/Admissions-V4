/**
 * Système Bilingue Avancé FR/AR - École La Victoire
 * Avec validations, messages d'erreur, et fonctionnalités spécialisées
 */

// Extension du système bilingue de base
class BilingualAdvanced {
    constructor() {
        this.validationMessages = {
            fr: {
                required: "Ce champ est obligatoire",
                phone: "Numéro de téléphone invalide (format: 0X XX XX XX XX)",
                cin: "Format CIN incorrect",
                email: "Adresse email invalide",
                date: "Date invalide",
                minLength: "Minimum {min} caractères requis",
                maxLength: "Maximum {max} caractères autorisés",
                phoneFormat: "Format attendu: 0X XX XX XX XX ou +212 X XX XX XX XX"
            },
            ar: {
                required: "هذا الحقل إجباري",
                phone: "رقم الهاتف غير صحيح (الصيغة: 0X XX XX XX XX)",
                cin: "صيغة البطاقة الوطنية غير صحيحة",
                email: "عنوان البريد الإلكتروني غير صحيح",
                date: "التاريخ غير صحيح",
                minLength: "مطلوب {min} أحرف كحد أدنى",
                maxLength: "مسموح بـ {max} أحرف كحد أقصى",
                phoneFormat: "الصيغة المتوقعة: 0X XX XX XX XX أو +212 X XX XX XX XX"
            }
        };

        // Quartiers de Tétouan bilingues
        this.quartiersBilingue = {
            "centre-ville": { fr: "Centre-ville", ar: "وسط المدينة" },
            "mellah": { fr: "Mellah", ar: "الملاح" },
            "ensanche": { fr: "Ensanche", ar: "الإنسانش" },
            "touilaa": { fr: "Touilaa", ar: "الطويلة" },
            "samsa": { fr: "Samsa", ar: "سمسة" },
            "mhannech": { fr: "M'hannech", ar: "المحنش" },
            "sidi-talha": { fr: "Sidi Talha", ar: "سيدي طلحة" },
            "sania": { fr: "Sania", ar: "السانية" },
            "kouia": { fr: "Koui'a", ar: "القويعة" },
            "sidi-mandri": { fr: "Sidi Mandri", ar: "سيدي المندري" },
            "borouj": { fr: "Borouj", ar: "البروج" },
            "federation": { fr: "Fédération", ar: "الفيدرالية" },
            "al-massira": { fr: "Al Massira", ar: "المسيرة" },
            "salam": { fr: "Salam", ar: "السلام" },
            "autre": { fr: "Autre quartier", ar: "حي آخر" }
        };

        // Messages système bilingues
        this.systemMessages = {
            fr: {
                loading: "Chargement...",
                saving: "Sauvegarde en cours...",
                saved: "✅ Sauvegardé automatiquement",
                error: "❌ Erreur lors de la sauvegarde",
                success: "✅ Opération réussie",
                confirm: "Êtes-vous sûr ?",
                cancel: "Annuler",
                continue: "Continuer",
                complete: "Terminé",
                incomplete: "En cours",
                step: "Étape",
                of: "sur",
                total: "Total",
                progress: "Progression",
                selectAll: "Sélectionner tout",
                deselectAll: "Désélectionner tout",
                noSelection: "Aucune sélection",
                fileNumber: "N° Dossier",
                registrationSuccess: "Inscription réussie !",
                whatsappSent: "Message WhatsApp envoyé"
            },
            ar: {
                loading: "جار التحميل...",
                saving: "جار الحفظ...",
                saved: "✅ تم الحفظ تلقائياً",
                error: "❌ خطأ أثناء الحفظ",
                success: "✅ تمت العملية بنجاح",
                confirm: "هل أنت متأكد؟",
                cancel: "إلغاء",
                continue: "متابعة",
                complete: "مكتمل",
                incomplete: "غير مكتمل",
                step: "خطوة",
                of: "من",
                total: "المجموع",
                progress: "التقدم",
                selectAll: "تحديد الكل",
                deselectAll: "إلغاء تحديد الكل",
                noSelection: "لا يوجد تحديد",
                fileNumber: "رقم الملف",
                registrationSuccess: "تم التسجيل بنجاح!",
                whatsappSent: "تم إرسال رسالة الواتساب"
            }
        };

        this.init();
    }

    init() {
        this.setupValidationMessages();
        this.setupTooltips();
        this.setupProgressUpdates();
    }

    /**
     * Configuration des messages de validation bilingues
     */
    setupValidationMessages() {
        // Écouter les événements de changement de langue
        document.addEventListener('languageChanged', (e) => {
            this.updateValidationMessages(e.detail.language);
            this.updateQuartierNames(e.detail.language);
            this.updateSystemMessages(e.detail.language);
        });
    }

    /**
     * Met à jour les messages de validation selon la langue
     */
    updateValidationMessages(lang) {
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(element => {
            const errorType = element.getAttribute('data-error-type');
            if (errorType && this.validationMessages[lang][errorType]) {
                element.textContent = this.validationMessages[lang][errorType];
            }
        });
    }

    /**
     * Met à jour les noms des quartiers selon la langue
     */
    updateQuartierNames(lang) {
        const quartierElements = document.querySelectorAll('[data-quartier]');
        quartierElements.forEach(element => {
            const quartierKey = element.getAttribute('data-quartier');
            if (this.quartiersBilingue[quartierKey]) {
                const span = element.querySelector('.quartier-name');
                if (span) {
                    span.textContent = this.quartiersBilingue[quartierKey][lang];
                }
            }
        });
    }

    /**
     * Met à jour les messages système selon la langue
     */
    updateSystemMessages(lang) {
        const messageElements = document.querySelectorAll('[data-system-message]');
        messageElements.forEach(element => {
            const messageKey = element.getAttribute('data-system-message');
            if (this.systemMessages[lang][messageKey]) {
                element.textContent = this.systemMessages[lang][messageKey];
            }
        });
    }

    /**
     * Validation bilingue des champs
     */
    validateField(field, type, options = {}) {
        const lang = getCurrentLanguage();
        let isValid = true;
        let errorMessage = '';

        // Supprimer les erreurs précédentes
        this.clearFieldError(field);

        switch (type) {
            case 'required':
                if (!field.value.trim()) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].required;
                }
                break;

            case 'phone':
                const phoneRegex = /^(0[67]|(\+212[67]))[0-9]{8}$/;
                if (field.value && !phoneRegex.test(field.value.replace(/\s/g, ''))) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].phone;
                }
                break;

            case 'cin':
                const cinRegex = /^[A-Z]{1,2}[0-9]{1,6}$/;
                if (field.value && !cinRegex.test(field.value.toUpperCase())) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].cin;
                }
                break;

            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].email;
                }
                break;

            case 'minLength':
                if (field.value.length < options.min) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].minLength.replace('{min}', options.min);
                }
                break;

            case 'maxLength':
                if (field.value.length > options.max) {
                    isValid = false;
                    errorMessage = this.validationMessages[lang].maxLength.replace('{max}', options.max);
                }
                break;
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    /**
     * Affiche une erreur sur un champ
     */
    showFieldError(field, message) {
        field.classList.add('error');
        
        let errorElement = field.parentNode.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            field.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    /**
     * Supprime l'erreur d'un champ
     */
    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    /**
     * Formate un numéro de téléphone selon la langue
     */
    formatPhone(phone, lang) {
        if (!phone) return '';
        
        // Nettoyer le numéro
        const cleaned = phone.replace(/\s/g, '');
        
        if (lang === 'ar' && cleaned.startsWith('0')) {
            return '+212' + cleaned.substring(1);
        }
        
        return phone;
    }

    /**
     * Formate une date selon la langue
     */
    formatDate(date, lang) {
        if (!date) return '';
        
        const dateObj = new Date(date);
        
        if (lang === 'ar') {
            return new Intl.DateTimeFormat('ar-MA').format(dateObj);
        }
        
        return new Intl.DateTimeFormat('fr-FR').format(dateObj);
    }

    /**
     * Configuration des tooltips bilingues
     */
    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip-fr]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target);
            });
            
            element.addEventListener('mouseleave', (e) => {
                this.hideTooltip(e.target);
            });
        });
    }

    /**
     * Affiche un tooltip bilingue
     */
    showTooltip(element) {
        const lang = getCurrentLanguage();
        const tooltipText = lang === 'ar' 
            ? element.getAttribute('data-tooltip-ar') 
            : element.getAttribute('data-tooltip-fr');
        
        if (!tooltipText) return;
        
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-bilingual';
        tooltip.textContent = tooltipText;
        tooltip.style.cssText = `
            position: absolute;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(tooltip);
        
        // Position du tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        
        // Animation d'apparition
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        element._tooltip = tooltip;
    }

    /**
     * Cache un tooltip
     */
    hideTooltip(element) {
        if (element._tooltip) {
            element._tooltip.remove();
            delete element._tooltip;
        }
    }

    /**
     * Met à jour les indicateurs de progression
     */
    setupProgressUpdates() {
        // Écouter les événements de changement de langue pour les indicateurs
        document.addEventListener('languageChanged', (e) => {
            this.updateProgressIndicators(e.detail.language);
        });
    }

    /**
     * Met à jour les indicateurs de progression selon la langue
     */
    updateProgressIndicators(lang) {
        const progressElements = document.querySelectorAll('.progress-text');
        progressElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('%')) {
                const percentage = text.match(/\d+/)?.[0] || '0';
                element.textContent = lang === 'ar' 
                    ? `${percentage}% مكتمل`
                    : `${percentage}% complété`;
            }
        });
    }

    /**
     * Génère un message WhatsApp bilingue
     */
    generateWhatsAppMessage(data, lang = 'fr') {
        const messages = {
            fr: `Bonjour,

Inscription confirmée pour ${data.studentName}
N° Dossier: ${data.dossierNumber}
Niveau: ${data.level}

École La Victoire
Tétouan, Maroc

Merci pour votre confiance.`,
            ar: `مرحبا،

تم تأكيد التسجيل للتلميذ ${data.studentName}
رقم الملف: ${data.dossierNumber}  
المستوى: ${data.level}

مدرسة النصر
تطوان، المغرب

شكراً لثقتكم.`
        };
        
        return encodeURIComponent(messages[lang]);
    }

    /**
     * Envoie un message WhatsApp bilingue
     */
    sendWhatsAppBilingual(phone, data, lang = 'fr') {
        const message = this.generateWhatsAppMessage(data, lang);
        const formattedPhone = this.formatPhone(phone, lang);
        const whatsappUrl = `https://wa.me/${formattedPhone.replace(/[^0-9]/g, '')}?text=${message}`;
        
        window.open(whatsappUrl, '_blank');
        
        // Afficher confirmation
        this.showNotification(this.systemMessages[lang].whatsappSent, 'success');
    }

    /**
     * Affiche une notification bilingue
     */
    showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#3B82F6'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 10000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Suppression automatique
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    /**
     * Sauvegarde automatique avec feedback bilingue
     */
    autoSaveWithFeedback(data) {
        const lang = getCurrentLanguage();
        
        // Afficher message de sauvegarde
        this.showNotification(this.systemMessages[lang].saving, 'info', 1000);
        
        // Simuler sauvegarde
        setTimeout(() => {
            try {
                localStorage.setItem('inscriptionData', JSON.stringify(data));
                this.showNotification(this.systemMessages[lang].saved, 'success', 2000);
            } catch (error) {
                this.showNotification(this.systemMessages[lang].error, 'error', 3000);
            }
        }, 500);
    }
}

// Initialiser le système avancé
window.bilingualAdvanced = new BilingualAdvanced();

// Fonctions utilitaires globales
function validateBilingualField(fieldId, type, options = {}) {
    const field = document.getElementById(fieldId);
    if (field) {
        return window.bilingualAdvanced.validateField(field, type, options);
    }
    return false;
}

function showBilingualNotification(messageKey, type = 'info') {
    const lang = getCurrentLanguage();
    const message = window.bilingualAdvanced.systemMessages[lang][messageKey];
    if (message) {
        window.bilingualAdvanced.showNotification(message, type);
    }
}

function formatBilingualPhone(phone) {
    const lang = getCurrentLanguage();
    return window.bilingualAdvanced.formatPhone(phone, lang);
}

function formatBilingualDate(date) {
    const lang = getCurrentLanguage();
    return window.bilingualAdvanced.formatDate(date, lang);
}

// CSS pour les erreurs et notifications
const style = document.createElement('style');
style.textContent = `
    .error {
        border-color: #EF4444 !important;
        background-color: rgba(239, 68, 68, 0.05) !important;
    }

    .error-message {
        color: #EF4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: none;
    }

    .tooltip-bilingual {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .notification {
        font-weight: 500;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* RTL pour les notifications */
    [dir="rtl"] .notification {
        right: auto;
        left: 20px;
        transform: translateX(-100%);
    }

    [dir="rtl"] .notification.show {
        transform: translateX(0);
    }

    /* Validation bilingue */
    body.lang-ar .error-message {
        text-align: right;
        direction: rtl;
    }
`;

document.head.appendChild(style); 