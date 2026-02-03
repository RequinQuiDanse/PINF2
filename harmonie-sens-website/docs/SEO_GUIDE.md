# Guide SEO - Harmonie & Sens

## Améliorations SEO implémentées

### 1. Balises méta essentielles (base.html.twig)
- **Title** : Titre unique et descriptif pour chaque page (50-60 caractères)
- **Meta description** : Description unique par page (150-160 caractères)
- **Meta keywords** : Mots-clés pertinents par page
- **Meta robots** : Contrôle de l'indexation
- **Canonical URL** : Évite le contenu dupliqué

### 2. Open Graph (partage sur réseaux sociaux)
- `og:title`, `og:description`, `og:image`
- `og:url`, `og:type`, `og:locale`
- `og:site_name`

### 3. Twitter Cards
- `twitter:card`, `twitter:title`
- `twitter:description`, `twitter:image`

### 4. Données structurées JSON-LD
- **Organization** : Informations sur l'entreprise
- **LocalBusiness** : Coordonnées et services
- **Service** : Description des services proposés
- **ContactPage** : Page de contact

### 5. Fichiers techniques
- **robots.txt** : Directives pour les robots d'indexation
- **sitemap.xml** : Plan du site dynamique (SitemapController)

### 6. Optimisations accessibilité (bonus SEO)
- Attributs ARIA sur la navigation
- Rôles sémantiques (menubar, menuitem)
- Attributs `role="main"` sur le contenu principal

## Fichiers à créer/ajouter

### Image Open Graph (obligatoire)
Créez une image `public/images/og-image.jpg` :
- Dimensions : 1200 x 630 pixels
- Format : JPG ou PNG
- Contenu : Logo + nom du cabinet + slogan

### Favicon (recommandé)
- `public/images/favicon.ico` (32x32 ou 64x64)
- `public/images/apple-touch-icon.png` (180x180)

## Bonnes pratiques à suivre

### Contenu
1. Utilisez des titres H1 uniques par page
2. Structurez avec H2, H3 (hiérarchie logique)
3. Ajoutez des attributs `alt` aux images
4. Rédigez du contenu de qualité (min 300 mots/page)

### Technique
1. Optimisez les images (compression, WebP)
2. Activez la mise en cache
3. Utilisez HTTPS
4. Vérifiez la vitesse de chargement

### Liens
1. Utilisez des ancres descriptives
2. Créez un maillage interne cohérent
3. Obtenez des backlinks de qualité

## Outils de vérification

- **Google Search Console** : Indexation et erreurs
- **Google PageSpeed Insights** : Performance
- **Schema.org Validator** : Données structurées
- **OpenGraph.xyz** : Aperçu des partages

## Prochaines étapes recommandées

1. [ ] Créer l'image og-image.jpg
2. [ ] Ajouter le favicon
3. [ ] Soumettre le sitemap à Google Search Console
4. [ ] Vérifier le rendu mobile
5. [ ] Ajouter Google Analytics 4
6. [ ] Configurer Google My Business (si applicable)
