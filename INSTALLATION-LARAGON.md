# 🚀 Installation Rapide avec Laragon - École La Victoire

## ✅ **Avantages de Laragon**
- Plus rapide et moderne que XAMPP
- Virtual hosts automatiques
- Interface intuitive
- Parfait pour le développement Windows

## 📋 **Installation en 5 étapes**

### **1. Démarrer Laragon**
1. Ouvrir **Laragon** depuis le menu Démarrer
2. Cliquer sur **"Start All"**
3. Vérifier que Apache et MySQL sont **verts** ✅

### **2. Accéder au projet**
Le projet est déjà dans le bon dossier ! Ouvrir le navigateur et aller à :

**🔗 URL principale :** [http://admission2026-v3.test/ecole-la-victoire/](http://admission2026-v3.test/ecole-la-victoire/)

**🔗 URL alternative :** [http://localhost/Admission2026-V3/ecole-la-victoire/](http://localhost/Admission2026-V3/ecole-la-victoire/)

### **3. Créer la base de données**
1. Cliquer sur **"Database"** dans Laragon
2. Cela ouvrira **phpMyAdmin** automatiquement
3. Cliquer sur **"Importer"**
4. Sélectionner le fichier `database.sql`
5. Cliquer **"Exécuter"**

### **4. Tester la connexion**
- **Identifiant :** `admin`
- **Mot de passe :** `ecole2025`

### **5. C'est prêt ! 🎉**

## 🛠️ **Avantages spécifiques Laragon**

### **Virtual Hosts automatiques**
Laragon crée automatiquement des URLs propres :
- `http://admission2026-v3.test/` au lieu de `http://localhost/Admission2026-V3/`

### **Interface moderne**
- Démarrage/arrêt en un clic
- Accès direct à phpMyAdmin
- Terminal intégré
- Gestion facile des versions PHP/MySQL

### **Performance optimisée**
- Démarrage plus rapide
- Moins de ressources utilisées
- Meilleure stabilité

## 🔧 **Configuration Laragon**

### **Si les virtual hosts ne marchent pas :**
1. Clic droit sur l'icône Laragon
2. **Apache** → **httpd.conf**
3. Vérifier que cette ligne existe :
```apache
Include conf/extra/httpd-vhosts.conf
```

### **Redémarrer les services :**
1. Cliquer **"Stop All"**
2. Attendre 3 secondes
3. Cliquer **"Start All"**

## 📱 **URLs d'accès**

| Service | URL Laragon |
|---------|-------------|
| **Projet principal** | [http://admission2026-v3.test/ecole-la-victoire/](http://admission2026-v3.test/ecole-la-victoire/) |
| **Alternative** | [http://localhost/Admission2026-V3/ecole-la-victoire/](http://localhost/Admission2026-V3/ecole-la-victoire/) |
| **phpMyAdmin** | [http://localhost/phpmyadmin](http://localhost/phpmyadmin) |
| **Laragon Home** | [http://laragon.test](http://laragon.test) |

## 🧪 **Test rapide**

1. ✅ Laragon démarré (icônes vertes)
2. ✅ Base de données créée
3. ✅ Page de connexion accessible
4. ✅ Login admin/ecole2025 fonctionne
5. ✅ Navigation vers tableau de bord
6. ✅ Étape 1 d'inscription accessible

## 🆘 **Dépannage Laragon**

### **Problème : Page ne s'affiche pas**
**Solution :** Utiliser l'URL alternative avec localhost

### **Problème : Erreur base de données**
**Solution :** 
1. Vérifier que MySQL est démarré (vert)
2. Re-importer database.sql
3. Vérifier config.php

### **Problème : Virtual host ne marche pas**
**Solution :**
1. Redémarrer Laragon
2. Utiliser l'URL localhost
3. Vider le cache du navigateur

## 🎯 **Prêt pour l'École La Victoire !**

Le système est maintenant opérationnel avec Laragon. Plus simple et plus rapide que XAMPP ! 🚀

---

**Développé spécifiquement pour Laragon - École La Victoire 2025-2026** 🎓 