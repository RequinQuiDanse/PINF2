# Charte Graphique - Harmonie & Sens

## 🎨 Palette de couleurs

### Couleurs principales
```css
--color-cream:        #F5F5DC  /* Crème - Fond principal */
--color-cream-light:  #FDFDF8  /* Crème clair - Fond alternatif */
--color-white:        #FFFFFF  /* Blanc - Cartes, navbar */
--color-black:        #1A1A1A  /* Noir - Texte principal, footer */
```

### Couleurs secondaires (gris)
```css
--color-gray-dark:    #333333  /* Gris foncé - Texte secondaire */
--color-gray-medium:  #666666  /* Gris moyen - Texte tertiaire */
--color-gray-light:   #CCCCCC  /* Gris clair - Bordures */
--color-gray-lighter: #E5E5E5  /* Gris très clair - Séparateurs */
```

### Couleurs d'accent (or subtil)
```css
--color-gold:         #B8956A  /* Or - Accents, hover */
--color-gold-light:   #D4C5A9  /* Or clair - Accents secondaires */
```

### Utilisation des couleurs

#### Backgrounds
- **Pages principales** : `--color-cream-light`
- **Sections alternées** : `--color-cream`
- **Cartes/Boxes** : `--color-white`
- **Footer** : `--color-black`
- **Navbar admin** : `--color-black`
- **Navbar client** : `--color-white`

#### Textes
- **Titres** : `--color-black`
- **Texte principal** : `--color-black`
- **Texte secondaire** : `--color-gray-medium`
- **Texte footer** : `--color-gray-light`

#### Accents
- **Liens hover** : `--color-gold`
- **Boutons primaires** : `--color-black` (fond) + `--color-white` (texte)
- **Boutons secondaires** : `--color-white` (fond) + `--color-black` (texte) + bordure `--color-gold`
- **Éléments actifs** : `--color-gold`

---

## 📝 Typographie

### Polices

#### Police principale (Titres)
```css
font-family: 'Georgia', 'Times New Roman', serif;
```
- **Usage** : H1, H2, H3, H4, H5, H6, logo
- **Caractère** : Élégant, professionnel, intemporel
- **Poids disponibles** : 400 (normal), 700 (bold)

#### Police secondaire (Corps de texte)
```css
font-family: 'Helvetica Neue', 'Arial', sans-serif;
```
- **Usage** : Paragraphes, listes, menus, boutons
- **Caractère** : Moderne, lisible, sobre
- **Poids disponibles** : 300 (light), 400 (normal), 500 (medium), 700 (bold)

### Hiérarchie typographique

```css
h1: 2.5rem (40px)   - Page titles
h2: 2rem (32px)     - Section titles
h3: 1.75rem (28px)  - Subsection titles
h4: 1.5rem (24px)   - Card titles
h5: 1.25rem (20px)  - Small titles
h6: 1rem (16px)     - Labels

body: 16px          - Base size
small: 0.875rem (14px)
```

### Interligne et espacements
```css
line-height: 1.6    - Corps de texte
line-height: 1.3    - Titres

letter-spacing:
  - Titres : 0
  - Nav : 0.5px
  - Texte : 0
```

---

## 🔲 Espacements

### Système d'espacement
```css
--spacing-xs:  0.5rem  (8px)   - Petits espacements
--spacing-sm:  1rem    (16px)  - Espacements standards
--spacing-md:  2rem    (32px)  - Espacements moyens
--spacing-lg:  3rem    (48px)  - Grands espacements
--spacing-xl:  4rem    (64px)  - Très grands espacements
```

### Utilisation
- **Entre sections** : `--spacing-xl`
- **Dans sections** : `--spacing-lg`
- **Entre éléments** : `--spacing-md`
- **Marges internes** : `--spacing-sm`
- **Petits espacements** : `--spacing-xs`

---

## 🎭 Styles de composants

### Boutons

#### Bouton primaire
```css
background: --color-black
color: --color-white
padding: 12px 32px
border-radius: 4px
font-weight: 500
text-transform: uppercase
letter-spacing: 1px

hover:
  background: --color-gold
  color: --color-white
```

#### Bouton secondaire
```css
background: transparent
color: --color-black
border: 2px solid --color-gold
padding: 10px 30px
border-radius: 4px

hover:
  background: --color-gold
  color: --color-white
  border-color: --color-gold
```

### Cartes (Cards)
```css
background: --color-white
padding: --spacing-md
border-radius: 8px
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08)

hover:
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12)
  transform: translateY(-2px)
```

### Formulaires

#### Input / Textarea
```css
background: --color-white
border: 1px solid --color-gray-light
padding: 12px 16px
border-radius: 4px
font-family: --font-secondary

focus:
  border-color: --color-gold
  outline: none
  box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1)
```

#### Labels
```css
font-weight: 500
color: --color-gray-dark
margin-bottom: 8px
```

---

## 🌊 Effets et transitions

### Transitions
```css
--transition-fast:   0.2s ease
--transition-normal: 0.3s ease
--transition-slow:   0.5s ease
```

### Animations recommandées
- **Hover links** : `0.3s ease`
- **Dropdown menus** : `0.2s ease`
- **Cards hover** : `0.3s ease`
- **Modals** : `0.4s ease`

### Ombres (Box shadows)
```css
/* Légère */
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08)

/* Moyenne */
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12)

/* Forte */
box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15)

/* Hover */
box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12)
```

---

## 📐 Layout et grille

### Container
```css
max-width: 1200px
margin: 0 auto
padding: 0 2rem
```

### Breakpoints responsive
```css
/* Mobile first */
Base: < 768px       - Mobile
sm:  >= 768px       - Tablet
md:  >= 992px       - Desktop
lg:  >= 1200px      - Large desktop
```

### Grid système
```css
display: grid
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))
gap: --spacing-md
```

---

## 🖼️ Images et icônes

### Format images
- **Logo** : SVG ou PNG transparent
- **Photos** : JPG optimisées (max 200kb)
- **Illustrations** : SVG de préférence
- **Icônes** : SVG ou font-icons

### Dimensions recommandées
- **Logo header** : 200x60px
- **Images services** : 400x300px
- **Photos équipe** : 300x300px (carré)
- **Bannières** : 1200x400px

### Style images
```css
border-radius: 8px
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1)
```

---

## ✨ Principes de design

### 1. Minimalisme
- Espaces blancs généreux
- Contenus aérés
- Pas de surcharge visuelle

### 2. Élégance
- Polices serif pour les titres
- Couleurs sobres et raffinées
- Transitions douces

### 3. Professionnalisme
- Hiérarchie claire
- Alignements précis
- Cohérence visuelle

### 4. Accessibilité
- Contraste suffisant (ratio 4.5:1 minimum)
- Tailles de texte lisibles
- Navigation au clavier

---

## 🎯 À faire / Ne pas faire

### ✅ À FAIRE
- Utiliser les espacements définis
- Respecter la palette de couleurs
- Garder de la cohérence
- Privilégier la lisibilité
- Tester le responsive

### ❌ NE PAS FAIRE
- Ajouter des couleurs vives
- Utiliser trop de polices différentes
- Surcharger les pages
- Négliger les espacements
- Oublier le mobile

---

## 📱 Responsive design

### Mobile (< 768px)
- Menu hamburger
- Colonnes à 1
- Padding réduit
- Titres plus petits

### Tablet (768px - 992px)
- Menu horizontal simplifié
- Colonnes à 2
- Espacements moyens

### Desktop (> 992px)
- Menu complet avec dropdowns
- Colonnes à 3-4
- Espacements généreux
- Effets hover actifs
