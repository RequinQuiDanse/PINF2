# Guide d'utilisation du Service Email

## Configuration

### 1. Variables d'environnement (.env ou .env.local)

```env
# Configuration SMTP IONOS
MAILER_DSN=smtp://votre.email@domain.com:votre_mot_de_passe@smtp.ionos.fr:465?encryption=ssl

# Email d'expédition
MAIL_FROM_ADDRESS=noreply@harmonieetsens.fr
MAIL_FROM_NAME="Harmonie & Sens"

# Email de réception des notifications
MAIL_ADMIN_ADDRESS=contact@harmonieetsens.fr
```

**Serveur SMTP IONOS :**
- Serveur : `smtp.ionos.fr`
- Port : `465`
- Encryption : `SSL`
- Authentification requise : `Oui`

### 2. Modifier l'email de destination

Pour changer l'email où vous recevez les notifications, modifiez simplement la variable `MAIL_ADMIN_ADDRESS` dans votre fichier `.env` ou `.env.local`.

## Utilisation du Service EmailService

### Injection dans un Controller

```php
use App\Service\EmailService;

class MyController extends AbstractController
{
    #[Route('/my-route', name: 'app_my_route')]
    public function myAction(EmailService $emailService): Response
    {
        // Utiliser le service ici
    }
}
```

### Méthodes disponibles

#### 1. Notification de contact

Envoie un email de notification à l'administrateur (ou à un email spécifique) :

```php
// Utilise l'email admin par défaut (MAIL_ADMIN_ADDRESS)
$emailService->sendContactNotification($message);

// Ou spécifier un email destinataire différent
$emailService->sendContactNotification($message, 'autre.email@example.com');
```

#### 2. Confirmation à l'expéditeur

Envoie un email de confirmation à la personne qui a envoyé le message :

```php
$emailService->sendContactConfirmation($message);
```

#### 3. Email personnalisé (méthode générique)

Pour envoyer n'importe quel type d'email :

```php
$emailService->sendTemplatedEmail(
    recipient: 'destinataire@example.com',
    subject: 'Mon sujet',
    template: 'emails/mon_template.html.twig',
    context: [
        'variable1' => 'valeur1',
        'variable2' => 'valeur2',
    ]
);
```

## Exemples d'utilisation

### Exemple 1 : Formulaire de contact (implémenté)

```php
// Dans ContactController
if ($form->isSubmitted() && $form->isValid()) {
    $em->persist($message);
    $em->flush();

    // Envoyer notification à l'admin
    $emailSent = $emailService->sendContactNotification($message);

    // Optionnel : confirmation à l'expéditeur
    $emailService->sendContactConfirmation($message);

    if ($emailSent) {
        $this->addFlash('success', 'Message envoyé avec succès.');
    }
}
```

### Exemple 2 : Notification de commande (futur usage)

```php
// Créer un template emails/order_confirmation.html.twig
// Puis utiliser :

$emailService->sendTemplatedEmail(
    recipient: $order->getCustomerEmail(),
    subject: 'Confirmation de commande #' . $order->getId(),
    template: 'emails/order_confirmation.html.twig',
    context: [
        'order' => $order,
        'customer' => $customer,
    ]
);
```

### Exemple 3 : Email de bienvenue (futur usage)

```php
$emailService->sendTemplatedEmail(
    recipient: $user->getEmail(),
    subject: 'Bienvenue sur Harmonie & Sens',
    template: 'emails/welcome.html.twig',
    context: [
        'user' => $user,
    ]
);
```

### Exemple 4 : Notification de rendez-vous (futur usage)

```php
// Notification à l'admin
$emailService->sendTemplatedEmail(
    recipient: $this->getParameter('app.email.admin_address'),
    subject: 'Nouveau rendez-vous demandé',
    template: 'emails/appointment_notification.html.twig',
    context: [
        'appointment' => $appointment,
    ]
);

// Confirmation au client
$emailService->sendTemplatedEmail(
    recipient: $appointment->getEmail(),
    subject: 'Votre demande de rendez-vous',
    template: 'emails/appointment_confirmation.html.twig',
    context: [
        'appointment' => $appointment,
    ]
);
```

## Gestion des erreurs

Le service gère automatiquement les erreurs :
- Retourne `true` si l'email est envoyé avec succès
- Retourne `false` en cas d'erreur
- Les erreurs sont automatiquement loggées dans les logs Symfony

```php
$emailSent = $emailService->sendContactNotification($message);

if ($emailSent) {
    // Succès
    $this->addFlash('success', 'Email envoyé !');
} else {
    // Erreur (déjà loggée)
    $this->addFlash('warning', 'Email non envoyé, mais message enregistré.');
}
```

## Créer un nouveau template d'email

1. Créer un fichier dans `templates/emails/`
2. Utiliser la structure HTML de base (voir les templates existants)
3. Utiliser les variables Twig passées dans le contexte

Exemple de template (`templates/emails/my_template.html.twig`) :

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
        <div style="background: #fff; padding: 30px; border-radius: 8px;">
            <h2 style="color: #7A8F6E;">{{ title }}</h2>
            <p>{{ content }}</p>
        </div>
    </div>
</body>
</html>
```

## Tests en environnement de développement

Pour tester sans envoyer de vrais emails, utilisez le mode `null` :

```env
# Dans .env.local
MAILER_DSN=null://null
```

Puis consultez les emails dans le profiler Symfony (barre de debug).

## Production

En production, assurez-vous que :
1. Le fichier `.env.local` ou variables d'environnement contiennent les vrais identifiants IONOS
2. Les identifiants ne sont jamais commités dans le dépôt Git
3. Les logs sont surveillés pour détecter les erreurs d'envoi

## Support

Pour toute question sur l'utilisation du service Email, consulter :
- La classe `src/Service/EmailService.php` (bien documentée)
- Les templates dans `templates/emails/`
- La configuration dans `config/services.yaml`
