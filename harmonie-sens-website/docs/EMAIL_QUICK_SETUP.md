# Configuration rapide de l'envoi d'emails

## Étapes de configuration

### 1. Modifier le fichier .env

Ouvrez le fichier `.env` et modifiez ces lignes :

```env
# Remplacez YOUR_EMAIL@domain.com par votre email IONOS
# Remplacez YOUR_PASSWORD par votre mot de passe email
MAILER_DSN=smtp://YOUR_EMAIL@domain.com:YOUR_PASSWORD@smtp.ionos.fr:465?encryption=ssl

# L'email qui apparaîtra comme expéditeur
MAIL_FROM_ADDRESS=noreply@harmonieetsens.fr
MAIL_FROM_NAME="Harmonie & Sens"

# L'email où VOUS voulez recevoir les notifications de contact
MAIL_ADMIN_ADDRESS=contact@harmonieetsens.fr
```

### 2. Exemple de configuration complète

```env
MAILER_DSN=smtp://contact@harmonieetsens.fr:MonMotDePasse123@smtp.ionos.fr:465?encryption=ssl
MAIL_FROM_ADDRESS=noreply@harmonieetsens.fr
MAIL_FROM_NAME="Harmonie & Sens"
MAIL_ADMIN_ADDRESS=admin@harmonieetsens.fr
```

### 3. Pour tester localement sans envoyer de vrais emails

Créez un fichier `.env.local` avec :

```env
MAILER_DSN=null://null
```

Les emails seront capturés dans le profiler Symfony (visible dans la barre de debug).

### 4. Vérifier que ça fonctionne

1. Lancez votre serveur : `symfony server:start` ou `php -S localhost:8000 -t public`
2. Allez sur la page de contact
3. Envoyez un message
4. Vérifiez que l'email arrive à l'adresse configurée dans `MAIL_ADMIN_ADDRESS`

## Changer l'email de destination

Pour recevoir les notifications sur un autre email, modifiez simplement :

```env
MAIL_ADMIN_ADDRESS=votre.nouvel.email@example.com
```

Redémarrez le serveur et c'est tout !

## Problèmes courants

### "Connection could not be established"
- Vérifiez votre nom d'utilisateur et mot de passe
- Assurez-vous que le port 465 n'est pas bloqué par un firewall

### "Authentication failed"
- Vérifiez que vous utilisez le bon mot de passe
- Vérifiez que l'email est bien configuré chez IONOS

### L'email n'arrive pas
- Vérifiez vos spams
- Vérifiez les logs Symfony : `var/log/dev.log`
- Assurez-vous que `MAIL_ADMIN_ADDRESS` est correct

## Documentation complète

Consultez `docs/EMAIL_SERVICE_GUIDE.md` pour :
- Utiliser le service dans d'autres contrôleurs
- Créer de nouveaux templates d'email
- Envoyer différents types d'emails

## Structure mise en place

✅ Service `EmailService` centralisé et réutilisable  
✅ Configuration flexible via variables d'environnement  
✅ Templates d'email professionnels  
✅ Gestion des erreurs avec logs  
✅ Possibilité d'envoyer à n'importe quel email  
✅ Prêt pour de futurs usages (commandes, rendez-vous, etc.)
