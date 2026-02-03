# 🌿 Harmonie & Sens - Site Web

Site vitrine pour le cabinet de conseil Harmonie & Sens.

## 📋 Prérequis

- **PHP** >= 8.2
- **Composer** >= 2.0
- **MySQL** >= 8.0 (ou MariaDB)
- **Apache** ou **Nginx** (ou Symfony CLI pour le dev)

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
- `DATABASE_URL` : adapter selon votre configuration MySQL
  ```
  DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@127.0.0.1:3306/harmonie_sens?serverVersion=8.0&charset=utf8mb4"
  ```

### 4. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. (Optionnel) Initialiser les données de base

```bash
php bin/console app:init-services
php bin/console app:create-admin
```

### 6. Configurer le serveur web

**Option A : Apache (production/développement)**

Configurer un VirtualHost pointant vers le dossier `public/` :
```apache
<VirtualHost *:80>
    ServerName harmonie-sens.local
    DocumentRoot /chemin/vers/harmonie-sens-website/public

    <Directory /chemin/vers/harmonie-sens-website/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Option B : Symfony CLI (développement)**
```bash
symfony server:start
```

**Option C : PHP built-in server (développement)**
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

##  Documentation

Consultez le dossier [docs/](docs/) pour plus de détails :
- [QUICKSTART.md](docs/QUICKSTART.md) - Guide de démarrage rapide
- [DATABASE.md](docs/DATABASE.md) - Schéma de la base de données
- [DESIGN_GUIDE.md](docs/DESIGN_GUIDE.md) - Guide de design
- [SEO_GUIDE.md](docs/SEO_GUIDE.md) - Guide SEO

## 👥 Équipe

Projet développé dans le cadre de PINF2.

## 📄 Licence

Projet propriétaire - Tous droits réservés.
