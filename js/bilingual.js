/**
 * Système Bilingue FR/AR - École La Victoire
 * Gestion centralisée des langues français/arabe
 */

// Variables globales pour la langue
let currentLanguage = 'fr';

// Fonctions pour gérer le changement de langue
function setLanguage(lang) {
    currentLanguage = lang;
    localStorage.setItem('selectedLanguage', lang);
    
    // Mettre à jour les boutons de langue
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById('lang' + lang.charAt(0).toUpperCase() + lang.slice(1)).classList.add('active');
    
    // Direction du texte
    document.body.dir = lang === 'ar' ? 'rtl' : 'ltr';
    document.body.className = 'lang-' + lang;
    
    // Afficher/masquer les textes selon la langue
    if (lang === 'ar') {
        document.querySelectorAll('.fr').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.ar').forEach(el => el.style.display = 'inline');
    } else {
        document.querySelectorAll('.fr').forEach(el => el.style.display = 'inline');
        document.querySelectorAll('.ar').forEach(el => el.style.display = 'none');
    }
    
    // Mettre à jour les placeholders et select
    updatePlaceholders(lang);
    updateSelectOptions(lang);
    
    // Déclencher événement personnalisé
    document.dispatchEvent(new CustomEvent('languageChanged', { 
        detail: { language: lang, isRTL: lang === 'ar' } 
    }));
}

function updatePlaceholders(lang) {
    const inputs = document.querySelectorAll('input[data-placeholder-fr], textarea[data-placeholder-fr]');
    inputs.forEach(input => {
        const placeholder = lang === 'ar' ? input.getAttribute('data-placeholder-ar') : input.getAttribute('data-placeholder-fr');
        if (placeholder) {
            input.placeholder = placeholder;
        }
    });
}

function updateSelectOptions(lang) {
    const options = document.querySelectorAll('option[data-fr]');
    options.forEach(option => {
        const text = lang === 'ar' ? option.getAttribute('data-ar') : option.getAttribute('data-fr');
        if (text) {
            option.textContent = text;
        }
    });
}

// Fonction d'initialisation
function initializeBilingualSystem() {
    // Charger la langue sauvegardée
    const savedLanguage = localStorage.getItem('selectedLanguage') || 'fr';
    setLanguage(savedLanguage);
    
    // Ajouter les écouteurs d'événements
    document.addEventListener('DOMContentLoaded', function() {
        // Charger la langue au démarrage
        const savedLang = localStorage.getItem('selectedLanguage') || 'fr';
        setLanguage(savedLang);
    });
}

// Traductions prédéfinies
const translations = {
    fr: {
        'school_name': 'École La Victoire',
        'inscription_system': 'Système d\'inscription',
        'operator': 'Opératrice',
        'step': 'Étape',
        'of': 'sur',
        'previous': 'Précédent',
        'next': 'Suivant',
        'continue': 'Continuer',
        'save': 'Enregistrer',
        'cancel': 'Annuler',
        'required': 'Obligatoire',
        'optional': 'Optionnel',
        'select_option': 'Sélectionnez une option',
        'loading': 'Chargement...',
        'saved': 'Sauvegardé',
        'error': 'Erreur',
        'success': 'Succès',
        'complete': 'Terminé',
        'incomplete': 'En cours'
    },
    ar: {
        'school_name': 'مدرسة النصر',
        'inscription_system': 'نظام التسجيل',
        'operator': 'المشغلة',
        'step': 'خطوة',
        'of': 'من',
        'previous': 'السابق',
        'next': 'التالي',
        'continue': 'متابعة',
        'save': 'حفظ',
        'cancel': 'إلغاء',
        'required': 'مطلوب',
        'optional': 'اختياري',
        'select_option': 'اختر خيار',
        'loading': 'جار التحميل...',
        'saved': 'تم الحفظ',
        'error': 'خطأ',
        'success': 'نجح',
        'complete': 'مكتمل',
        'incomplete': 'غير مكتمل'
    }
};

// Fonction pour obtenir une traduction
function t(key, fallback = '') {
    if (translations[currentLanguage] && translations[currentLanguage][key]) {
        return translations[currentLanguage][key];
    }
    return fallback || key;
}

// Fonctions utilitaires
function getCurrentLanguage() {
    return currentLanguage;
}

function isRTL() {
    return currentLanguage === 'ar';
}

function getDirection() {
    return currentLanguage === 'ar' ? 'rtl' : 'ltr';
}

function getFontFamily() {
    return currentLanguage === 'ar' 
        ? "'Tajawal', 'Cairo', 'Amiri', sans-serif"
        : "'Segoe UI', system-ui, sans-serif";
}

// Initialiser le système
if (typeof window !== 'undefined') {
    initializeBilingualSystem();
}

// Export pour utilisation en modules (Node.js)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        setLanguage,
        getCurrentLanguage,
        isRTL,
        getDirection,
        getFontFamily,
        t,
        translations
    };
}