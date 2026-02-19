# 🍪 Système Responsive du Cookie Consent Banner

## 📋 Vue d'ensemble

Système de bannière de consentement aux cookies **100% responsive** et **conforme RGPD** pour le site Solution, Stratégie et Sens.

## 📁 Architecture des fichiers

```
harmonie-sens-website/
├── public/
│   ├── css/
│   │   └── cookie-consent.css         # ⭐ Nouveau fichier CSS responsive
│   └── js/
│       ├── cookie-consent.js           # Logique de gestion
│       └── tracking-integration.js     # Intégration tierces parties
├── templates/
│   ├── base.html.twig                  # Chargement du CSS
│   └── components/
│       └── cookie-consent.html.twig    # Template de la bannière
```

## 🎨 Nouveau fichier CSS dédié

**Fichier :** `public/css/cookie-consent.css` (~1000 lignes)

### Avantages de la séparation
- ✅ **Maintenance facilitée** : tous les styles cookies dans un seul fichier
- ✅ **Performance** : chargement parallèle avec global.css
- ✅ **Modularité** : peut être désactivé/modifié indépendamment
- ✅ **Responsive avancé** : 5 breakpoints au lieu de 2

## 📱 Breakpoints responsive

### 🖥️ Desktop (> 1024px)
**Styles par défaut**
```css
.cookie-consent-banner {
    padding: 2rem;
    max-height: 95vh;
}
.cookie-consent-icon { font-size: 3rem; }
.cookie-btn { min-width: 150px; }
```

### 💻 Tablette (< 1024px)
**Optimisation intermédiaire**
```css
padding: 1.75rem;
icon: 2.5rem;
min-width boutons: 140px;
```

### 📱 Mobile (< 768px)
**Layout simplifié**
```css
/* Content en colonne + centré */
.cookie-consent-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Boutons en colonne, pleine largeur */
.cookie-consent-actions {
    flex-direction: column;
}
.cookie-btn { width: 100%; }

/* Détails sans padding-left */
.cookie-category p {
    padding-left: 0;
    margin-top: 0.5rem;
}
```

### 📱 Petit Mobile (< 480px)
**Ultra-compact**
```css
padding: 1.25rem;
icon: 2rem;
h3: 1.15rem;
toggle: 48×26px;
boutons: padding réduit
```

### 📱 Très Petit Mobile (< 360px)
**Cas extrême (iPhone SE, vieux Android)**
```css
padding: 1rem;
h3: 1.05rem;
texte: 0.8rem;
boutons: 0.7rem padding;
```

## ✨ Améliorations visuelles

### 🎯 Animations fluides

#### Apparition de la bannière
```css
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(100%);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

#### Rotation subtile du cookie icon
```css
@keyframes cookieRotate {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(5deg); }
}
```

#### Toggle bounce au clic
```css
@keyframes toggleBounce {
    0% { transform: translateX(0); }
    60% { transform: translateX(28px); }
    100% { transform: translateX(24px); }
}
```

#### Expansion des détails
```css
@keyframes expandDetails {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

### 🖱️ Hover effects

**Boutons**
- Transform: translateY(-2px)
- Box-shadow amplifiée
- Transitions fluides (0.3s ease)

**Toggle switches**
- Focus ring visible (accessibilité)
- Shadow au focus : `0 0 0 3px rgba(156, 175, 136, 0.3)`

**Bouton flottant**
- Scale: 1.15 au hover
- Rotation du cookie : animation cookieBounce
- Shadow amplifiée

## ♿ Accessibilité avancée

### Navigation au clavier
```css
/* Focus visible sur tous les éléments interactifs */
.cookie-consent-banner *:focus-visible {
    outline: 2px solid var(--color-sage);
    outline-offset: 2px;
    border-radius: 4px;
}

/* Boutons */
.cookie-btn:focus {
    outline: 3px solid rgba(0, 0, 0, 0.2);
    outline-offset: 2px;
}
```

### Mode contraste élevé
```css
@media (prefers-contrast: high) {
    .cookie-consent-banner {
        border-top: 4px solid var(--color-gray-dark);
    }
    .cookie-btn {
        border: 2px solid currentColor;
    }
    .cookie-consent-text p {
        color: var(--color-gray-dark);
    }
}
```

### Mode sombre natif
```css
@media (prefers-color-scheme: dark) {
    .cookie-consent-banner {
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.5);
    }
    .cookie-consent-text h3 { color: #f0f0f0; }
    .cookie-details { background: #333; }
}
```

### Réduction des animations
```css
@media (prefers-reduced-motion: reduce) {
    .cookie-consent-banner,
    .cookie-btn,
    .cookie-toggle-slider,
    .cookie-settings-trigger {
        transition: none;
        animation: none;
    }
    .cookie-btn:hover {
        transform: none;
    }
}
```

## 📐 Layout avancé

### Scroll vertical automatique
```css
.cookie-consent-banner {
    max-height: 95vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch; /* iOS smooth scroll */
}
```

### Flexbox intelligent
```css
/* Desktop : 3 boutons en ligne */
.cookie-consent-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.cookie-btn {
    flex: 1;
    min-width: 150px;
}

/* Mobile : colonne, pleine largeur */
@media (max-width: 768px) {
    .cookie-consent-actions {
        flex-direction: column;
    }
    .cookie-btn {
        width: 100%;
        min-width: 0;
    }
}
```

### Gestion du débordement
```css
.cookie-consent-text {
    flex: 1;
    min-width: 0; /* Empêche le débordement flexbox */
}
```

## 🌍 Support paysage mobile

```css
@media (max-width: 768px) and (orientation: landscape) {
    .cookie-consent-banner {
        max-height: 80vh;
        padding: 1rem 1.5rem;
    }
    
    /* Revenir en ligne pour économiser hauteur */
    .cookie-consent-content {
        flex-direction: row;
        text-align: left;
    }
    
    .cookie-consent-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }
}
```

## 📱 Bouton flottant responsive

### Desktop
```css
width: 60px;
height: 60px;
font-size: 1.5rem;
bottom: 20px;
left: 20px;
```

### Mobile (< 768px)
```css
width: 54px;
height: 54px;
font-size: 1.35rem;
bottom: 15px;
left: 15px;
```

### Petit mobile (< 480px)
```css
width: 50px;
height: 50px;
font-size: 1.25rem;
bottom: 12px;
left: 12px;
```

## 🎯 Toggle switches améliorés

### Taille responsive
```css
/* Desktop */
width: 52px;
height: 28px;

/* Petit mobile (< 480px) */
width: 48px;
height: 26px;
```

### États
- ✅ **Checked** : background vert sauge
- ❌ **Unchecked** : background gris clair
- 🔒 **Disabled** : opacity 0.5, cursor not-allowed
- 👁️ **Focus** : box-shadow ring visible

## 🖨️ Impression

```css
@media print {
    .cookie-consent-banner,
    .cookie-settings-trigger {
        display: none !important;
    }
}
```

## 📊 Écrans haute densité (Retina)

```css
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .cookie-toggle-slider {
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15);
    }
    .cookie-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }
}
```

## 🔧 Variables CSS utilisées

```css
:root {
    --cookie-banner-shadow: 0 -8px 32px rgba(47, 62, 42, 0.15);
    --cookie-banner-border: 3px solid var(--color-sage);
    --cookie-transition-speed: 0.4s;
    --cookie-transition-timing: cubic-bezier(0.4, 0, 0.2, 1);
}
```

## 🎨 Classes CSS principales

### Structure
- `.cookie-consent-banner` : Bannière principale (fixed bottom)
- `.cookie-consent-container` : Container max-width 1200px
- `.cookie-consent-content` : Zone avec icône + texte
- `.cookie-consent-text` : Zone de texte
- `.cookie-consent-icon` : Icône cookie animée

### Détails
- `.cookie-details` : Zone extensible des détails
- `.cookie-category` : Catégorie de cookie
- `.cookie-toggle` : Container du switch
- `.cookie-toggle-slider` : Slider du switch
- `.cookie-consent-link` : Lien "En savoir plus"

### Actions
- `.cookie-consent-actions` : Container des boutons
- `.cookie-btn` : Bouton générique
- `.cookie-btn-accept` : Bouton "Tout accepter" (vert)
- `.cookie-btn-customize` : Bouton "Personnaliser" (blanc bordure)
- `.cookie-btn-refuse` : Bouton "Tout refuser" (gris)

### Bouton flottant
- `.cookie-settings-trigger` : Bouton flottant doré
- `.cookie-settings-visible` : Classe pour afficher le bouton

## 🚀 Intégration

### Dans base.html.twig
```twig
<link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
```

### Dans components/cookie-consent.html.twig
Le template utilise automatiquement les classes du CSS.

### Dans global.css
Les anciens styles ont été supprimés et remplacés par :
```css
/* Les styles du système de cookies ont été déplacés vers :
   public/css/cookie-consent.css */
```

## 📈 Performance

### Taille du fichier
- **CSS minifié** : ~25 KB
- **CSS non minifié** : ~60 KB
- **Gzip** : ~8 KB

### Optimisations
- Sélecteurs simples (performance CSS)
- Transitions GPU-accelerated (`transform`, `opacity`)
- Animations conditionnelles (prefers-reduced-motion)
- Pas de JavaScript dans le CSS

## 🧪 Tests effectués

- ✅ **Desktop** : Windows (1920×1080), macOS (2560×1440)
- ✅ **Tablette** : iPad (768×1024), iPad Pro (1024×1366)
- ✅ **Mobile** : iPhone 13 (390×844), iPhone SE (375×667)
- ✅ **Petit mobile** : iPhone 5 (320×568), Galaxy Fold (280×653)
- ✅ **Paysage** : Tous formats
- ✅ **Navigation clavier** : Tab, Enter, Espace
- ✅ **Lecteurs d'écran** : VoiceOver (iOS), NVDA (Windows)
- ✅ **Mode sombre** : macOS, iOS, Android
- ✅ **Contraste élevé** : Windows High Contrast
- ✅ **Animations réduites** : prefers-reduced-motion
- ✅ **Impression** : Chrome, Firefox, Safari

## 🔄 Migration depuis l'ancien système

### Avant
```
public/css/global.css (lignes 4346-4699)
• ~350 lignes de CSS cookie consent
• 2 breakpoints seulement (768px, 480px)
• Animations basiques
• Pas d'accessibilité avancée
```

### Après
```
public/css/cookie-consent.css
• ~1000 lignes de CSS dédié
• 5 breakpoints (1024px, 768px, 480px, 360px)
• Animations avancées
• Accessibilité complète (focus, a11y, dark mode)
• Mode paysage
• Écrans Retina
```

## 📞 Support

### Fichiers à consulter
- [COOKIE_SYSTEM_GUIDE.md](COOKIE_SYSTEM_GUIDE.md) : Guide complet du système cookies
- [DESIGN_GUIDE.md](DESIGN_GUIDE.md) : Guide de design global
- [COLOR_PALETTE.md](COLOR_PALETTE.md) : Palette de couleurs

### Modifications courantes

**Changer la position du bouton flottant**
```css
/* cookie-consent.css ligne ~440 */
.cookie-settings-trigger {
    bottom: 20px;  /* Modifier ici */
    left: 20px;    /* Ou right: 20px pour à droite */
}
```

**Modifier les couleurs**
```css
/* Utiliser les variables CSS de global.css */
--color-sage
--color-gold
--color-gray-dark
```

**Ajouter un breakpoint personnalisé**
```css
@media (max-width: 900px) {
    .cookie-consent-banner {
        /* Vos styles */
    }
}
```

---

**Dernière mise à jour :** 19 février 2026  
**Version :** 2.0  
**Fichier CSS :** `public/css/cookie-consent.css`  
**Chargé dans :** `templates/base.html.twig`
