# Harmonie & Sens - Site Web

Site web du cabinet de conseil Harmonie & Sens développé avec Symfony 6.4

## Thème
- **Couleurs principales** : Crème (#F5F5DC), Blanc (#FFFFFF), Noir (#1A1A1A)
- **Couleur d'accent** : Or subtil (#B8956A)
- **Polices** : Georgia (titres), Helvetica Neue (texte)

## Structure du projet

```
harmonie-sens-website/
│
├── config/                      # Configuration Symfony
├── public/                      # Fichiers publics
│   ├── css/
│   │   └── global.css          # Styles globaux (thème crème/blanc/noir)
│   ├── js/
│   └── images/
│
├── src/
│   ├── Controller/
│   │   ├── Admin/              # Contrôleurs admin
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── AdminContentController.php
│   │   │   ├── AdminMessagesController.php
│   │   │   └── AdminUsersController.php
│   │   │
│   │   ├── HomeController.php           # Page d'accueil
│   │   ├── ServicesController.php       # Pages services
│   │   ├── CabinetController.php        # Pages cabinet
│   │   └── ContactController.php        # Page contact
│   │
│   ├── Entity/                 # Entités Doctrine (à créer)
│   ├── Repository/             # Repositories (à créer)
│   └── Form/                   # Formulaires (à créer)
│
├── templates/
│   ├── base.html.twig          # Template de base
│   │
│   ├── components/             # Composants réutilisables
│   │   ├── navbar_client.html.twig   # Navbar côté client
│   │   ├── navbar_admin.html.twig    # Navbar côté admin
│   │   └── footer.html.twig          # Footer partagé
│   │
│   ├── client/                 # Templates côté client
│   │   ├── home/
│   │   │   └── index.html.twig       # Page d'accueil
│   │   │
│   │   ├── services/           # Section Services
│   │   │   ├── direction_transition.html.twig
│   │   │   ├── diagnostic_audit.html.twig
│   │   │   ├── formations.html.twig
│   │   │   └── accompagnement.html.twig
│   │   │
│   │   ├── cabinet/            # Section Cabinet
│   │   │   ├── presentation.html.twig
│   │   │   ├── valeurs.html.twig
│   │   │   ├── methodologie.html.twig
│   │   │   └── secteurs.html.twig
│   │   │
│   │   └── contact/
│   │       └── index.html.twig       # Page contact
│   │
│   └── admin/                  # Templates côté admin
│       ├── dashboard/
│       │   └── index.html.twig       # Tableau de bord admin
│       │
│       ├── content/
│       │   ├── list.html.twig        # Liste des contenus
│       │   └── form.html.twig        # Formulaire de contenu
│       │
│       ├── messages/
│       │   ├── list.html.twig        # Liste des messages
│       │   └── view.html.twig        # Détails d'un message
│       │
│       └── users/
│           ├── list.html.twig        # Liste des utilisateurs
│           └── form.html.twig        # Formulaire utilisateur
│
└── vendor/                     # Dépendances Composer
```

## Routes définies

### Routes Client
- `/` - Page d'accueil
- `/services/direction-transition` - Direction de transition
- `/services/diagnostic-audit` - Diagnostic & Audit
- `/services/formations` - Formations
- `/services/accompagnement` - Accompagnement
- `/cabinet/presentation` - Présentation du cabinet
- `/cabinet/valeurs` - Valeurs
- `/cabinet/methodologie` - Méthodologie
- `/cabinet/secteurs` - Secteurs d'intervention
- `/contact` - Contact

### Routes Admin
- `/admin` - Tableau de bord
- `/admin/content` - Gestion des contenus
- `/admin/content/new` - Nouveau contenu
- `/admin/content/{id}/edit` - Éditer un contenu
- `/admin/messages` - Liste des messages
- `/admin/messages/{id}` - Détail d'un message
- `/admin/users` - Liste des utilisateurs
- `/admin/users/new` - Nouvel utilisateur
- `/admin/users/{id}/edit` - Éditer un utilisateur

## Organisation pour 4 développeurs

### Dev 1 - Frontend Client
- Templates client (home, services, cabinet, contact)
- CSS et styles
- Intégration des composants navbar/footer

### Dev 2 - Frontend Admin
- Templates admin (dashboard, content, messages, users)
- Interface d'administration
- Formulaires

### Dev 3 - Backend & Base de données
- Entités Doctrine
- Repositories
- Configuration base de données
- Migrations

### Dev 4 - Fonctionnalités & Intégration
- Formulaires Symfony
- Validation
- Traitement des emails (contact)
- Sécurité et authentification

## À développer ensuite

1. **Base de données**
   - Entité User (admin)
   - Entité Content (pages dynamiques)
   - Entité Message (formulaire contact)
   - Entité Service
   - Entité Testimony

2. **Formulaires**
   - Formulaire de contact
   - Formulaires admin (CRUD)

3. **Sécurité**
   - Authentification admin
   - Système de rôles

4. **Assets**
   - Images
   - Logo
   - JavaScript pour interactions

5. **Contenu**
   - Remplir les sections vides avec le contenu de PROJET.txt
   - Ajouter les textes et descriptions

## Installation

```bash
cd harmonie-sens-website
composer install
symfony server:start
```

## Base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Créer les migrations
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```
