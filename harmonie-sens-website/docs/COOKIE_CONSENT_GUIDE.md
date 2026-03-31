# 🍪 Guide du Système de Consentement des Cookies

## Vue d'ensemble

Le système de gestion du consentement des cookies est maintenant intégré au site. Il est conforme RGPD et permet aux utilisateurs de contrôler les cookies utilisés sur le site.

## Fichiers créés

- **Template Twig** : `templates/components/cookie-consent.html.twig`
- **JavaScript** : `public/js/cookie-consent.js`
- **Styles CSS** : Ajoutés dans `public/css/global.css`

## Fonctionnalités

### 1. Bannière de consentement
- S'affiche automatiquement à la première visite
- Design responsive adapté mobile/desktop
- Animation fluide d'apparition

### 2. Types de cookies gérés
- **Cookies essentiels** : Toujours actifs (session, authentification, préférences cookies)
- **Cookies analytiques** : Optionnels (Google Analytics, Matomo, etc.)
- **Cookies marketing** : Optionnels (Facebook Pixel, publicités, etc.)

### 3. Options pour l'utilisateur
- **Tout accepter** : Active tous les cookies
- **Personnaliser** : Permet de choisir catégorie par catégorie
- **Tout refuser** : Désactive tous les cookies non essentiels
- **Bouton flottant** : Permet de modifier les préférences à tout moment

### 4. Stockage
- Les préférences sont stockées dans le `localStorage` du navigateur
- Durée de conservation : indéfinie (jusqu'à ce que l'utilisateur change)
- Nom de la clé : `cookie_consent`

## Intégration avec des services tiers

### Google Analytics

Pour intégrer Google Analytics avec respect du consentement :

```twig
{# Dans votre template (ex: base.html.twig) #}
{% block javascripts %}
    <script>
        // Écouter l'événement de mise à jour du consentement
        document.addEventListener('cookieConsentUpdated', function(event) {
            const consent = event.detail;
            
            if (consent.analytics) {
                // Charger Google Analytics
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
                
                ga('create', 'UA-XXXXX-Y', 'auto');
                ga('send', 'pageview');
            }
        });
        
        // Si le consentement existe déjà, charger immédiatement
        if (window.checkCookieConsent('analytics')) {
            // Charger Google Analytics
        }
    </script>
{% endblock %}
```

### Google Tag Manager

```javascript
document.addEventListener('cookieConsentUpdated', function(event) {
    const consent = event.detail;
    
    if (consent.analytics || consent.marketing) {
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-XXXXXX');
    }
});
```

### Matomo (Piwik)

```javascript
document.addEventListener('cookieConsentUpdated', function(event) {
    const consent = event.detail;
    
    if (consent.analytics) {
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//votre-domaine.matomo.cloud/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    }
});
```

### Facebook Pixel

```javascript
document.addEventListener('cookieConsentUpdated', function(event) {
    const consent = event.detail;
    
    if (consent.marketing) {
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'VOTRE_PIXEL_ID');
        fbq('track', 'PageView');
    }
});
```

## API JavaScript

### Vérifier le consentement

```javascript
// Vérifier si une catégorie est autorisée
if (window.checkCookieConsent('analytics')) {
    // Charger un script analytics
}

if (window.checkCookieConsent('marketing')) {
    // Charger un script marketing
}
```

### Accéder au consentement complet

```javascript
// Obtenir l'objet de consentement complet
const consent = window.cookieConsent.getConsent();
console.log(consent);
// {
//   essential: true,
//   analytics: true,
//   marketing: false,
//   timestamp: 1708358400000
// }
```

### Rouvrir la bannière programmatiquement

```javascript
// Forcer l'affichage de la bannière
window.cookieConsent.showBanner();
```

## Personnalisation

### Modifier les couleurs

Les couleurs utilisent les variables CSS définies dans `global.css` :

```css
:root {
    --color-sage: #9CAF88;      /* Vert sauge - bouton accepter */
    --color-gold: #D4AF37;      /* Doré - bouton flottant */
    --color-gray-dark: #2F3E2A; /* Textes */
}
```

### Modifier les textes

Éditez le fichier `templates/components/cookie-consent.html.twig` pour personnaliser :
- Les titres et descriptions
- Les catégories de cookies
- Les textes des boutons

### Modifier la durée de conservation

Dans `public/js/cookie-consent.js`, ligne 10 :

```javascript
this.cookieExpireDays = 365; // Modifier ici (en jours)
```

### Ajouter une nouvelle catégorie de cookies

1. **Dans le template Twig** : Ajouter une nouvelle section dans `cookie-details`
2. **Dans le JavaScript** : Ajouter la nouvelle catégorie dans les méthodes `acceptAll()`, `acceptSelected()`, `refuseAll()`
3. **Créer les méthodes** : `enableVotreCatégorie()` et `disableVotreCatégorie()`

## Conformité RGPD

✅ **Ce système est conforme RGPD** car il :
- Demande le consentement avant de charger des cookies non essentiels
- Permet de refuser les cookies
- Permet de modifier les préférences à tout moment
- Stocke le choix de l'utilisateur
- Informe clairement sur les types de cookies utilisés
- Ne charge pas de scripts tiers avant le consentement

## Recommandations

1. **Mentionner dans la politique de confidentialité** : Ajoutez une page `/privacy-policy` qui détaille tous les cookies utilisés
2. **Mentions légales** : Créez une page `/legal-notice` conforme
3. **Tester régulièrement** : Vérifiez que les scripts tiers ne se chargent que si autorisés
4. **Logs** : Gardez une trace des consentements (optionnel, mais recommandé pour les audits)

## Exemple d'utilisation avancée

Créer un lien dans le footer pour gérer les cookies :

```twig
<a href="#" id="manage-cookies-link">Gérer mes cookies</a>

<script>
document.getElementById('manage-cookies-link').addEventListener('click', function(e) {
    e.preventDefault();
    window.cookieConsent.showBanner();
});
</script>
```

## Support navigateurs

Le système fonctionne sur tous les navigateurs modernes :
- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+

**Note** : Utilise `localStorage`, donc incompatible avec les anciens navigateurs (IE10 et antérieurs).

## Déboguer

Pour voir les logs dans la console :

```javascript
// Voir l'état du consentement
console.log(window.cookieConsent.getConsent());

// Voir si une catégorie est autorisée
console.log('Analytics:', window.checkCookieConsent('analytics'));
console.log('Marketing:', window.checkCookieConsent('marketing'));
```

## Problèmes courants

### La bannière ne s'affiche pas
- Vérifiez que le fichier JS est bien chargé : `<script src="{{ asset('js/cookie-consent.js') }}"></script>`
- Vérifiez la console pour des erreurs JavaScript
- Assurez-vous que le composant Twig est bien inclus

### Les préférences ne sont pas sauvegardées
- Vérifiez que `localStorage` est disponible (pas en navigation privée)
- Ouvrez les outils développeur > Application > Local Storage

### Les scripts tiers se chargent quand même
- Assurez-vous d'écouter l'événement `cookieConsentUpdated`
- Vérifiez que vous utilisez `window.checkCookieConsent()` avant de charger

## Questions fréquentes

**Q: Dois-je demander le consentement pour les cookies de session ?**
R: Non, les cookies essentiels au fonctionnement du site (session, authentification, CSRF) n'ont pas besoin de consentement.

**Q: Combien de temps conserver le consentement ?**
R: La CNIL recommande 13 mois maximum. Le système actuel conserve indéfiniment, mais vous pouvez ajouter une vérification de date.

**Q: Puis-je utiliser des cookies avant consentement ?**
R: Uniquement les cookies strictement nécessaires au fonctionnement du site.

**Q: Comment gérer les sous-domaines ?**
R: Si vous avez plusieurs sous-domaines, vous devrez adapter le système pour utiliser un cookie partagé au lieu de localStorage.

---

Pour toute question ou personnalisation avancée, consultez le code dans `public/js/cookie-consent.js`.
