# Workflow Symfony - Comprendre le fonctionnement

## Vue d'ensemble

Ce document explique comment Symfony traite une requête HTTP depuis l'URL jusqu'à l'affichage de la page dans le navigateur.

## 1. Le cycle de vie d'une requête

```
URL du navigateur
    ↓
public/index.php (Point d'entrée)
    ↓
Routing (config/routes.yaml + Annotations)
    ↓
Controller (src/Controller/)
    ↓
Services & Entités (si nécessaire)
    ↓
Template Twig (templates/)
    ↓
HTML envoyé au navigateur
```

## 2. Les Routes - Comment Symfony sait quelle page afficher

### Qu'est-ce qu'une route ?

Une route fait le lien entre une URL et une fonction dans un Controller.

### Exemple concret du projet

Dans `src/Controller/HomeController.php` :

```php
#[Route('/', name: 'app_home')]
public function index(): Response
{
    return $this->render('client/home/index.html.twig');
}
```

**Explication** :
- `#[Route('/', name: 'app_home')]` = Annotation de route
- `'/'` = L'URL (ici la page d'accueil)
- `name: 'app_home'` = Nom interne de la route (utilisé dans le code)
- La fonction `index()` sera exécutée quand on visite `/`

### Autre exemple avec paramètres

```php
#[Route('/services/{id}', name: 'app_service_detail')]
public function show(int $id, ServiceRepository $serviceRepository): Response
{
    $service = $serviceRepository->find($id);
    
    return $this->render('client/services/show.html.twig', [
        'service' => $service
    ]);
}
```

**Explication** :
- `/services/{id}` = URL dynamique, `{id}` est un paramètre
- `int $id` = Symfony récupère automatiquement l'ID depuis l'URL
- Si on visite `/services/5`, alors `$id = 5`

## 3. Les Controllers - La logique métier

### Rôle du Controller

Le Controller est le **chef d'orchestre** :
1. Reçoit la requête HTTP
2. Récupère les données nécessaires (via les Repositories)
3. Traite la logique métier
4. Envoie les données au template Twig
5. Retourne la réponse HTML

### Structure d'un Controller

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MonController extends AbstractController
{
    #[Route('/ma-page', name: 'app_ma_page')]
    public function maFonction(): Response
    {
        // Logique ici
        
        return $this->render('mon_template.html.twig', [
            'variable' => 'valeur'
        ]);
    }
}
```

### Exemple réel du projet - ContactController

```php
#[Route('/contact', name: 'app_contact')]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    // 1. Créer un formulaire
    $message = new Message();
    $form = $this->createForm(ContactType::class, $message);
    
    // 2. Traiter la soumission du formulaire
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // 3. Sauvegarder en base de données
        $entityManager->persist($message);
        $entityManager->flush();
        
        // 4. Message flash de confirmation
        $this->addFlash('success', 'Votre message a été envoyé !');
        
        return $this->redirectToRoute('app_contact');
    }
    
    // 5. Afficher le template avec le formulaire
    return $this->render('client/contact/index.html.twig', [
        'form' => $form->createView()
    ]);
}
```

## 4. Les Entités - Les données

### Qu'est-ce qu'une Entité ?

Une Entité est une **classe PHP qui représente une table en base de données**.

Exemple : `src/Entity/Service.php`

```php
#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    // Getters et setters...
}
```

**Correspondance** :
- Classe `Service` = Table `service` en base de données
- Propriété `$name` = Colonne `name` dans la table
- `#[ORM\Column]` = Annotations Doctrine pour la configuration

## 5. Les Repositories - Récupérer les données

### Rôle du Repository

Le Repository permet de **faire des requêtes en base de données** sans écrire de SQL.

### Exemple simple

```php
// Dans un Controller
public function index(ServiceRepository $serviceRepository): Response
{
    // Récupère tous les services
    $services = $serviceRepository->findAll();
    
    // Récupère un service par son ID
    $service = $serviceRepository->find(1);
    
    // Récupère les services actifs (méthode personnalisée)
    $activeServices = $serviceRepository->findBy(['isActive' => true]);
    
    return $this->render('template.html.twig', [
        'services' => $services
    ]);
}
```

### Méthodes courantes

- `find($id)` : Trouve par ID
- `findAll()` : Trouve tout
- `findBy(['champ' => 'valeur'])` : Trouve avec critères
- `findOneBy(['champ' => 'valeur'])` : Trouve un seul résultat

## 6. Les Templates Twig - L'affichage HTML

### Qu'est-ce que Twig ?

Twig est un **moteur de templates** qui permet de générer du HTML dynamique.

### Syntaxe de base

```twig
{# Ceci est un commentaire #}

{# Afficher une variable #}
{{ variable }}

{# Structure de contrôle #}
{% if condition %}
    <p>Vrai</p>
{% else %}
    <p>Faux</p>
{% endif %}

{# Boucle #}
{% for service in services %}
    <h2>{{ service.name }}</h2>
    <p>{{ service.description }}</p>
{% endfor %}
```

### Héritage de templates

**Template parent** (`templates/base.html.twig`) :
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Mon Site{% endblock %}</title>
</head>
<body>
    {% block body %}{% endblock %}
</body>
</html>
```

**Template enfant** (`templates/client/home/index.html.twig`) :
```twig
{% extends 'base.html.twig' %}

{% block title %}Accueil{% endblock %}

{% block body %}
    <h1>Bienvenue !</h1>
{% endblock %}
```

### Passer des données du Controller au Template

**Controller** :
```php
return $this->render('template.html.twig', [
    'titre' => 'Mon titre',
    'services' => $services,
    'nombre' => 42
]);
```

**Template** :
```twig
<h1>{{ titre }}</h1>
<p>Nombre : {{ nombre }}</p>

{% for service in services %}
    <div>{{ service.name }}</div>
{% endfor %}
```

## 7. Exemple complet - Afficher la liste des services

### Étape 1 : Créer la route et le controller

```php
// src/Controller/ServicesController.php
#[Route('/services', name: 'app_services_list')]
public function list(ServiceRepository $serviceRepository): Response
{
    // Récupère tous les services en base de données
    $services = $serviceRepository->findAll();
    
    // Envoie au template
    return $this->render('client/services/list.html.twig', [
        'services' => $services
    ]);
}
```

### Étape 2 : Créer le template

```twig
{# templates/client/services/list.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Nos Services{% endblock %}

{% block body %}
    <h1>Nos Services</h1>
    
    <div class="services-grid">
        {% for service in services %}
            <div class="service-card">
                <h2>{{ service.name }}</h2>
                <p>{{ service.description }}</p>
                <a href="{{ path('app_service_detail', {id: service.id}) }}">
                    En savoir plus
                </a>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

### Étape 3 : Visiter la page

Quand on tape `http://localhost/services` dans le navigateur :

1. **Routing** : Symfony trouve la route `/services` → `app_services_list`
2. **Controller** : Exécute `ServicesController::list()`
3. **Repository** : Récupère tous les services depuis la base de données
4. **Template** : Twig génère le HTML avec les données
5. **Réponse** : Le HTML est envoyé au navigateur

## 8. Les Formulaires

### Créer un formulaire

**Type de formulaire** (`src/Form/ContactType.php`) :
```php
class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ]);
    }
}
```

**Controller** :
```php
#[Route('/contact', name: 'app_contact')]
public function contact(Request $request, EntityManagerInterface $em): Response
{
    $message = new Message();
    $form = $this->createForm(ContactType::class, $message);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($message);
        $em->flush();
        
        return $this->redirectToRoute('app_contact_success');
    }
    
    return $this->render('contact.html.twig', [
        'form' => $form
    ]);
}
```

**Template** :
```twig
{{ form_start(form) }}
    {{ form_row(form.name) }}
    {{ form_row(form.email) }}
    {{ form_row(form.message) }}
    {{ form_row(form.submit) }}
{{ form_end(form) }}
```

## 9. Générer des URLs dans les templates

### Lien vers une route

```twig
{# Lien simple #}
<a href="{{ path('app_home') }}">Accueil</a>

{# Lien avec paramètres #}
<a href="{{ path('app_service_detail', {id: 5}) }}">Service #5</a>

{# URL absolue #}
<a href="{{ url('app_home') }}">Accueil</a>
```

### Dans un Controller

```php
// Redirection
return $this->redirectToRoute('app_home');

// Redirection avec paramètres
return $this->redirectToRoute('app_service_detail', ['id' => 5]);
```

## 10. Structure d'organisation du projet

```
src/
├── Controller/        # Logique de traitement des requêtes
│   ├── Admin/        # Controllers pour l'admin
│   └── ...           # Controllers pour le front
├── Entity/           # Classes représentant les tables DB
├── Form/             # Définition des formulaires
└── Repository/       # Requêtes personnalisées en base

templates/
├── admin/            # Templates de l'administration
├── client/           # Templates du site public
└── components/       # Composants réutilisables (navbar, footer)

config/
├── routes.yaml       # Configuration globale des routes
└── packages/         # Configuration des bundles Symfony
```

## 11. Commandes utiles

```bash
# Voir toutes les routes disponibles
php bin/console debug:router

# Voir les détails d'une route spécifique
php bin/console debug:router app_home

# Créer une entité
php bin/console make:entity

# Créer un controller
php bin/console make:controller

# Créer un formulaire
php bin/console make:form

# Créer/exécuter une migration
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
```

## 12. Résumé du workflow

1. **L'utilisateur visite une URL** (ex: `/services`)
2. **Le Routing trouve la route** correspondante dans les annotations
3. **Le Controller associé est exécuté**
4. **Le Controller récupère les données** via les Repositories si nécessaire
5. **Le Controller envoie les données au template Twig**
6. **Twig génère le HTML** en utilisant les données
7. **Le HTML est renvoyé au navigateur**

## 13. Pour aller plus loin

- **Services** : Classes réutilisables injectées dans les Controllers
- **Events & Listeners** : Réagir à des événements dans l'application
- **Security** : Gestion de l'authentification et des autorisations
- **API Platform** : Créer des APIs REST facilement
- **Webpack Encore** : Gestion des assets CSS/JS

---

## Aide-mémoire rapide

| Besoin | Outil Symfony |
|--------|---------------|
| Afficher une page | Controller + Template |
| Récupérer des données DB | Repository |
| Créer/Modifier des données | EntityManager |
| Formulaire | FormType + handleRequest |
| Lien vers une page | `path('nom_route')` |
| Redirection | `redirectToRoute()` |

N'hésite pas à explorer les fichiers existants du projet pour voir des exemples concrets !
