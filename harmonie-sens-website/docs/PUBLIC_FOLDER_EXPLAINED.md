# Le dossier `/public` - Explication détaillée

## Introduction

Le dossier `/public` dans Symfony est **crucial** : c'est le seul dossier qui doit être accessible publiquement par le serveur web. Ce document explique en détail ce qui se passe quand on accède à ce dossier.

---

## 1. Configuration du serveur web

### Point d'entrée unique

Dans une application Symfony, **toutes les requêtes HTTP** passent par un seul fichier : `public/index.php`. C'est ce qu'on appelle le **Front Controller Pattern**.

### Configuration Apache (exemple)

```apache
<VirtualHost *:80>
    DocumentRoot /chemin/vers/projet/public
    
    <Directory /chemin/vers/projet/public>
        AllowOverride All
        Require all granted
        
        # Rediriger toutes les requêtes vers index.php
        FallbackResource /index.php
    </Directory>
</VirtualHost>
```

### Configuration Nginx (exemple)

```nginx
server {
    root /chemin/vers/projet/public;
    
    location / {
        try_files $uri /index.php$is_args$args;
    }
    
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

**Point important** : Que vous visitiez `/`, `/services`, `/contact` ou n'importe quelle URL, **c'est toujours `index.php` qui est exécuté**.

---

## 2. Le fichier `public/index.php` - Le point d'entrée

### Contenu du fichier

```php
<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

### Analyse ligne par ligne

#### Ligne 1 : `<?php`
- Ouvre le tag PHP (évidemment !)

#### Ligne 3 : `use App\Kernel;`
- Importe la classe `Kernel` qui se trouve dans `src/Kernel.php`
- C'est le **cœur de l'application Symfony**

#### Ligne 5 : `require_once dirname(__DIR__).'/vendor/autoload_runtime.php';`

Cette ligne fait **beaucoup de choses** :

1. **`dirname(__DIR__)`** :
   - `__DIR__` = Le dossier `public/`
   - `dirname(__DIR__)` = Le dossier parent, donc la racine du projet
   
2. **`vendor/autoload_runtime.php`** :
   - C'est le fichier d'autoloading généré par Composer
   - Il permet de charger automatiquement toutes les classes PHP du projet
   - Inclut les classes de Symfony, Doctrine, Twig, etc.
   - Inclut aussi vos classes (`App\Controller`, `App\Entity`, etc.)

3. **Symfony Runtime Component** :
   - C'est un composant qui gère le cycle de vie de l'application
   - Il s'occupe de lire les variables d'environnement
   - Il initialise tout ce qui est nécessaire pour démarrer Symfony

#### Lignes 7-9 : La fonction callable

```php
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

- **`return function`** : On retourne une fonction anonyme (closure)
- **`$context`** : Tableau contenant les variables d'environnement :
  - `APP_ENV` : L'environnement (`dev`, `prod`, `test`)
  - `APP_DEBUG` : Mode debug activé ou non (`true`/`false`)
- **`new Kernel(...)`** : Crée une instance du Kernel Symfony
- Le Runtime Component de Symfony va automatiquement :
  1. Exécuter cette fonction
  2. Prendre le Kernel retourné
  3. Le démarrer
  4. Traiter la requête HTTP

---

## 3. Le Kernel - Le chef d'orchestre

### Le fichier `src/Kernel.php`

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
```

### Qu'est-ce que le Kernel fait ?

Le Kernel est responsable de :

1. **Charger tous les bundles** (composants Symfony)
   - FrameworkBundle
   - TwigBundle
   - SecurityBundle
   - DoctrineBundle
   - Etc.

2. **Charger la configuration**
   - Fichiers YAML dans `config/packages/`
   - Variables d'environnement `.env`
   - Configuration des routes

3. **Initialiser le Container de Services**
   - Tous les services de l'application (repositories, mailers, etc.)
   - Dependency Injection Container

4. **Gérer la requête HTTP**
   - Transformer la requête HTTP en objet `Request`
   - Trouver la bonne route
   - Appeler le bon Controller
   - Transformer la réponse en HTTP

---

## 4. Le cycle de vie complet d'une requête

Prenons un exemple concret : un utilisateur visite `http://localhost/services`

### Étape 1 : Le serveur web reçoit la requête

```
Navigateur → Serveur Web (Apache/Nginx) → public/index.php
```

### Étape 2 : Symfony Runtime démarre

```php
// vendor/autoload_runtime.php est chargé
// Il lit les variables d'environnement (.env)
$context = [
    'APP_ENV' => 'dev',
    'APP_DEBUG' => true,
    'APP_SECRET' => 'votre_secret_key',
    // ... autres variables
];
```

### Étape 3 : Le Kernel est créé et initialisé

```php
$kernel = new Kernel('dev', true);
$kernel->boot(); // Démarre l'application
```

Lors du `boot()`, le Kernel :

1. **Charge tous les bundles** définis dans `config/bundles.php`
2. **Compile le Container de Services**
   - Lit tous les fichiers YAML dans `config/packages/`
   - Enregistre tous les services disponibles
   - Configure l'autoload des classes
3. **Initialise le système de routing**
   - Lit `config/routes.yaml`
   - Scanne tous les Controllers pour trouver les annotations `#[Route]`
   - Construit une table de correspondance URL → Controller

### Étape 4 : La requête HTTP est transformée en objet Request

```php
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
// Contient :
// - $request->getPathInfo() = '/services'
// - $request->getMethod() = 'GET'
// - $request->query (paramètres GET)
// - $request->request (paramètres POST)
// - $request->headers
// - $request->cookies
// - etc.
```

### Étape 5 : Le Router trouve la route correspondante

Le Router de Symfony cherche dans toutes les routes enregistrées :

```php
// Il trouve dans src/Controller/ServicesController.php :
#[Route('/services', name: 'app_services_list')]
public function list(ServiceRepository $serviceRepository): Response
{
    // ...
}
```

Le Router crée un objet avec :
- Le Controller à appeler : `ServicesController::list`
- Les paramètres de la méthode : `ServiceRepository $serviceRepository`

### Étape 6 : Le Controller Resolver prépare l'exécution

Symfony utilise le **Dependency Injection** :

```php
// Symfony voit que la méthode list() a besoin de ServiceRepository
// Il le récupère automatiquement depuis le Container de Services
$serviceRepository = $container->get(ServiceRepository::class);
```

### Étape 7 : Le Controller est exécuté

```php
$controller = new ServicesController();
$response = $controller->list($serviceRepository);
```

Exemple de ce qui se passe dans le Controller :

```php
public function list(ServiceRepository $serviceRepository): Response
{
    // 1. Récupère les données en base via Doctrine
    $services = $serviceRepository->findAll();
    
    // 2. Rend le template Twig
    return $this->render('client/services/list.html.twig', [
        'services' => $services
    ]);
}
```

### Étape 8 : Twig génère le HTML

```php
// Symfony charge le template
$twig = $container->get('twig');

// Twig compile le template (ou utilise le cache)
$html = $twig->render('client/services/list.html.twig', [
    'services' => $services
]);

// Une Response HTTP est créée
$response = new Response($html, 200, [
    'Content-Type' => 'text/html'
]);
```

### Étape 9 : La Response est envoyée au navigateur

```php
// Le Kernel envoie la réponse
$response->send();
// Envoie les headers HTTP
// Envoie le contenu HTML
```

### Étape 10 : Le Kernel se termine

```php
$kernel->terminate($request, $response);
// Nettoyage, fermeture des connexions DB, etc.
```

---

## 5. Le rôle du dossier `public/`

### Qu'est-ce qui peut être dans `public/` ?

```
public/
├── index.php           # Point d'entrée (obligatoire)
├── robots.txt          # Instructions pour les robots
├── css/               # Fichiers CSS publics
│   ├── global.css
│   └── admin.css
├── images/            # Images statiques
│   └── backgrounds/
├── js/                # JavaScript public
└── uploads/           # Fichiers uploadés par les utilisateurs
    ├── content/
    ├── testimonies/
    └── users/
```

### Fichiers statiques

Quand on accède à `http://localhost/css/global.css` :

1. Le serveur web vérifie si le fichier **existe physiquement**
2. **Si oui** : Le serveur web le sert directement (pas de PHP, pas de Symfony)
3. **Si non** : La requête est redirigée vers `index.php` (qui retournera probablement une erreur 404)

### Sécurité : Pourquoi TOUT le reste doit être HORS de `public/` ?

```
Projet Symfony
├── public/              ← ACCESSIBLE PAR LE WEB
│   └── index.php
├── src/                ← INACCESSIBLE (PHP uniquement)
├── config/             ← INACCESSIBLE (configuration sensible)
├── var/                ← INACCESSIBLE (cache, logs)
│   ├── cache/
│   └── log/
├── vendor/             ← INACCESSIBLE (librairies)
└── .env                ← INACCESSIBLE (secrets, mots de passe)
```

**Si on mettait tout dans `public/`**, on pourrait :
- Lire les fichiers de configuration (avec les secrets)
- Accéder aux logs
- Voir le code source de l'application
- Télécharger la base de données

**C'est pour ça que seul `public/` est accessible !**

---

## 6. Mode développement vs. production

### Différences selon l'environnement

#### En développement (`APP_ENV=dev`)

```
Requête → index.php
    ↓
Kernel démarre en mode DEBUG
    ↓
- Affichage des erreurs détaillées
- Profiler Symfony activé (barre de debug)
- Cache désactivé ou invalidé souvent
- Logs très verbeux
    ↓
Réponse avec Web Debug Toolbar
```

#### En production (`APP_ENV=prod`)

```
Requête → index.php
    ↓
Kernel démarre en mode PRODUCTION
    ↓
- Erreurs génériques (pas de détails)
- Pas de profiler
- Cache agressif (tout est mis en cache)
- Logs minimes
- Optimisations activées
    ↓
Réponse optimisée
```

---

## 7. Le système de cache

### Où est le cache ?

```
var/cache/
├── dev/              # Cache de développement
│   ├── Container/    # Services compilés
│   ├── pools/        # Cache applicatif
│   └── twig/         # Templates Twig compilés
└── prod/             # Cache de production
    └── ...
```

### Quand le cache est-il utilisé ?

1. **Premier chargement** (cache vide) :
   ```
   Requête → Kernel boot → Compile tout → Sauvegarde cache → Réponse
   (Lent : 500-1000ms)
   ```

2. **Chargements suivants** (cache présent) :
   ```
   Requête → Kernel boot → Charge cache → Réponse
   (Rapide : 50-100ms)
   ```

3. **Après modification du code** :
   ```bash
   php bin/console cache:clear
   ```
   Vide le cache, tout est recompilé au prochain chargement.

---

## 8. Performance et optimisations

### Preload PHP (production)

Dans `config/preload.php`, on peut précharger des classes PHP en mémoire :

```php
// Charge en mémoire les classes les plus utilisées
opcache_compile_file(__DIR__.'/../src/Kernel.php');
opcache_compile_file(__DIR__.'/../src/Controller/HomeController.php');
// etc.
```

### OPcache

PHP peut compiler les fichiers PHP en bytecode et les garder en mémoire :
- **Sans OPcache** : Chaque requête recompile tout le code PHP
- **Avec OPcache** : Le code est compilé une fois, réutilisé ensuite

### CDN pour les assets

En production, les fichiers CSS/JS/images peuvent être servis par un CDN :

```twig
{# Au lieu de : #}
<link rel="stylesheet" href="/css/global.css">

{# On peut avoir : #}
<link rel="stylesheet" href="https://cdn.example.com/css/global.css">
```

---

## 9. Schéma récapitulatif complet

```
┌─────────────────────────────────────────────────────────────┐
│ 1. UTILISATEUR tape l'URL dans le navigateur               │
│    http://localhost/services                                │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. SERVEUR WEB (Apache/Nginx) reçoit la requête            │
│    - Vérifie si c'est un fichier statique (/css, /images)  │
│    - Si non → Redirige vers public/index.php               │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. public/index.php est exécuté                             │
│    - Charge vendor/autoload_runtime.php                     │
│    - Lit les variables d'environnement (.env)               │
│    - Crée le Kernel                                         │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. KERNEL démarre (boot)                                    │
│    - Charge les bundles (config/bundles.php)               │
│    - Compile le Container de Services                       │
│    - Initialise le Router                                   │
│    - Lit la configuration (config/packages/*.yaml)          │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. REQUEST transformé en objet Symfony                      │
│    Request {                                                │
│      pathInfo: '/services'                                  │
│      method: 'GET'                                          │
│      headers: [...]                                         │
│    }                                                        │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. ROUTER trouve la route correspondante                    │
│    Route '/services' → ServicesController::list()           │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. DEPENDENCY INJECTION prépare les paramètres              │
│    - ServiceRepository injecté automatiquement              │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. CONTROLLER est exécuté                                   │
│    public function list(ServiceRepository $repo) {          │
│      $services = $repo->findAll(); // Requête en base       │
│      return $this->render('...', ['services' => $services]);│
│    }                                                        │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. DOCTRINE récupère les données                            │
│    - Connexion à la base de données                         │
│    - Exécute SELECT * FROM service                          │
│    - Transforme les résultats en objets Service             │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 10. TWIG génère le HTML                                     │
│    - Charge le template (client/services/list.html.twig)   │
│    - Compile (ou utilise le cache)                          │
│    - Remplace {{ service.name }} par les vraies valeurs     │
│    - Génère le HTML final                                   │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 11. RESPONSE est créée                                      │
│    Response {                                               │
│      status: 200                                            │
│      headers: ['Content-Type' => 'text/html']              │
│      content: '<html>...</html>'                           │
│    }                                                        │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 12. Réponse envoyée au NAVIGATEUR                           │
│    - Headers HTTP envoyés                                   │
│    - Contenu HTML envoyé                                    │
└─────────────────┬───────────────────────────────────────────┘
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ 13. NAVIGATEUR affiche la page                              │
│    - Parse le HTML                                          │
│    - Charge les CSS (nouvelles requêtes vers /css/...)     │
│    - Charge les JS (nouvelles requêtes vers /js/...)       │
│    - Affiche la page à l'utilisateur                        │
└─────────────────────────────────────────────────────────────┘
```

---

## 10. Debugging : Suivre une requête en temps réel

### Avec le Profiler Symfony (mode dev)

1. Activez le profiler dans `.env` :
   ```env
   APP_ENV=dev
   APP_DEBUG=1
   ```

2. Visitez n'importe quelle page

3. En bas de la page, une **barre de debug** apparaît avec :
   - Temps d'exécution total
   - Utilisation mémoire
   - Nombre de requêtes SQL
   - Routes matchées
   - Services utilisés
   - Events déclenchés

4. Cliquez sur l'icône pour voir le détail complet

### Avec les logs

```bash
# Surveiller les logs en temps réel
tail -f var/log/dev.log
```

Pendant une requête, vous verrez :
```
[2026-02-03 10:30:45] request.INFO: Matched route "app_services_list"
[2026-02-03 10:30:45] doctrine.DEBUG: SELECT * FROM service
[2026-02-03 10:30:45] request.INFO: Response returned (200)
```

### Avec XDebug (pour le step-by-step)

Avec XDebug configuré, vous pouvez mettre des breakpoints et voir :
- Chaque ligne exécutée
- Les valeurs des variables à chaque instant
- La pile d'appels complète

---

## 11. Questions fréquentes

### Pourquoi toutes les requêtes passent par index.php ?

C'est le **Front Controller Pattern** :
- ✅ Un seul point d'entrée = plus facile à sécuriser
- ✅ Initialisation centralisée (connexion DB, session, etc.)
- ✅ Routing flexible (URL propres sans .php)
- ✅ Middleware global (authentification, cache, etc.)

### Que se passe-t-il si j'accède directement à un fichier PHP ?

Si vous avez `public/test.php` et que vous visitez `/test.php` :
- Le serveur web exécutera ce fichier **directement**
- Symfony ne sera **pas chargé**
- Pas de routing, pas de services, pas de sécurité
- ⚠️ **À éviter absolument** (sauf pour des scripts très spécifiques)

### Puis-je mettre des sous-dossiers dans public/ ?

Oui ! C'est même recommandé :
```
public/
├── index.php
├── css/
├── js/
├── images/
├── fonts/
└── uploads/
```

Ces fichiers seront servis **directement** par le serveur web (sans passer par Symfony).

### Comment Symfony sait quelle route appeler ?

1. Le Router lit **toutes** les routes au démarrage
2. Il crée une "map" (tableau associatif) :
   ```
   '/services' → ServicesController::list
   '/contact' → ContactController::index
   '/services/{id}' → ServicesController::show
   ```
3. Quand une requête arrive, il cherche dans cette map
4. Si trouvé → Appelle le controller
5. Si pas trouvé → Erreur 404

### Peut-on avoir plusieurs points d'entrée ?

Techniquement oui, mais c'est déconseillé. Par exemple :
```
public/
├── index.php      # Site principal
└── api.php        # API séparée
```

Mais dans ce cas, mieux vaut utiliser :
- Des **sous-domaines** : `www.example.com` et `api.example.com`
- Ou des **routes** : `/` et `/api/*`

---

## 12. Résumé en une phrase

> **Toutes les requêtes HTTP arrivent à `public/index.php`, qui initialise Symfony (Kernel), trouve la bonne route, exécute le Controller correspondant, génère le HTML via Twig, et renvoie la réponse au navigateur.**

---

## Pour aller plus loin

- **Symfony HTTP Foundation** : Comment Symfony gère les requêtes/réponses
- **Symfony Kernel** : Architecture interne du Kernel
- **Symfony Routing** : Système de routing avancé
- **Symfony EventDispatcher** : Events déclenchés pendant le cycle de vie
- **Symfony Service Container** : Dependency Injection en profondeur

N'hésite pas à explorer le code source de Symfony dans `vendor/symfony/` pour voir comment tout cela fonctionne en interne !
