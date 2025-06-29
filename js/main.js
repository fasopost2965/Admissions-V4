// JavaScript principal - École La Victoire

// Configuration globale
const CONFIG = {
    schoolName: 'École La Victoire',
    schoolYear: '2025-2026',
    maxFileSize: 5 * 1024 * 1024, // 5MB
    allowedImageTypes: ['image/jpeg', 'image/png', 'image/gif'],
    allowedDocTypes: ['application/pdf', 'image/jpeg', 'image/png'],
    website: 'groupelavictoire.com',
    location: 'Tétouan, Maroc'
};

// Structure des étapes
const steps = [
    { id: 1, name: "Type d'inscription", file: "step1-type-inscription.html" },
    { id: 2, name: "Informations élève", file: "step2-informations-eleve.html" },
    { id: 3, name: "Informations parents", file: "step3-informations-parents.html" },
    { id: 4, name: "Services", file: "step4-services.html" },
    { id: 5, name: "Infos médicales", file: "step5-infos-medicales.html" },
    { id: 6, name: "Fournitures", file: "step6-fournitures.html" },
    { id: 7, name: "Validation", file: "step7-validation.html" }
];

// Classe pour gérer les données d'inscription
class InscriptionManager {
    constructor() {
        this.data = this.loadData();
        this.currentStep = 1;
    }

    // Charger les données depuis localStorage
    loadData() {
        const saved = localStorage.getItem('inscriptionData');
        return saved ? JSON.parse(saved) : {
            // Étape 1
            inscriptionType: '',
            schoolLevel: '',
            previousSchool: '',
            
            // Étape 2 - Élève
            studentFirstname: '',
            studentLastname: '',
            studentBirthdate: '',
            studentBirthplace: '',
            studentGender: '',
            studentNationality: '',
            studentPhoto: null,
            
            // Étape 3 - Parents
            fatherFirstname: '',
            fatherLastname: '',
            fatherPhone: '',
            fatherEmail: '',
            motherFirstname: '',
            motherLastname: '',
            motherPhone: '',
            motherEmail: '',
            
            // Étape 4 - Services
            transportScolaire: false,
            transportQuartier: '',
            cantineScolaire: false,
            
            // Étape 5 - Infos médicales
            groupeSanguin: '',
            allergies: '',
            medicaments: '',
            maladiesChroniques: '',
            
            // Métadonnées
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString(),
            dossierNumber: ''
        };
    }

    // Sauvegarder les données
    saveData() {
        this.data.updatedAt = new Date().toISOString();
        localStorage.setItem('inscriptionData', JSON.stringify(this.data));
    }

    // Mettre à jour une section des données
    updateData(section, newData) {
        Object.assign(this.data, newData);
        this.saveData();
    }

    // Générer un numéro de dossier
    generateDossierNumber() {
        const year = new Date().getFullYear();
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
        return `${year}-LV-${timestamp}${random}`;
    }
}

// Classe pour gérer la barre de progression
class ProgressManager {
    constructor(totalSteps = 7) {
        this.totalSteps = totalSteps;
        this.currentStep = 1;
    }

    updateProgress(step) {
        this.currentStep = step;
        const progressPercent = ((step - 1) / (this.totalSteps - 1)) * 100;
        
        // Mettre à jour la ligne de progression
        const progressLine = document.querySelector('.progress-line-active');
        if (progressLine) {
            progressLine.style.width = progressPercent + '%';
        }
        
        // Mettre à jour les cercles
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((stepElement, index) => {
            const circle = stepElement.querySelector('.step-circle');
            if (index < step - 1) {
                circle.classList.add('completed');
                circle.classList.remove('active');
                circle.innerHTML = '✓';
            } else if (index === step - 1) {
                circle.classList.add('active');
                circle.classList.remove('completed');
                circle.innerHTML = step;
            } else {
                circle.classList.remove('active', 'completed');
                circle.innerHTML = index + 1;
            }
        });
    }
}

// Utilitaires
const Utils = {
    // Formater un nom proprement
    formatName(name) {
        return name.trim().toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    },
    
    // Formater un numéro de téléphone
    formatPhone(phone) {
        return phone.replace(/\D/g, '').replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
    },
    
    // Calculer l'âge
    calculateAge(birthdate) {
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }
};

// Fonctions WhatsApp
const WhatsApp = {
    // Nettoyer et formater le numéro
    formatNumber(number) {
        const clean = number.replace(/[^0-9]/g, '');
        return clean.startsWith('212') ? clean : '212' + clean.substring(1);
    },
    
    // Envoyer un message
    send(phoneNumber, message) {
        const formattedNumber = this.formatNumber(phoneNumber);
        const encodedMessage = encodeURIComponent(message);
        const url = `https://wa.me/${formattedNumber}?text=${encodedMessage}`;
        window.open(url, '_blank');
    }
};

// Exporter les classes pour utilisation globale
window.InscriptionManager = InscriptionManager;
window.ProgressManager = ProgressManager;
window.Utils = Utils;
window.WhatsApp = WhatsApp;
