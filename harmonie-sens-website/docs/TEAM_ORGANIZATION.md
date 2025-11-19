# Organisation du Travail - Équipe de 4 Développeurs

## 🎨 Développeur 1 : Frontend Client & Design

### Responsabilités
- Intégration des templates côté client
- Design et UX/UI
- CSS et responsive design
- Animations et interactions

### Tâches
1. **Templates à compléter**
   - `templates/client/home/index.html.twig` - Page d'accueil avec sections
   - `templates/client/services/*.html.twig` - 4 pages de services
   - `templates/client/cabinet/*.html.twig` - 4 pages cabinet
   - `templates/client/contact/index.html.twig` - Page contact avec formulaire

2. **Styles CSS**
   - Enrichir `public/css/global.css`
   - Créer des styles spécifiques par section
   - Optimiser le responsive
   - Ajouter des animations subtiles

3. **Composants**
   - Améliorer navbar client avec animations dropdown
   - Créer des cards pour services
   - Créer des sections réutilisables
   - Footer enrichi

### Fichiers principaux
- `public/css/global.css`
- `templates/client/**/*.html.twig`
- `templates/components/navbar_client.html.twig`
- `templates/components/footer.html.twig`

---

## 🔧 Développeur 2 : Frontend Admin & Interfaces

### Responsabilités
- Interface d'administration
- Tableaux de données
- Formulaires admin
- Dashboard et statistiques

### Tâches
1. **Templates admin**
   - `templates/admin/dashboard/index.html.twig` - Dashboard avec stats
   - `templates/admin/content/*.html.twig` - Gestion contenus
   - `templates/admin/messages/*.html.twig` - Gestion messages
   - `templates/admin/users/*.html.twig` - Gestion utilisateurs

2. **Composants admin**
   - Tables avec tri et pagination
   - Formulaires stylisés
   - Boutons d'action (éditer, supprimer)
   - Alertes et notifications
   - Statistiques visuelles

3. **Navigation admin**
   - Améliorer `navbar_admin.html.twig`
   - Menu latéral (optionnel)
   - Breadcrumbs

### Fichiers principaux
- `templates/admin/**/*.html.twig`
- `templates/components/navbar_admin.html.twig`
- `public/css/admin.css` (à créer)

---

## 💾 Développeur 3 : Backend & Base de données

### Responsabilités
- Architecture de la base de données
- Entités Doctrine
- Repositories et requêtes
- Migrations

### Tâches
1. **Créer les entités** (voir `docs/DATABASE.md`)
   - User (avec authentification)
   - Content
   - Message
   - Service
   - Testimony
   - Sector
   - Formation
   - Setting

2. **Repositories personnalisés**
   - Méthodes de recherche
   - Filtres et tri
   - Statistiques

3. **Configuration**
   - `.env` pour base de données
   - `config/packages/doctrine.yaml`
   - Migrations

4. **Fixtures** (données de test)
   - Créer des données initiales
   - Users admin de test
   - Services et secteurs de base

### Fichiers principaux
- `src/Entity/*.php`
- `src/Repository/*.php`
- `migrations/*.php`
- `src/DataFixtures/*.php` (à créer)

---

## ⚙️ Développeur 4 : Fonctionnalités & Intégration

### Responsabilités
- Formulaires Symfony
- Contrôleurs et logique métier
- Système d'emails
- Sécurité et authentification

### Tâches
1. **Formulaires Symfony**
   - ContactType (formulaire de contact)
   - ContentType (admin)
   - ServiceType (admin)
   - UserType (admin)
   - TestimonyType (admin)

2. **Logique dans les contrôleurs**
   - Compléter les méthodes vides
   - Gestion du CRUD pour admin
   - Traitement formulaire contact
   - Envoi d'emails

3. **Sécurité**
   - `config/packages/security.yaml`
   - Login admin
   - Gestion des rôles (ROLE_ADMIN)
   - Protection des routes admin
   - Voter personnalisés si nécessaire

4. **Services métier**
   - `src/Service/EmailService.php` - Envoi emails
   - `src/Service/ContentService.php` - Gestion contenus
   - `src/Service/StatisticsService.php` - Calcul stats

### Fichiers principaux
- `src/Controller/**/*.php`
- `src/Form/*.php`
- `src/Service/*.php`
- `config/packages/security.yaml`

---

## 📋 Répartition par priorité

### Phase 1 : Structure de base (Semaine 1)
- **Dev 3** : Créer toutes les entités et migrations
- **Dev 4** : Configurer sécurité et créer formulaires de base
- **Dev 1** : Intégrer page d'accueil et navbar/footer
- **Dev 2** : Créer dashboard admin basique

### Phase 2 : Fonctionnalités principales (Semaine 2)
- **Dev 1** : Compléter toutes les pages services et cabinet
- **Dev 2** : Interfaces CRUD admin complètes
- **Dev 3** : Repositories et fixtures
- **Dev 4** : Formulaire contact fonctionnel + emails

### Phase 3 : Finitions (Semaine 3)
- **Dev 1** : Responsive, animations, optimisations CSS
- **Dev 2** : Statistiques dashboard, améliorations UX admin
- **Dev 3** : Optimisations requêtes, indexation BDD
- **Dev 4** : Tests, validation, sécurité renforcée

---

## 🔄 Points de synchronisation

### Daily Stand-up (15 min chaque matin)
- Ce qui a été fait hier
- Ce qui sera fait aujourd'hui
- Blocages éventuels

### Code Review (2x par semaine)
- Revue croisée du code
- Respect des standards
- Optimisations

### Intégration continue
- Commits réguliers sur branches feature
- Merge sur develop après review
- Tests avant merge

---

## 📁 Convention de nommage

### Branches Git
- `feature/frontend-home` (Dev 1)
- `feature/admin-dashboard` (Dev 2)
- `feature/entity-user` (Dev 3)
- `feature/contact-form` (Dev 4)

### Commits
```
[Frontend] Add home page hero section
[Admin] Create content management interface
[Database] Add User entity with authentication
[Feature] Implement contact form with email
```

---

## 🛠️ Outils recommandés

### Tous
- **Git** : Gestion de version
- **VS Code** : IDE
- **Symfony CLI** : Serveur de développement

### Frontend (Dev 1 & 2)
- Extension Twig pour VS Code
- DevTools navigateur
- Lighthouse pour performance

### Backend (Dev 3 & 4)
- PHP Intelephense
- Symfony extension pour VS Code
- Postman/Insomnia pour API testing

---

## ✅ Checklist finale

- [ ] Toutes les entités créées et migrées
- [ ] Pages client complètes et responsives
- [ ] Interface admin fonctionnelle
- [ ] Formulaire de contact opérationnel
- [ ] Authentification admin sécurisée
- [ ] Données de test (fixtures)
- [ ] CSS optimisé et cohérent
- [ ] Code documenté
- [ ] Tests fonctionnels
- [ ] README à jour
