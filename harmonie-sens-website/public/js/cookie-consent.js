/**
 * Système de gestion du consentement des cookies - Conforme RGPD
 * Solution, Stratégie et Sens
 */

class CookieConsent {
    constructor() {
        this.cookieName = 'cookie_consent';
        this.cookieExpireDays = 365;
        this.consentData = this.loadConsent();
        
        this.init();
    }
    
    /**
     * Initialise le système de consentement
     */
    init() {
        // Si l'utilisateur n'a pas encore fait de choix, afficher la bannière
        if (!this.consentData) {
            this.showBanner();
        } else {
            // Sinon, afficher le bouton de paramètres et appliquer les préférences
            this.showSettingsTrigger();
            this.applyConsent();
        }
        
        // Attacher les événements
        this.attachEvents();
    }
    
    /**
     * Attache tous les événements nécessaires
     */
    attachEvents() {
        // Bouton "Tout accepter"
        const acceptAllBtn = document.getElementById('cookie-accept-all');
        if (acceptAllBtn) {
            acceptAllBtn.addEventListener('click', () => this.acceptAll());
        }
        
        // Bouton "Personnaliser"
        const customizeBtn = document.getElementById('cookie-accept-selected');
        if (customizeBtn) {
            customizeBtn.addEventListener('click', () => this.toggleDetails());
        }
        
        // Bouton "Tout refuser"
        const refuseAllBtn = document.getElementById('cookie-refuse-all');
        if (refuseAllBtn) {
            refuseAllBtn.addEventListener('click', () => this.refuseAll());
        }
        
        // Toggle des détails
        const detailsToggle = document.getElementById('cookie-details-toggle');
        if (detailsToggle) {
            detailsToggle.addEventListener('click', () => this.toggleDetails());
        }
        
        // Bouton flottant pour rouvrir les paramètres
        const settingsTrigger = document.getElementById('cookie-settings-trigger');
        if (settingsTrigger) {
            settingsTrigger.addEventListener('click', () => this.showBanner());
        }
    }
    
    /**
     * Affiche la bannière de consentement
     */
    showBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.style.display = 'block';
            // Animation d'entrée
            setTimeout(() => {
                banner.classList.add('cookie-consent-visible');
            }, 100);
        }
        
        // Masquer le bouton de paramètres
        this.hideSettingsTrigger();
    }
    
    /**
     * Masque la bannière de consentement
     */
    hideBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.classList.remove('cookie-consent-visible');
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        }
        
        // Afficher le bouton de paramètres
        this.showSettingsTrigger();
    }
    
    /**
     * Affiche le bouton flottant de paramètres
     */
    showSettingsTrigger() {
        const trigger = document.getElementById('cookie-settings-trigger');
        if (trigger) {
            trigger.style.display = 'flex';
            setTimeout(() => {
                trigger.classList.add('cookie-settings-visible');
            }, 100);
        }
    }
    
    /**
     * Masque le bouton flottant de paramètres
     */
    hideSettingsTrigger() {
        const trigger = document.getElementById('cookie-settings-trigger');
        if (trigger) {
            trigger.classList.remove('cookie-settings-visible');
            setTimeout(() => {
                trigger.style.display = 'none';
            }, 300);
        }
    }
    
    /**
     * Toggle l'affichage des détails des cookies
     */
    toggleDetails() {
        const details = document.getElementById('cookie-details');
        const toggle = document.getElementById('cookie-details-toggle');
        
        if (details && toggle) {
            const isVisible = details.style.display !== 'none';
            details.style.display = isVisible ? 'none' : 'block';
            
            // Changer l'icône
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        }
    }
    
    /**
     * Accepte tous les cookies
     */
    acceptAll() {
        const consent = {
            essential: true,
            analytics: true,
            marketing: true,
            timestamp: Date.now()
        };
        
        this.saveConsent(consent);
        this.applyConsent();
        this.hideBanner();
        
        console.log('✅ Tous les cookies ont été acceptés');
    }
    
    /**
     * Accepte uniquement les cookies sélectionnés
     */
    acceptSelected() {
        const analyticsCheckbox = document.getElementById('analytics-cookies');
        const marketingCheckbox = document.getElementById('marketing-cookies');
        
        const consent = {
            essential: true, // toujours actifs
            analytics: analyticsCheckbox ? analyticsCheckbox.checked : false,
            marketing: marketingCheckbox ? marketingCheckbox.checked : false,
            timestamp: Date.now()
        };
        
        this.saveConsent(consent);
        this.applyConsent();
        this.hideBanner();
        
        console.log('✅ Préférences de cookies enregistrées:', consent);
    }
    
    /**
     * Refuse tous les cookies non essentiels
     */
    refuseAll() {
        const consent = {
            essential: true, // toujours actifs
            analytics: false,
            marketing: false,
            timestamp: Date.now()
        };
        
        this.saveConsent(consent);
        this.applyConsent();
        this.hideBanner();
        
        console.log('⛔ Tous les cookies non essentiels ont été refusés');
    }
    
    /**
     * Sauvegarde le consentement dans le localStorage
     */
    saveConsent(consent) {
        try {
            localStorage.setItem(this.cookieName, JSON.stringify(consent));
            this.consentData = consent;
        } catch (e) {
            console.error('Erreur lors de la sauvegarde du consentement:', e);
        }
    }
    
    /**
     * Charge le consentement depuis le localStorage
     */
    loadConsent() {
        try {
            const data = localStorage.getItem(this.cookieName);
            return data ? JSON.parse(data) : null;
        } catch (e) {
            console.error('Erreur lors du chargement du consentement:', e);
            return null;
        }
    }
    
    /**
     * Applique les préférences de consentement
     */
    applyConsent() {
        if (!this.consentData) return;
        
        // Appliquer les cookies analytiques
        if (this.consentData.analytics) {
            this.enableAnalytics();
        } else {
            this.disableAnalytics();
        }
        
        // Appliquer les cookies marketing
        if (this.consentData.marketing) {
            this.enableMarketing();
        } else {
            this.disableMarketing();
        }
        
        // Dispatcher un événement personnalisé pour que d'autres scripts puissent réagir
        document.dispatchEvent(new CustomEvent('cookieConsentUpdated', {
            detail: this.consentData
        }));
    }
    
    /**
     * Active les cookies analytiques
     */
    enableAnalytics() {
        console.log('📊 Cookies analytiques activés');
        
        // Exemple : Charger Google Analytics
        // window.dataLayer = window.dataLayer || [];
        // function gtag(){dataLayer.push(arguments);}
        // gtag('js', new Date());
        // gtag('config', 'GA_MEASUREMENT_ID');
        
        // Exemple : Charger Matomo
        // var _paq = window._paq = window._paq || [];
        // _paq.push(['trackPageView']);
        // _paq.push(['enableLinkTracking']);
    }
    
    /**
     * Désactive les cookies analytiques
     */
    disableAnalytics() {
        console.log('📊 Cookies analytiques désactivés');
        
        // Supprimer les cookies analytics existants si nécessaire
        this.deleteCookie('_ga');
        this.deleteCookie('_gid');
        this.deleteCookie('_gat');
    }
    
    /**
     * Active les cookies marketing
     */
    enableMarketing() {
        console.log('🎯 Cookies marketing activés');
        
        // Ici, vous pouvez charger vos scripts marketing (Facebook Pixel, etc.)
    }
    
    /**
     * Désactive les cookies marketing
     */
    disableMarketing() {
        console.log('🎯 Cookies marketing désactivés');
        
        // Supprimer les cookies marketing existants
    }
    
    /**
     * Supprime un cookie
     */
    deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }
    
    /**
     * Vérifie si une catégorie de cookies est autorisée
     */
    isAllowed(category) {
        if (!this.consentData) return false;
        return this.consentData[category] === true;
    }
    
    /**
     * Retourne le consentement actuel
     */
    getConsent() {
        return this.consentData;
    }
}

// Initialiser le système de consentement au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    window.cookieConsent = new CookieConsent();
});

// Exposer une API globale pour vérifier le consentement
window.checkCookieConsent = function(category) {
    if (window.cookieConsent) {
        return window.cookieConsent.isAllowed(category);
    }
    return false;
};
