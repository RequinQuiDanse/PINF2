# Documentation Base de Données - Harmonie & Sens

## Entités Implémentées

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

### 2. Message (Messages de contact)
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

### 3. Service (Services du cabinet)
```
- id (int, auto)
- name (string)
- slug (string, unique)
- description (text, nullable)
- priceMin (decimal, nullable)
- priceMax (decimal, nullable)
- pricingUnit (string, nullable) // 'jour', 'heure', 'mission'
- pricingDetails (text, nullable)
- isActive (boolean)
- createdAt (datetime)
- updatedAt (datetime)
```

### 4. Testimony (Témoignages clients)
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

### 5. Person (Contacts/Leads)
```
- id (int, auto)
- firstName (string)
- lastName (string)
- email (string)
- phone (string, nullable)
- organization (string, nullable)
- subscribedToNewsletter (boolean)
- createdAt (datetime)
```

### 6. Webinar (Webinaires/Formations en ligne)
```
- id (int, auto)
- name (string)
- description (text)
- date (datetime)
- link (string)
- duration (int, nullable) // en minutes
- isActive (boolean)
- createdAt (datetime)
```

### 7. Timetable (Horaires de disponibilité)
```
- id (int, auto)
- dayOfWeek (string) // 'lundi', 'mardi', etc.
- startTime (string) // '09:00'
- endTime (string) // '17:00'
- isAvailable (boolean)
- notes (string, nullable)
```

### 8. ServicePricing (Tarification des services)
```
- id (int, auto)
- serviceName (string)
- description (text, nullable)
- unitPrice (decimal, nullable)
- dailyPricing (json, nullable) // ['1-5' => 1000, '6-10' => 900, etc.]
- pricingUnit (string, nullable) // 'jour', 'heure', 'mission'
- isActive (boolean)
- updatedAt (datetime)
```

---

## Relations

Toutes les entités sont actuellement **standalone** (pas de relations entre elles).

---

## Commandes Doctrine

### Commandes utiles
```bash
# Voir l'état de la base de données
php bin/console doctrine:schema:validate

# Créer un administrateur
php bin/console app:create-admin

# Initialiser les services
php bin/console app:init-services

# Tester l'envoi d'emails
php bin/console app:test-email

# Générer une migration après modification d'entité
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

---

## Données Initiales

### Services (via commande app:init-services)
Les services suivants peuvent être initialisés automatiquement :
- Direction de transition
- Diagnostic & Audit
- Formations et webinaires
- Accompagnement individuel

### Horaires de disponibilité (Timetable)
```
Lundi - Vendredi : 09:00 - 18:00
```

### Informations de contact
- Email : contact@harmonieetsens.fr
- Téléphone : 06 83 42 40 12
- Zone d'intervention : National
