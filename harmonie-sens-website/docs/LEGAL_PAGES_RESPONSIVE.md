# 📱 Système Responsive des Pages Légales

## 📋 Vue d'ensemble

Un système CSS responsive complet a été créé pour les 3 pages légales du site :
- **Mentions légales** (`/mentions-legales`)
- **Politique de confidentialité** (`/politique-confidentialite`)
- **Conditions Générales d'Utilisation** (`/conditions-generales`)

## 📁 Fichier CSS centralisé

**Fichier :** `public/css/legal-pages.css`

Tous les styles des pages légales sont désormais centralisés dans un seul fichier, facilitant la maintenance et les modifications.

## 📱 Breakpoints responsive

Le système utilise **4 breakpoints** pour une adaptation progressive sur tous les appareils :

### 🖥️ Desktop (> 1024px)
- Styles par défaut (base)
- Container : 1200px max-width
- Padding : 2rem
- Grid : colonnes multiples pour les cartes de droits

### 💻 Tablette (< 1024px)
- Container padding : 1.5rem
- H1 : 2.25rem
- Sections : padding 1.75rem
- Grid : colonnes ajustées (minmax 250px)

### 📱 Mobile (< 768px)
- Container padding : 1rem
- H1 : 1.85rem
- Sections : padding 1.5rem, border-radius 8px
- **Grid : 1 colonne** pour les cartes de droits
- Tables : scroll horizontal avec touch
- Boutons : largeur 100%
- Icônes : taille réduite

### 📱 Petit Mobile (< 480px)
- Container padding : 0.75rem
- H1 : 1.5rem
- Sections : padding 1.25rem
- H2 : 1.25rem (layout en colonne)
- Tables : font-size 0.8rem, padding réduit
- Typography : font-size 0.9rem

## 🎨 Fonctionnalités responsive

### Tables
```css
/* Scroll horizontal sur mobile avec smooth touch */
.retention-table {
    display: block;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
```

### Grilles de cartes
```css
/* Desktop: multiple colonnes */
.rights-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

/* Mobile (< 768px): 1 colonne */
.rights-grid {
    grid-template-columns: 1fr;
}
```

### Headers de sections
```css
/* Mobile: icônes en colonne pour éviter wrap problématique */
@media (max-width: 480px) {
    .legal-section h2 {
        flex-direction: column;
        align-items: flex-start;
    }
}
```

### Boutons
```css
/* Mobile: pleine largeur pour faciliter le touch */
@media (max-width: 768px) {
    .btn-manage-cookies {
        width: 100%;
        text-align: center;
    }
}
```

## ♿ Accessibilité

### Navigation au clavier
```css
/* Focus visible pour les liens et boutons */
.legal-info a:focus,
.btn-manage-cookies:focus {
    outline: 3px solid var(--color-sage);
    outline-offset: 2px;
}
```

### Contrast élevé
```css
/* Support du mode contraste élevé */
@media (prefers-contrast: high) {
    .legal-info p,
    .legal-info li {
        color: var(--color-gray-dark);
    }
}
```

### Mode sombre
```css
/* Support du dark mode natif */
@media (prefers-color-scheme: dark) {
    .legal-page { background: #1a1a1a; }
    .legal-section { background: #2a2a2a; color: #e0e0e0; }
}
```

### Réduction des animations
```css
/* Respect des préférences utilisateur */
@media (prefers-reduced-motion: reduce) {
    .legal-section {
        animation: none;
    }
    .legal-section:hover { transform: none; }
}
```

## 🖨️ Impression

Le CSS inclut des styles d'impression optimisés :

```css
@media print {
    /* Suppression des ombres et backgrounds */
    .legal-section {
        box-shadow: none;
        page-break-inside: avoid;
        border: 1px solid #ccc;
    }
    
    /* Affichage des URLs après les liens */
    .legal-info a[href]:after {
        content: " (" attr(href) ")";
    }
    
    /* Masquage des éléments interactifs */
    .btn-manage-cookies { display: none; }
}
```

## ✨ Animations

### Fade-in progressif
Les sections apparaissent progressivement avec un délai croissant :

```css
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.legal-section:nth-child(1) { animation-delay: 0.1s; }
.legal-section:nth-child(2) { animation-delay: 0.15s; }
/* ... */
```

### Hover effets
```css
.legal-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.right-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(156, 175, 136, 0.2);
}
```

## 📝 Intégration dans les templates

Chaque page légale inclut le CSS via le block `stylesheets` :

```twig
{% extends 'base.html.twig' %}

{% block stylesheets %}
    <link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
{% endblock %}

{% block body %}
    <div class="legal-page">
        <!-- Contenu -->
    </div>
{% endblock %}
```

## 🎯 Classes CSS principales

### Structure
- `.legal-page` : Container principal
- `.legal-header` : En-tête avec titre et intro
- `.legal-section` : Section de contenu (article)
- `.legal-info` : Zone de contenu texte
- `.legal-update` : Date de mise à jour

### Composants spéciaux
- `.privacy-intro-box` : Box d'introduction RGPD (gradient vert)
- `.purpose-box` : Box de finalités
- `.cookie-type-box` : Box de types de cookies
- `.highlight-section` : Section importante (bordure dorée + badge)
- `.rights-grid` : Grille de cartes de droits
- `.right-card` : Carte individuelle de droit
- `.retention-table` : Tableau de durée de conservation
- `.exercise-rights` : Box d'exercice des droits
- `.cnil-info` : Information CNIL
- `.contact-box` : Coordonnées de contact
- `.btn-manage-cookies` : Bouton de gestion des cookies

## 📊 Performance

### Optimisations
- Utilisation de variables CSS natives (`var(--color-sage)`)
- Transitions GPU-accelerated (`transform`, `opacity`)
- `will-change` évité (performance mobile)
- Animations désactivables via `prefers-reduced-motion`

### Chargement
- Fichier CSS unique (~700 lignes)
- Pas de dépendances externes
- Chargé via `<link>` classique (mise en cache navigateur)

## 🛠️ Maintenance

### Modifier les breakpoints
Les breakpoints sont définis dans les media queries :
```css
@media (max-width: 1024px) { /* Tablette */ }
@media (max-width: 768px)  { /* Mobile */ }
@media (max-width: 480px)  { /* Petit mobile */ }
```

### Modifier les couleurs
Les couleurs utilisent les variables CSS définies dans `global.css` :
- `--color-sage` : Vert sauge principal
- `--color-gold` : Doré
- `--color-gray-dark` : Gris foncé
- `--color-gray-medium` : Gris moyen
- `--color-gray-lighter` : Gris très clair

### Ajouter une nouvelle section
Utiliser la structure :
```html
<section class="legal-section">
    <h2><i class="fas fa-icon"></i> Titre</h2>
    <div class="legal-info">
        <p>Contenu...</p>
    </div>
</section>
```

## ✅ Tests effectués

- ✅ Desktop (1920×1080)
- ✅ Tablette (768×1024)
- ✅ Mobile (375×667)
- ✅ Petit mobile (320×568)
- ✅ Navigation au clavier
- ✅ Lecteur d'écran (structure sémantique)
- ✅ Impression
- ✅ Mode sombre
- ✅ Contraste élevé

## 📞 Support

Pour toute question ou modification, consultez :
- [DESIGN_GUIDE.md](DESIGN_GUIDE.md) : Guide de design global
- [COLOR_PALETTE.md](COLOR_PALETTE.md) : Palette de couleurs
- [LEGAL_PAGES_README.md](LEGAL_PAGES_README.md) : Documentation des pages légales

---

**Dernière mise à jour :** Février 2026  
**Version :** 1.0  
**Fichier CSS :** `public/css/legal-pages.css`
