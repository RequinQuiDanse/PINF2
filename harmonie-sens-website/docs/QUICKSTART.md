# 🚀 QUICK START - Harmonie & Sens

## Installation rapide

### 1. Vérifier les prérequis
```bash
php --version    # PHP 8.1+
composer --version
symfony version  # Symfony CLI (optionnel mais recommandé)
```

### 2. Installer les dépendances
```bash
cd /home/adam/code/2i/le2/PINF2/harmonie-sens-website
composer install
```

### 3. Configuration de la base de données

Éditer le fichier `.env` :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/harmonie_sens?serverVersion=8.0"
```

Créer la base de données :
```bash
php bin/console doctrine:database:create
```

### 4. Démarrer le serveur

**Option A : Avec Symfony CLI (recommandé)**
```bash
symfony server:start
```

**Option B : Avec PHP built-in server**
```bash
php -S localhost:8000 -t public/
```

### 5. Accéder au site

- 🌐 **Site client** : http://localhost:8000/
- ⚙️ **Admin** : http://localhost:8000/admin

---

## 📝 Prochaines étapes

### Pour Dev 1 (Frontend Client)
```bash
# Commencer par la page d'accueil
# Éditer : templates/client/home/index.html.twig
# Ajouter du contenu aux sections vides
```

### Pour Dev 2 (Frontend Admin)
```bash
# Commencer par le dashboard
# Éditer : templates/admin/dashboard/index.html.twig
# Créer des statistiques factices
```

### Pour Dev 3 (Backend/BDD)
```bash
# Créer la première entité
php bin/console make:entity User

# Suivre les prompts, puis :
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### Pour Dev 4 (Formulaires/Sécurité)
```bash
# Créer le formulaire de contact
php bin/console make:form ContactType

# Configurer la sécurité
# Éditer : config/packages/security.yaml
```

---

## 🧪 Tester les routes

### Routes client accessibles
- http://localhost:8000/
- http://localhost:8000/services/direction-transition
- http://localhost:8000/services/diagnostic-audit
- http://localhost:8000/services/formations
- http://localhost:8000/services/accompagnement
- http://localhost:8000/cabinet/presentation
- http://localhost:8000/cabinet/valeurs
- http://localhost:8000/cabinet/methodologie
- http://localhost:8000/cabinet/secteurs
- http://localhost:8000/contact

### Routes admin accessibles
- http://localhost:8000/admin
- http://localhost:8000/admin/content
- http://localhost:8000/admin/messages
- http://localhost:8000/admin/users

---

## 📁 Fichiers importants

### À consulter avant de commencer
- `ARBORESCENCE.md` - Vue d'ensemble complète
- `docs/TEAM_ORGANIZATION.md` - Répartition des tâches
- `docs/DESIGN_GUIDE.md` - Charte graphique
- `docs/DATABASE.md` - Structure BDD

### Fichiers de configuration
- `.env` - Variables d'environnement
- `config/packages/doctrine.yaml` - Config BDD
- `config/packages/security.yaml` - Sécurité (à configurer)
- `config/routes.yaml` - Routes globales

### Fichiers CSS
- `public/css/global.css` - Styles globaux (thème complet)

---

## 🔧 Commandes utiles

### Développement
```bash
# Lister toutes les routes
php bin/console debug:router

# Voir les services disponibles
php bin/console debug:container

# Vider le cache
php bin/console cache:clear

# Voir les logs en temps réel
tail -f var/log/dev.log
```

### Base de données
```bash
# Créer une nouvelle entité
php bin/console make:entity

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Vérifier la BDD
php bin/console doctrine:schema:validate
```

### Génération de code
```bash
# Créer un contrôleur
php bin/console make:controller

# Créer un formulaire
php bin/console make:form

# Créer un repository
php bin/console make:entity --regenerate
```

---

## 🐛 Debugging

### Problème : Page blanche
```bash
# Vérifier les logs
tail var/log/dev.log

# Vérifier les erreurs PHP
php -l src/Controller/HomeController.php
```

### Problème : CSS non chargé
```bash
# Vérifier que le fichier existe
ls -la public/css/global.css

# Vider le cache
php bin/console cache:clear
```

### Problème : Route introuvable
```bash
# Lister toutes les routes
php bin/console debug:router

# Vérifier une route spécifique
php bin/console debug:router app_home
```

---

## 📚 Documentation

### Symfony
- [Documentation officielle](https://symfony.com/doc/current/index.html)
- [Twig Documentation](https://twig.symfony.com/doc/)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)

### Projet
- Tous les fichiers de documentation sont dans `docs/`
- La structure est documentée dans `README_STRUCTURE.md`

---

## ✅ Checklist première utilisation

- [ ] PHP 8.1+ installé
- [ ] Composer installé
- [ ] Dépendances installées (`composer install`)
- [ ] Base de données configurée (`.env`)
- [ ] Base de données créée
- [ ] Serveur démarré
- [ ] Page d'accueil accessible
- [ ] Documentation lue
- [ ] Tâches assignées (voir `docs/TEAM_ORGANIZATION.md`)

---

## 🎯 Objectifs par phase

### Phase 1 - Semaine 1
- [x] Structure de base créée
- [ ] Entités créées
- [ ] Migrations exécutées
- [ ] Sécurité configurée
- [ ] Page d'accueil intégrée

### Phase 2 - Semaine 2
- [ ] Toutes les pages client complètes
- [ ] Interface admin fonctionnelle
- [ ] Formulaire de contact opérationnel
- [ ] CRUD admin complet

### Phase 3 - Semaine 3
- [ ] Tests fonctionnels
- [ ] Optimisations
- [ ] Documentation finale
- [ ] Prêt pour déploiement

---

## 💡 Conseils

1. **Commits fréquents** : Commit après chaque fonctionnalité
2. **Branches** : Travaillez sur des branches feature
3. **Communication** : Daily stand-up de 15 min
4. **Documentation** : Documentez vos ajouts
5. **Tests** : Testez dans plusieurs navigateurs

---

## 🆘 Besoin d'aide ?

- Consultez `docs/TEAM_ORGANIZATION.md` pour savoir qui fait quoi
- Lisez `docs/DESIGN_GUIDE.md` pour les questions de style
- Référez-vous à `docs/DATABASE.md` pour la structure BDD
- Regardez `ARBORESCENCE.md` pour la vue d'ensemble

**Bon développement ! 🚀**
