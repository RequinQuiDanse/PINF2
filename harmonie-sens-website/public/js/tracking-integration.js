/**
 * Exemple d'intégration de services tiers avec le système de consentement des cookies
 * Placez ce code dans votre template Twig dans un block {% block javascripts %}
 */

(function() {
    'use strict';
    
    /**
     * Configuration des services tiers
     * Modifiez ces valeurs avec vos propres identifiants
     */
    const TRACKING_CONFIG = {
        googleAnalytics: {
            enabled: false, // Mettre à true pour activer
            trackingId: 'UA-XXXXX-Y', // Votre ID Google Analytics
            requireConsent: true,
            category: 'analytics'
        },
        googleTagManager: {
            enabled: false, // Mettre à true pour activer
            containerId: 'GTM-XXXXXX', // Votre ID Google Tag Manager
            requireConsent: true,
            category: 'analytics'
        },
        matomo: {
            enabled: false, // Mettre à true pour activer
            siteId: '1', // Votre site ID Matomo
            trackerUrl: 'https://votre-domaine.matomo.cloud/',
            requireConsent: true,
            category: 'analytics'
        },
        facebookPixel: {
            enabled: false, // Mettre à true pour activer
            pixelId: 'VOTRE_PIXEL_ID', // Votre Facebook Pixel ID
            requireConsent: true,
            category: 'marketing'
        },
        hotjar: {
            enabled: false, // Mettre à true pour activer
            hjid: 'VOTRE_HOTJAR_ID', // Votre Hotjar ID
            hjsv: 6,
            requireConsent: true,
            category: 'analytics'
        }
    };
    
    /**
     * Initialise les services en fonction du consentement
     */
    function initializeTracking(consent) {
        console.log('🔄 Initialisation des services de tracking...', consent);
        
        // Google Analytics
        if (TRACKING_CONFIG.googleAnalytics.enabled) {
            if (!TRACKING_CONFIG.googleAnalytics.requireConsent || 
                consent[TRACKING_CONFIG.googleAnalytics.category]) {
                loadGoogleAnalytics();
            }
        }
        
        // Google Tag Manager
        if (TRACKING_CONFIG.googleTagManager.enabled) {
            if (!TRACKING_CONFIG.googleTagManager.requireConsent || 
                consent[TRACKING_CONFIG.googleTagManager.category]) {
                loadGoogleTagManager();
            }
        }
        
        // Matomo
        if (TRACKING_CONFIG.matomo.enabled) {
            if (!TRACKING_CONFIG.matomo.requireConsent || 
                consent[TRACKING_CONFIG.matomo.category]) {
                loadMatomo();
            }
        }
        
        // Facebook Pixel
        if (TRACKING_CONFIG.facebookPixel.enabled) {
            if (!TRACKING_CONFIG.facebookPixel.requireConsent || 
                consent[TRACKING_CONFIG.facebookPixel.category]) {
                loadFacebookPixel();
            }
        }
        
        // Hotjar
        if (TRACKING_CONFIG.hotjar.enabled) {
            if (!TRACKING_CONFIG.hotjar.requireConsent || 
                consent[TRACKING_CONFIG.hotjar.category]) {
                loadHotjar();
            }
        }
    }
    
    /**
     * Charge Google Analytics
     */
    function loadGoogleAnalytics() {
        if (window.ga) {
            console.log('⚠️ Google Analytics déjà chargé');
            return;
        }
        
        console.log('📊 Chargement de Google Analytics...');
        
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        
        ga('create', TRACKING_CONFIG.googleAnalytics.trackingId, 'auto');
        ga('send', 'pageview');
        
        console.log('✅ Google Analytics chargé');
    }
    
    /**
     * Charge Google Tag Manager
     */
    function loadGoogleTagManager() {
        if (window.google_tag_manager) {
            console.log('⚠️ Google Tag Manager déjà chargé');
            return;
        }
        
        console.log('📊 Chargement de Google Tag Manager...');
        
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',TRACKING_CONFIG.googleTagManager.containerId);
        
        console.log('✅ Google Tag Manager chargé');
    }
    
    /**
     * Charge Matomo
     */
    function loadMatomo() {
        if (window._paq) {
            console.log('⚠️ Matomo déjà chargé');
            return;
        }
        
        console.log('📊 Chargement de Matomo...');
        
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        
        (function() {
            var u = TRACKING_CONFIG.matomo.trackerUrl;
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', TRACKING_CONFIG.matomo.siteId]);
            var d = document, 
                g = d.createElement('script'), 
                s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
        
        console.log('✅ Matomo chargé');
    }
    
    /**
     * Charge Facebook Pixel
     */
    function loadFacebookPixel() {
        if (window.fbq) {
            console.log('⚠️ Facebook Pixel déjà chargé');
            return;
        }
        
        console.log('🎯 Chargement de Facebook Pixel...');
        
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        
        fbq('init', TRACKING_CONFIG.facebookPixel.pixelId);
        fbq('track', 'PageView');
        
        console.log('✅ Facebook Pixel chargé');
    }
    
    /**
     * Charge Hotjar
     */
    function loadHotjar() {
        if (window.hj) {
            console.log('⚠️ Hotjar déjà chargé');
            return;
        }
        
        console.log('🔥 Chargement de Hotjar...');
        
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:TRACKING_CONFIG.hotjar.hjid,hjsv:TRACKING_CONFIG.hotjar.hjsv};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
        
        console.log('✅ Hotjar chargé');
    }
    
    /**
     * Écoute les changements de consentement
     */
    document.addEventListener('cookieConsentUpdated', function(event) {
        console.log('🍪 Consentement mis à jour:', event.detail);
        initializeTracking(event.detail);
    });
    
    /**
     * Si le consentement existe déjà au chargement de la page
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Petit délai pour s'assurer que cookieConsent est initialisé
        setTimeout(function() {
            if (window.cookieConsent) {
                const consent = window.cookieConsent.getConsent();
                if (consent) {
                    console.log('🍪 Consentement existant détecté:', consent);
                    initializeTracking(consent);
                } else {
                    console.log('🍪 Aucun consentement trouvé, en attente...');
                }
            }
        }, 100);
    });
    
    /**
     * Fonctions utilitaires pour tracking personnalisé
     */
    window.trackEvent = function(category, action, label, value) {
        // Google Analytics
        if (window.ga && window.checkCookieConsent('analytics')) {
            ga('send', 'event', category, action, label, value);
        }
        
        // Matomo
        if (window._paq && window.checkCookieConsent('analytics')) {
            _paq.push(['trackEvent', category, action, label, value]);
        }
        
        // Facebook Pixel
        if (window.fbq && window.checkCookieConsent('marketing')) {
            fbq('trackCustom', action, {
                category: category,
                label: label,
                value: value
            });
        }
        
        console.log('📊 Événement tracké:', { category, action, label, value });
    };
    
    /**
     * Track conversion
     */
    window.trackConversion = function(conversionName, value) {
        // Google Analytics
        if (window.ga && window.checkCookieConsent('analytics')) {
            ga('send', 'event', 'Conversion', conversionName, '', value);
        }
        
        // Facebook Pixel
        if (window.fbq && window.checkCookieConsent('marketing')) {
            fbq('track', 'Purchase', {
                content_name: conversionName,
                value: value,
                currency: 'EUR'
            });
        }
        
        console.log('💰 Conversion trackée:', conversionName, value);
    };
    
    console.log('✅ Module de tracking initialisé');
    
})();
