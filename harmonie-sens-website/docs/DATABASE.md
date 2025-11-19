# Structure de la Base de Données - Harmonie & Sens

## Entités à créer

### 1. User (Utilisateurs admin)
```
- id (int, auto)
- email (string, unique)
- roles (json)
- password (string, hashed)
- firstName (string)
- lastName (string)
- createdAt (datetime)
- updatedAt (datetime)
```

### 2. Content (Pages et contenus dynamiques)
```
- id (int, auto)
- title (string)
- slug (string, unique)
- content (text)
- type (string) // 'page', 'service', 'article'
- isPublished (boolean)
- metaDescription (string, nullable)
- metaKeywords (string, nullable)
- createdAt (datetime)
- updatedAt (datetime)
- author (relation: User)
```

### 3. Message (Messages de contact)
```
- id (int, auto)
- firstName (string)
- lastName (string)
- email (string)
- phone (string, nullable)
- subject (string)
- message (text)
- isRead (boolean)
- isArchived (boolean)
- createdAt (datetime)
- repliedAt (datetime, nullable)
```

### 4. Service (Services du cabinet)
```
- id (int, auto)
- name (string)
- slug (string, unique)
- shortDescription (text)
- fullDescription (text)
- price (string, nullable)
- icon (string, nullable)
- displayOrder (int)
- isActive (boolean)
- createdAt (datetime)
- updatedAt (datetime)
```

### 5. Testimony (Témoignages clients)
```
- id (int, auto)
- clientName (string)
- position (string, nullable)
- organization (string)
- content (text)
- rating (int) // 1-5
- isPublished (boolean)
- displayOrder (int)
- createdAt (datetime)
```

### 6. Sector (Secteurs d'intervention)
```
- id (int, auto)
- name (string)
- slug (string, unique)
- description (text)
- icon (string, nullable)
- displayOrder (int)
- isActive (boolean)
```

### 7. Formation (Formations proposées)
```
- id (int, auto)
- title (string)
- slug (string, unique)
- description (text)
- duration (string)
- price (string)
- maxParticipants (int, nullable)
- objectives (text)
- isActive (boolean)
- createdAt (datetime)
- updatedAt (datetime)
```

### 8. Setting (Paramètres du site)
```
- id (int, auto)
- settingKey (string, unique)
- settingValue (text)
- description (string, nullable)
- updatedAt (datetime)
```

## Relations

- Content → User (ManyToOne) : author
- Message : pas de relations
- Service : pas de relations (standalone)
- Testimony : pas de relations
- Sector : pas de relations
- Formation : pas de relations
- Setting : pas de relations

## Commandes pour créer les entités

```bash
# Créer l'entité User avec MakerBundle
php bin/console make:user

# Créer les autres entités
php bin/console make:entity Content
php bin/console make:entity Message
php bin/console make:entity Service
php bin/console make:entity Testimony
php bin/console make:entity Sector
php bin/console make:entity Formation
php bin/console make:entity Setting

# Générer la migration
php bin/console make:migration

# Exécuter la migration
php bin/console doctrine:migrations:migrate
```

## Données initiales à créer

### Settings
- site_name: "Harmonie & Sens"
- site_tagline: "Conduire, relier et restaurer l'équilibre au cœur des organisations"
- contact_email: "contact@harmonieetsens.fr"
- contact_phone: "06 83 42 40 12"
- contact_address: "Interventions sur le territoire national"

### Services initiaux
- Direction de transition
- Diagnostic & Audit
- Formations et webinaires
- Accompagnement individuel

### Secteurs initiaux
- Personnes âgées
- Adultes en situation de handicap
- Enfance & protection de l'enfance
- Champ sanitaire
