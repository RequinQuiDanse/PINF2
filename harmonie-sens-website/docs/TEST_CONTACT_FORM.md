# Test du formulaire de contact

## 🧪 Instructions de test

### 1. Lancer le serveur Symfony

```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

### 2. Accéder au formulaire

Ouvrir dans votre navigateur : `http://localhost:8000/contact`

### 3. Remplir le formulaire

- **Prénom** : Test
- **Nom** : Utilisateur
- **Email** : votre.email@example.com
- **Téléphone** : 06 12 34 56 78 (optionnel)
- **Sujet** : Test d'envoi email
- **Message** : Ceci est un message de test pour vérifier l'envoi d'email.

### 4. Soumettre et vérifier

Après soumission, vous devriez voir :

✅ **Message de succès** : "Votre message a été envoyé avec succès..."
- L'email a été envoyé à `contact@3s-managers.fr`

⚠️ **Message d'avertissement** : "Votre message a été enregistré mais..."
- Le message est sauvegardé en BDD mais l'email n'a pas été envoyé
- Vérifier la configuration SMTP

❌ **Message d'erreur** : "Une erreur est survenue..."
- Une exception s'est produite
- Vérifier les logs

### 5. Vérifier la réception

- Allez sur la boîte mail `contact@3s-managers.fr`
- Vérifiez les spams si nécessaire
- L'email devrait arriver dans 1-5 minutes

## 🔍 Debug

### Vérifier que le message est en BDD

```bash
php bin/console doctrine:query:sql "SELECT * FROM message ORDER BY created_at DESC LIMIT 1"
```

### Voir les logs

```bash
tail -f var/log/dev.log
```

### Test via commande (bypass le formulaire)

```bash
php bin/console app:test-email
```

## 🎯 Ce qui doit fonctionner maintenant

1. ✅ Formulaire soumis → Message sauvegardé en BDD
2. ✅ Email envoyé à `contact@3s-managers.fr`
3. ✅ Message flash affiché (success/warning/error)
4. ✅ Redirection vers le formulaire vide

## 🐛 Problèmes courants

### "Votre message a été enregistré mais l'email n'a pas pu être envoyé"

**Cause** : L'email n'a pas pu être envoyé mais le message est sauvegardé.

**Solutions** :
1. Vérifier `.env.local` : `MAILER_DSN`, `MAIL_FROM_ADDRESS`, `MAIL_ADMIN_ADDRESS`
2. Vérifier que le test en ligne de commande fonctionne : `php bin/console app:test-email`
3. Vérifier les logs : `tail -f var/log/dev.log`

### "Une erreur est survenue"

**Cause** : Exception lors de l'envoi ou de la sauvegarde.

**Solutions** :
1. Vérifier la BDD est accessible
2. Vérifier les logs pour voir l'erreur exacte
3. Vérifier que le service EmailService est bien configuré dans `config/services.yaml`

### L'email n'arrive pas

**Cause** : Email envoyé mais non reçu.

**Solutions** :
1. Vérifier les spams
2. Attendre 5 minutes
3. Vérifier que `contact@3s-managers.fr` existe bien chez IONOS
4. Tester avec un autre email : modifier `MAIL_ADMIN_ADDRESS` dans `.env.local`

## ✅ Checklist finale

- [ ] Serveur Symfony lancé
- [ ] Page `/contact` accessible
- [ ] Formulaire se remplit sans erreur
- [ ] Après soumission : message success/warning/error affiché
- [ ] Message sauvegardé en BDD
- [ ] Email reçu dans la boîte `contact@3s-managers.fr`
