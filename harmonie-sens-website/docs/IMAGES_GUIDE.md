# 📸 GUIDE DES IMAGES
## Harmonie & Sens - Site Web Symfony

---

## 📁 STRUCTURE DES DOSSIERS

### Images statiques (`public/images/`)
Dossier pour les **images fixes du design**, versionnées dans Git.

```
public/images/
├── logo/              Logo, variations et favicon
├── services/          Images des 4 services
├── team/              Photos de l'équipe
├── backgrounds/       Images de fond des sections
└── icons/             Icônes personnalisées
```

### Images dynamiques (`public/uploads/`)
Dossier pour les **images uploadées via l'admin**, **non versionnées** dans Git.

```
public/uploads/
├── content/           Images des contenus éditables
├── testimonies/       Photos clients pour témoignages
└── users/             Photos de profil admin
```

---

## 🖼️ IMAGES NÉCESSAIRES

### 1. Logo (`images/logo/`)
- [ ] **logo.svg** - Logo principal (couleur or/crème)
- [ ] **logo-white.svg** - Logo blanc (pour footer noir)
- [ ] **logo-dark.svg** - Logo noir (si besoin)
- [ ] **favicon.ico** - Favicon 32x32px
- [ ] **favicon.png** - Favicon PNG 180x180px (Apple)

### 2. Services (`images/services/`)
- [ ] **direction-transition.jpg** - Direction de transition
- [ ] **diagnostic-audit.jpg** - Diagnostic et audit
- [ ] **formations.jpg** - Formations
- [ ] **accompagnement.jpg** - Accompagnement personnalisé

**Spécifications :**
- Format : JPG ou WebP
- Dimensions : 1200x800px (ratio 3:2)
- Poids : < 200 KB
- Style : Professionnel, lumineux, tons chauds

### 3. Équipe (`images/team/`)
- [ ] **portrait-1.jpg** - Photo membre 1
- [ ] **portrait-2.jpg** - Photo membre 2
- [ ] **portrait-3.jpg** - Photo membre 3

**Spécifications :**
- Format : JPG
- Dimensions : 400x400px (carré)
- Poids : < 100 KB
- Fond neutre, éclairage professionnel

### 4. Backgrounds (`images/backgrounds/`)
- [ ] **hero.jpg** - Image hero page d'accueil
- [ ] **about.jpg** - Section "Le Cabinet"
- [ ] **contact.jpg** - Page contact
- [ ] **services-bg.jpg** - Fond section services

**Spécifications :**
- Format : JPG ou WebP
- Dimensions : 1920x1080px (Full HD)
- Poids : < 300 KB
- Couleurs douces, harmonie avec thème crème

### 5. Icônes (`images/icons/`)
- [ ] **valeur-1.svg** - Icône valeur 1
- [ ] **valeur-2.svg** - Icône valeur 2
- [ ] **valeur-3.svg** - Icône valeur 3
- [ ] **secteur-1.svg** - Icône secteur 1
- [ ] **secteur-2.svg** - Icône secteur 2

**Spécifications :**
- Format : SVG
- Couleur : Or (#B8956A) ou noir
- Style : Simple, épuré

---

## 💻 UTILISATION DANS TWIG

### Images statiques avec `asset()`

```twig
{# Logo dans la navbar #}
<img src="{{ asset('images/logo/logo.svg') }}" 
     alt="Harmonie & Sens - Cabinet de conseil" 
     width="200" 
     height="60">

{# Image de service #}
<img src="{{ asset('images/services/direction-transition.jpg') }}" 
     alt="Direction de transition" 
     loading="lazy">

{# Photo d'équipe #}
<div class="team-member">
    <img src="{{ asset('images/team/portrait-1.jpg') }}" 
         alt="Marie Dupont - Consultante senior">
</div>

{# Background en CSS inline #}
<section class="hero" 
         style="background-image: url('{{ asset('images/backgrounds/hero.jpg') }}')">
    <h1>Bienvenue</h1>
</section>
```

### Images uploadées dynamiquement

```twig
{# Image de contenu (depuis BDD) #}
{% if content.image %}
    <img src="{{ asset('uploads/content/' ~ content.image) }}" 
         alt="{{ content.title }}">
{% endif %}

{# Photo de témoignage #}
{% if testimony.photo %}
    <img src="{{ asset('uploads/testimonies/' ~ testimony.photo) }}" 
         alt="{{ testimony.clientName }}">
{% endif %}

{# Photo de profil utilisateur #}
{% if user.avatar %}
    <img src="{{ asset('uploads/users/' ~ user.avatar) }}" 
         alt="{{ user.fullName }}" 
         class="avatar">
{% else %}
    <img src="{{ asset('images/icons/default-avatar.svg') }}" 
         alt="Avatar par défaut">
{% endif %}
```

### Background en CSS

```twig
{# Dans le <head> ou dans un <style> #}
<style>
.hero {
    background-image: url('{{ asset('images/backgrounds/hero.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
```

---

## 🎨 BONNES PRATIQUES

### 1. Optimisation des images

**Avant de les ajouter au projet :**
- Compresser avec [TinyPNG](https://tinypng.com/) ou [ImageOptim](https://imageoptim.com/)
- Convertir en WebP pour meilleure performance
- Utiliser des dimensions adaptées (pas de 4K pour une vignette)

### 2. Accessibilité

```twig
{# ✅ BON - Alt descriptif #}
<img src="{{ asset('images/services/formations.jpg') }}" 
     alt="Formation en groupe dans une salle moderne">

{# ❌ MAUVAIS - Alt générique ou vide #}
<img src="{{ asset('images/services/formations.jpg') }}" 
     alt="Image">
```

### 3. Lazy loading

```twig
{# Pour les images en dessous du pli (below the fold) #}
<img src="{{ asset('images/backgrounds/about.jpg') }}" 
     alt="À propos" 
     loading="lazy">
```

### 4. Responsive images

```twig
{# Différentes tailles selon l'écran #}
<img srcset="{{ asset('images/services/formations-400.jpg') }} 400w,
             {{ asset('images/services/formations-800.jpg') }} 800w,
             {{ asset('images/services/formations-1200.jpg') }} 1200w"
     sizes="(max-width: 600px) 400px,
            (max-width: 1000px) 800px,
            1200px"
     src="{{ asset('images/services/formations-800.jpg') }}"
     alt="Formations professionnelles">
```

### 5. Format WebP avec fallback

```twig
<picture>
    <source srcset="{{ asset('images/services/formations.webp') }}" 
            type="image/webp">
    <img src="{{ asset('images/services/formations.jpg') }}" 
         alt="Formations professionnelles">
</picture>
```

---

## 📦 UPLOAD D'IMAGES (ADMIN)

### Configuration dans `services.yaml`

```yaml
parameters:
    upload_directory: '%kernel.project_dir%/public/uploads'
    upload_content_directory: '%kernel.project_dir%/public/uploads/content'
    upload_users_directory: '%kernel.project_dir%/public/uploads/users'
```

### Exemple de contrôleur pour upload

```php
// src/Controller/Admin/AdminContentController.php

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

public function new(Request $request, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ContentType::class, $content);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo(
                $imageFile->getClientOriginalName(), 
                PATHINFO_FILENAME
            );
            
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('upload_content_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
            }

            $content->setImage($newFilename);
        }

        // ... save entity
    }

    return $this->render('admin/content/form.html.twig', [
        'form' => $form,
    ]);
}
```

---

## 🔒 SÉCURITÉ

### .gitignore pour uploads

Ajouter dans `.gitignore` :
```
# Uploads utilisateurs (ne pas versionner)
/public/uploads/*
!/public/uploads/.gitkeep
!/public/uploads/*/.gitkeep
```

### Validation des uploads

```php
// Dans le FormType
use Symfony\Component\Validator\Constraints as Assert;

->add('image', FileType::class, [
    'label' => 'Image (JPG, PNG)',
    'mapped' => false,
    'required' => false,
    'constraints' => [
        new Assert\File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/webp',
            ],
            'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPG, PNG, WebP)',
        ])
    ],
])
```

---

## 📊 CHECKLIST IMAGES

### Avant le lancement
- [ ] Tous les logos sont créés
- [ ] Images des 4 services sont prêtes
- [ ] Photos d'équipe disponibles
- [ ] Backgrounds optimisés
- [ ] Favicon installé
- [ ] Alt text sur toutes les images
- [ ] Images compressées
- [ ] Lazy loading activé
- [ ] Upload admin fonctionnel
- [ ] Validation des fichiers en place

### Performance
- [ ] Images < 200 KB en moyenne
- [ ] Format WebP utilisé
- [ ] CDN configuré (optionnel)
- [ ] Cache navigateur activé

---

## 🎨 RESSOURCES UTILES

### Banques d'images gratuites
- [Unsplash](https://unsplash.com/) - Photos haute qualité
- [Pexels](https://pexels.com/) - Photos et vidéos
- [Pixabay](https://pixabay.com/) - Images et illustrations

### Outils de compression
- [TinyPNG](https://tinypng.com/) - Compression PNG/JPG
- [Squoosh](https://squoosh.app/) - Optimisation et conversion
- [ImageOptim](https://imageoptim.com/) - Mac uniquement

### Outils de design
- [Canva](https://canva.com/) - Création de visuels
- [Figma](https://figma.com/) - Design UI/UX
- [Remove.bg](https://remove.bg/) - Suppression de fond

### Icônes
- [Font Awesome](https://fontawesome.com/) - Déjà utilisé dans le projet
- [Heroicons](https://heroicons.com/) - Icônes SVG
- [Feather Icons](https://feathericons.com/) - Icônes minimalistes

---

**✨ Structure créée et prête à l'emploi !**
