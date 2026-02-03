# 🌿 Harmonie & Sens - Site Web

Site vitrine pour le cabinet de conseil Harmonie & Sens.

## 📋 Prérequis

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Docker** et **Docker Compose** (pour la base de données)
- **Symfony CLI** (optionnel mais recommandé)

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd harmonie-sens-website
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copier le fichier d'exemple et l'adapter :

```bash
cp .env.example .env.local
```

Puis éditer `.env.local` avec vos valeurs :
- `APP_SECRET` : générer avec `php -r "echo bin2hex(random_bytes(16));"`
- `DATABASE_URL` : adapter si nécessaire

### 4. Démarrer la base de données

```bash
docker compose up -d
```

### 5. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. (Optionnel) Initialiser les données de base

```bash
php bin/console app:init-services
php bin/console app:create-admin
```

### 7. Démarrer le serveur

**Avec Symfony CLI (recommandé) :**
```bash
symfony server:start
```

**Ou avec PHP :**
```bash
php -S localhost:8000 -t public/
```

## 🔗 Accès

- **Site public** : http://localhost:8000
- **Administration** : http://localhost:8000/admin

## 📁 Structure du projet

```
├── config/          # Configuration Symfony
├── docs/            # Documentation du projet
├── migrations/      # Migrations Doctrine
├── public/          # Point d'entrée web (CSS, images, uploads)
├── src/             # Code source PHP
│   ├── Command/     # Commandes console
│   ├── Controller/  # Contrôleurs
│   ├── Entity/      # Entités Doctrine
│   ├── Form/        # Types de formulaires
│   └── Repository/  # Repositories Doctrine
├── templates/       # Templates Twig
│   ├── admin/       # Templates administration
│   ├── client/      # Templates site public
│   └── components/  # Composants réutilisables
└── var/             # Cache et logs (ignoré par Git)
```

## 🛠️ Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Créer un administrateur
php bin/console app:create-admin

# Initialiser les services
php bin/console app:init-services
```

## 🐳 Docker

### Démarrer les services
```bash
docker compose up -d
```

### Arrêter les services
```bash
docker compose down
```

### Voir les logs
```bash
docker compose logs -f database
```

## 📖 Documentation

Consultez le dossier [docs/](docs/) pour plus de détails :
- [QUICKSTART.md](docs/QUICKSTART.md) - Guide de démarrage rapide
- [DATABASE.md](docs/DATABASE.md) - Schéma de la base de données
- [DESIGN_GUIDE.md](docs/DESIGN_GUIDE.md) - Guide de design
- [SEO_GUIDE.md](docs/SEO_GUIDE.md) - Guide SEO

## 👥 Équipe

Projet développé dans le cadre de PINF2.

## 📄 Licence

Projet propriétaire - Tous droits réservés.
