# 🧪 Guide de test du système de gestion des cookies

## Tests rapides à effectuer

### ✅ Test 1 : Affichage de la bannière (première visite)

1. **Ouvrir une fenêtre de navigation privée**
   - Chrome/Edge : `Ctrl+Shift+N` (Windows) ou `Cmd+Shift+N` (Mac)
   - Firefox : `Ctrl+Shift+P` (Windows) ou `Cmd+Shift+P` (Mac)
   - Safari : `Cmd+Shift+N` (Mac)

2. **Accéder à votre site**
   ```
   http://localhost:8000  (ou votre URL locale)
   ```

3. **Vérifier que :**
   - ✅ La bannière s'affiche en bas de page
   - ✅ Elle contient le titre "Respect de votre vie privée"
   - ✅ Les 3 boutons sont visibles : "Tout accepter", "Personnaliser", "Tout refuser"
   - ✅ L'icône cookie 🍪 est affichée
   - ✅ L'animation d'entrée est fluide

---

### ✅ Test 2 : Bouton "En savoir plus"

1. **Cliquer sur "▼ En savoir plus"**

2. **Vérifier que :**
   - ✅ Les détails des cookies s'affichent
   - ✅ Les 3 catégories sont listées (essentiels, analytiques, marketing)
   - ✅ Les toggles ON/OFF sont fonctionnels pour analytiques et marketing
   - ✅ Les cookies essentiels n'ont pas de toggle (toujours actifs)

---

### ✅ Test 3 : Accepter tous les cookies

1. **Cliquer sur "✓ Tout accepter"**

2. **Vérifier que :**
   - ✅ La bannière disparaît avec une animation
   - ✅ Un bouton flottant 🍪 apparaît en bas à gauche
   - ✅ Dans la console (F12), voir : "✅ Tous les cookies ont été acceptés"

3. **Ouvrir la console (F12) et taper :**
   ```javascript
   localStorage.getItem('cookie_consent')
   ```
   
4. **Vérifier que :**
   - ✅ Le résultat contient : `"analytics":true` et `"marketing":true`

5. **Recharger la page (F5)**
   - ✅ La bannière ne s'affiche plus
   - ✅ Le bouton flottant est présent dès le chargement

---

### ✅ Test 4 : Refuser tous les cookies

1. **Supprimer le consentement précédent**
   - Console (F12) :
     ```javascript
     localStorage.removeItem('cookie_consent')
     ```
   - Recharger la page (F5)

2. **Cliquer sur "✗ Tout refuser"**

3. **Vérifier que :**
   - ✅ La bannière disparaît
   - ✅ Bouton flottant apparaît
   - ✅ Console : "⛔ Tous les cookies non essentiels ont été refusés"

4. **Vérifier le consentement :**
   ```javascript
   localStorage.getItem('cookie_consent')
   ```
   - ✅ Résultat : `"analytics":false` et `"marketing":false`

---

### ✅ Test 5 : Personnalisation

1. **Supprimer le consentement et recharger**

2. **Cliquer sur "▼ En savoir plus" pour afficher les détails**

3. **Désactiver les cookies marketing** (toggle OFF)
   - Laisser les cookies analytiques activés (toggle ON)

4. **Cliquer sur "⚙ Personnaliser"**

5. **Vérifier que :**
   - ✅ La bannière disparaît
   - ✅ Consentement enregistré : `"analytics":true` et `"marketing":false`

---

### ✅ Test 6 : Bouton flottant

1. **Avec un consentement enregistré, cliquer sur le bouton flottant 🍪**

2. **Vérifier que :**
   - ✅ La bannière se réaffiche
   - ✅ Vous pouvez modifier vos préférences
   - ✅ Le nouveau choix remplace l'ancien

---

### ✅ Test 7 : Lien footer

1. **Scroller en bas de page**

2. **Localiser le lien "🍪 Gérer les cookies" dans le footer**

3. **Cliquer dessus**

4. **Vérifier que :**
   - ✅ La bannière de consentement s'affiche
   - ✅ Scroll automatique vers le haut (optionnel)

---

### ✅ Test 8 : Page de démonstration

1. **Accéder à la page de démo :**
   ```
   http://localhost:8000/cookie-demo
   ```

2. **Tester tous les boutons :**
   - ✅ "Afficher la bannière" → La bannière apparaît
   - ✅ "Vérifier le consentement" → Affiche l'objet JSON
   - ✅ "Supprimer le consentement" → Efface les préférences
   - ✅ "Envoyer un événement" → Log dans la console

3. **Vérifier que l'affichage est responsive** (redimensionner la fenêtre)

---

### ✅ Test 9 : Responsive mobile

1. **Ouvrir les outils développeur (F12)**

2. **Activer le mode mobile** : Icône 📱 ou `Ctrl+Shift+M`

3. **Choisir un appareil** : iPhone 12, Samsung Galaxy, etc.

4. **Vérifier que :**
   - ✅ La bannière est bien adaptée
   - ✅ Les boutons sont empilés verticalement
   - ✅ Le texte est lisible
   - ✅ Le bouton flottant est bien positionné
   - ✅ Les toggles sont utilisables au doigt

---

### ✅ Test 10 : Console JavaScript

1. **Ouvrir la console (F12)**

2. **Tester les API disponibles :**
   ```javascript
   // Vérifier une catégorie
   window.checkCookieConsent('analytics')  // devrait retourner true ou false
   window.checkCookieConsent('marketing')
   
   // Obtenir le consentement complet
   window.cookieConsent.getConsent()
   
   // Afficher la bannière
   window.cookieConsent.showBanner()
   ```

3. **Vérifier que :**
   - ✅ Aucune erreur JavaScript
   - ✅ Les fonctions retournent les bonnes valeurs
   - ✅ Les logs sont clairs et informatifs

---

### ✅ Test 11 : Tracking (si tracking-integration.js est chargé)

1. **Ajouter dans base.html.twig :**
   ```twig
   <script src="{{ asset('js/tracking-integration.js') }}"></script>
   ```

2. **Recharger la page**

3. **Dans la console, tester :**
   ```javascript
   window.trackEvent('Test', 'Click', 'Button', 1)
   window.trackConversion('Purchase', 99.99)
   ```

4. **Vérifier que :**
   - ✅ Les événements sont loggés dans la console
   - ✅ Si consent = true, les scripts sont chargés
   - ✅ Si consent = false, les événements ne sont pas envoyés

---

### ✅ Test 12 : Persistance

1. **Accepter tous les cookies**

2. **Fermer complètement le navigateur**

3. **Rouvrir le navigateur et revenir sur le site**

4. **Vérifier que :**
   - ✅ La bannière ne s'affiche pas (consentement mémorisé)
   - ✅ Le bouton flottant est présent
   - ✅ Les préférences sont conservées

---

## 🐛 Tests de non-régression

### Services existants

Si vous aviez déjà des cookies ou services :

- ✅ Vérifier que l'authentification fonctionne toujours
- ✅ Vérifier que la session utilisateur persiste
- ✅ Vérifier que les formulaires CSRF fonctionnent
- ✅ Vérifier que les cookies essentiels ne sont pas bloqués

---

## 📊 Checklist de production

Avant de déployer en production :

- [ ] Tester sur tous les navigateurs (Chrome, Firefox, Safari, Edge)
- [ ] Tester sur mobile (iOS Safari, Android Chrome)
- [ ] Vérifier la conformité RGPD
- [ ] Créer une page "Politique de confidentialité"
- [ ] Créer une page "Mentions légales"
- [ ] Configurer les vrais IDs de tracking (GA, GTM, etc.)
- [ ] Retirer ou sécuriser la route `/cookie-demo`
- [ ] Vérifier que les scripts tiers ne se chargent que si autorisés
- [ ] Tester avec un bloqueur de publicités (uBlock, AdBlock)
- [ ] Tester les performances (Lighthouse, PageSpeed)

---

## 🔧 Commands utiles pour les tests

### Réinitialiser le consentement
```javascript
// Dans la console du navigateur
localStorage.removeItem('cookie_consent')
location.reload()
```

### Forcer un consentement spécifique
```javascript
// Tout accepter
localStorage.setItem('cookie_consent', JSON.stringify({
    essential: true,
    analytics: true,
    marketing: true,
    timestamp: Date.now()
}))

// Tout refuser
localStorage.setItem('cookie_consent', JSON.stringify({
    essential: true,
    analytics: false,
    marketing: false,
    timestamp: Date.now()
}))

location.reload()
```

### Déboguer
```javascript
// Activer tous les logs
localStorage.debug = 'cookie:*'

// Voir l'état complet
console.log('Consent:', window.cookieConsent.getConsent())
console.log('Analytics allowed:', window.checkCookieConsent('analytics'))
console.log('Marketing allowed:', window.checkCookieConsent('marketing'))
```

---

## 📝 Rapport de test

Utilisez cette checklist pour documenter vos tests :

```
Date: __________
Testeur: __________

[ ] Test 1 - Affichage bannière         : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 2 - En savoir plus             : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 3 - Tout accepter              : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 4 - Tout refuser               : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 5 - Personnalisation           : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 6 - Bouton flottant            : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 7 - Lien footer                : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 8 - Page démo                  : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 9 - Responsive                 : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 10 - Console JavaScript        : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 11 - Tracking                  : ⃞ OK  ⃞ KO  ⃞ N/A
[ ] Test 12 - Persistance               : ⃞ OK  ⃞ KO  ⃞ N/A

Navigateurs testés:
[ ] Chrome _____
[ ] Firefox _____
[ ] Safari _____
[ ] Edge _____
[ ] Mobile iOS _____
[ ] Mobile Android _____

Problèmes rencontrés:
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

Commentaires:
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

---

**Note** : Tous ces tests peuvent être effectués en 10-15 minutes.
Si un test échoue, consultez la console du navigateur (F12) pour identifier l'erreur.
