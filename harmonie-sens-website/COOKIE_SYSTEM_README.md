# 🍪 Système de Gestion du Consentement des Cookies - Documentation Rapide

## ✅ Système installé et opérationnel !

Le système de gestion du consentement des cookies a été intégré avec succès à votre projet Symfony.

## 📁 Fichiers créés

### Templates
- ✅ `templates/components/cookie-consent.html.twig` - Composant de la bannière
- ✅ `templates/example/cookie-demo.html.twig` - Page de démonstration

### JavaScript
- ✅ `public/js/cookie-consent.js` - Logique de gestion des cookies
- ✅ `public/js/tracking-integration.js` - Intégration services tiers (optionnel)

### Documentation
- ✅ `docs/COOKIE_CONSENT_GUIDE.md` - Guide complet d'utilisation

### Styles
- ✅ Styles CSS ajoutés dans `public/css/global.css`

### Modifications
- ✅ `templates/base.html.twig` - Inclusion du système
- ✅ `templates/components/footer.html.twig` - Lien "Gérer les cookies"

## 🚀 Comment ça fonctionne ?

### 1. Bannière automatique
La bannière s'affiche automatiquement à la première visite d'un utilisateur.

### 2. Choix de l'utilisateur
L'utilisateur peut :
- ✅ **Tout accepter** : Autorise tous les cookies
- ⚙️ **Personnaliser** : Choisit catégorie par catégorie
- ❌ **Tout refuser** : Désactive tous les cookies non essentiels

### 3. Bouton flottant
Un bouton circulaire en bas à gauche permet de modifier les préférences à tout moment.

### 4. Lien dans le footer
Un lien "Gérer les cookies" est disponible dans le footer.

## 🎯 Catégories gérées

| Catégorie | Obligatoire | Description |
|-----------|-------------|-------------|
| 🔐 **Essentiels** | ✅ Oui | Session, authentification, CSRF |
| 📊 **Analytiques** | ❌ Non | Google Analytics, Matomo, etc. |
| 🎯 **Marketing** | ❌ Non | Facebook Pixel, publicités |

## ⚙️ Utilisation dans votre code

### Vérifier le consentement

```javascript
// Vérifier si les cookies analytiques sont autorisés
if (window.checkCookieConsent('analytics')) {
    // Charger Google Analytics
}

// Vérifier si les cookies marketing sont autorisés
if (window.checkCookieConsent('marketing')) {
    // Charger Facebook Pixel
}
```

### Écouter les changements

```javascript
document.addEventListener('cookieConsentUpdated', function(event) {
    const consent = event.detail;
    console.log('Consentement:', consent);
    
    if (consent.analytics) {
        // Activer les analytics
    }
});
```

### Tracker des événements

```javascript
// Tracker un événement (si autorisé)
window.trackEvent('Category', 'Action', 'Label', 123);

// Tracker une conversion (si autorisé)
window.trackConversion('Purchase', 99.99);
```

## 🔧 Intégrer des services tiers

### Méthode 1 : Fichier tracking-integration.js (Recommandé)

1. **Activer le fichier** dans `base.html.twig` :
```twig
<script src="{{ asset('js/cookie-consent.js') }}"></script>
<script src="{{ asset('js/tracking-integration.js') }}"></script>
```

2. **Configurer vos services** dans `public/js/tracking-integration.js` :
```javascript
const TRACKING_CONFIG = {
    googleAnalytics: {
        enabled: true,  // ← Passer à true
        trackingId: 'UA-XXXXX-Y',  // ← Votre ID
        requireConsent: true,
        category: 'analytics'
    },
    // ...
};
```

### Méthode 2 : Script personnalisé

Dans votre template Twig :
```twig
{% block javascripts %}
<script>
    document.addEventListener('cookieConsentUpdated', function(event) {
        if (event.detail.analytics) {
            // Charger votre script analytics
        }
    });
</script>
{% endblock %}
```

## 🎨 Personnalisation

### Modifier les couleurs

Les couleurs utilisent les variables CSS de votre thème dans `global.css` :
```css
:root {
    --color-sage: #9CAF88;      /* Bouton accepter */
    --color-gold: #D4AF37;      /* Bouton flottant */
    --color-gray-dark: #2F3E2A; /* Textes */
}
```

### Modifier les textes

Éditez `templates/components/cookie-consent.html.twig` pour changer :
- Les titres et descriptions
- Les catégories de cookies
- Les textes des boutons

## 🧪 Tester le système

### Option 1 : Sur votre site
1. Ouvrez votre site dans un navigateur privé
2. La bannière doit s'afficher automatiquement
3. Testez les différentes options (accepter, refuser, personnaliser)

### Option 2 : Page de démonstration
Une page de démonstration est disponible (nécessite de créer une route) :

**Créer la route dans un Controller** :
```php
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cookie-demo', name: 'app_cookie_demo')]
public function cookieDemo(): Response
{
    return $this->render('example/cookie-demo.html.twig');
}
```

Puis visitez : `http://votre-site.local/cookie-demo`

### Réinitialiser les préférences

Pour retester :
1. Ouvrez la console du navigateur (F12)
2. Tapez : `localStorage.removeItem('cookie_consent')`
3. Rechargez la page

## 📊 Conformité RGPD

✅ **Le système est conforme RGPD** :
- ✅ Demande le consentement AVANT de charger des cookies
- ✅ Permet de refuser facilement
- ✅ Permet de modifier les préférences à tout moment
- ✅ Informe clairement sur les types de cookies
- ✅ Ne charge pas de scripts tiers sans consentement

### Recommandations complémentaires

Pour une conformité totale, pensez à :
1. **Créer une page "Politique de confidentialité"** détaillant tous les cookies
2. **Créer une page "Mentions légales"**
3. **Tenir un registre des consentements** (optionnel mais recommandé)
4. **Nommer un DPO** si nécessaire selon votre activité

## 🐛 Débogage

### Afficher l'état du consentement
```javascript
// Dans la console du navigateur
console.log(window.cookieConsent.getConsent());
```

### Forcer l'affichage de la bannière
```javascript
// Dans la console du navigateur
window.cookieConsent.showBanner();
```

### Vérifier les catégories
```javascript
// Dans la console du navigateur
console.log('Analytics:', window.checkCookieConsent('analytics'));
console.log('Marketing:', window.checkCookieConsent('marketing'));
```

## 📞 Support

Pour plus de détails, consultez :
- 📖 `docs/COOKIE_CONSENT_GUIDE.md` - Guide complet avec exemples avancés
- 💻 `public/js/cookie-consent.js` - Code source commenté
- 🎨 `public/css/global.css` - Styles personnalisables

## 🎉 Prochaines étapes

1. **Testez** le système sur votre environnement local
2. **Personnalisez** les textes et couleurs selon vos besoins
3. **Intégrez** vos services tiers (Google Analytics, etc.)
4. **Créez** une page de politique de confidentialité
5. **Déployez** en production !

---

**Note** : Le système utilise `localStorage` pour stocker les préférences. 
Les préférences sont conservées tant que l'utilisateur ne les modifie pas ou ne vide pas son cache.
