# 📸 Images - Harmonie & Sens

## 📁 Organisation des dossiers

### `/images/logo/`
- Logo principal du site
- Variations (couleur, blanc, etc.)
- Favicon

**Exemples :**
- `logo.svg` - Logo principal
- `logo-white.svg` - Logo blanc (pour fond noir)
- `favicon.ico` - Favicon du site

### `/images/services/`
- Images illustrant les 4 services du cabinet
- Format recommandé : JPG ou WebP
- Dimensions suggérées : 1200x800px

**Fichiers attendus :**
- `direction-transition.jpg`
- `diagnostic-audit.jpg`
- `formations.jpg`
- `accompagnement.jpg`

### `/images/team/`
- Photos des membres de l'équipe
- Photos professionnelles
- Format recommandé : JPG
- Dimensions suggérées : 400x400px (carré)

### `/images/backgrounds/`
- Images de fond pour les sections
- Format recommandé : JPG ou WebP
- Dimensions suggérées : 1920x1080px

**Fichiers attendus :**
- `hero.jpg` - Image hero de la page d'accueil
- `about.jpg` - Image section "À propos"
- `contact.jpg` - Image page contact

### `/images/icons/`
- Icônes personnalisées
- Format recommandé : SVG
- Pour compléter les icônes Font Awesome

---

## 🔗 Utilisation dans Twig

```twig
{# Logo #}
<img src="{{ asset('images/logo/logo.svg') }}" alt="Harmonie & Sens">

{# Image de service #}
<img src="{{ asset('images/services/direction-transition.jpg') }}" alt="Direction de transition">

{# Photo d'équipe #}
<img src="{{ asset('images/team/marie-dupont.jpg') }}" alt="Marie Dupont">

{# Background dans le style #}
<div style="background-image: url('{{ asset('images/backgrounds/hero.jpg') }}')">
```

---

## 📏 Recommandations

### Optimisation
- **Compression** : Utiliser des outils comme TinyPNG ou ImageOptim
- **Format WebP** : Pour les navigateurs modernes
- **Lazy loading** : Ajouter `loading="lazy"` aux images

### Tailles de fichier
- Logo : < 50 KB
- Services : < 200 KB
- Team : < 100 KB
- Backgrounds : < 300 KB

### Accessibilité
- Toujours inclure un attribut `alt` descriptif
- Utiliser des images avec bon contraste

---

## 🎨 Style et cohérence

### Palette de couleurs du site
- **Crème** : #F5F5DC
- **Blanc** : #FFFFFF
- **Noir** : #1A1A1A
- **Or** : #B8956A

### Style photographique
- Lumineux et professionnel
- Tons chauds (crème/or)
- Éviter les fonds trop chargés
- Privilégier la simplicité et l'élégance
