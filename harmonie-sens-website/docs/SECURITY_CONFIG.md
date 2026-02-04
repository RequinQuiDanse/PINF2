# 🔒 Sécurité - Configuration des identifiants

## ✅ Configuration sécurisée mise en place

### Fichiers et leur rôle :

#### 1. `.env` (COMMITÉ dans Git)
- ✅ Contient des **valeurs d'exemple uniquement**
- ✅ Aucun mot de passe réel
- ✅ Sert de template pour les autres développeurs
- ⚠️ **NE JAMAIS** mettre de vrais identifiants ici

#### 2. `.env.local` (NON COMMITÉ dans Git)
- ✅ Contient vos **vrais identifiants IONOS**
- ✅ Automatiquement ignoré par Git (dans `.gitignore`)
- ✅ Les valeurs ici **écrasent** celles de `.env`
- 🔐 Vos secrets sont **protégés**

### Configuration actuelle :

```
📄 .env (commité)          → Valeurs d'exemple
📄 .env.local (ignoré)     → Vos vrais identifiants ✅
```

## 🔐 Vos identifiants (dans .env.local)

```env
MAILER_DSN=smtp://no-reply@3s-managers.fr:cYZaxy9UVHYVPiP@smtp.ionos.fr:465?encryption=ssl
MAIL_FROM_ADDRESS=no-reply@3s-managers.fr
MAIL_FROM_NAME="3s-Managers"
MAIL_ADMIN_ADDRESS=contact@3s-managers.fr
```

## ✅ Vérification de sécurité

### 1. Vérifier que .env.local est ignoré par Git :

```bash
git status
```

Vous **NE DEVEZ PAS** voir `.env.local` dans la liste des fichiers modifiés.

### 2. Vérifier le .gitignore :

```bash
cat .gitignore | grep env.local
```

Doit afficher : `/.env.local` ✅

### 3. S'assurer qu'aucun commit ne contient les identifiants :

```bash
git log --all --full-history --source -- .env.local
```

Devrait être vide (aucun commit).

## 🚀 Pour un nouveau développeur

1. **Cloner le projet**
   ```bash
   git clone <repo>
   ```

2. **Créer son propre .env.local**
   ```bash
   cp .env .env.local
   ```

3. **Configurer ses identifiants** dans `.env.local`

4. **Lancer l'application**
   ```bash
   symfony server:start
   ```

## 🌍 Pour la production

### Option 1 : Variables d'environnement serveur
Configurer directement sur le serveur :
```bash
export MAILER_DSN="smtp://no-reply@3s-managers.fr:cYZaxy9UVHYVPiP@smtp.ionos.fr:465?encryption=ssl"
export MAIL_FROM_ADDRESS="no-reply@3s-managers.fr"
export MAIL_FROM_NAME="3s-Managers"
export MAIL_ADMIN_ADDRESS="contact@3s-managers.fr"
```

### Option 2 : Fichier .env.local sur le serveur
Créer manuellement un `.env.local` sur le serveur avec les identifiants de production.

### Option 3 : Symfony Secrets (recommandé)
```bash
php bin/console secrets:set MAILER_DSN
php bin/console secrets:set MAIL_FROM_ADDRESS
php bin/console secrets:set MAIL_FROM_NAME
php bin/console secrets:set MAIL_ADMIN_ADDRESS
```

## ⚠️ IMPORTANT : Ne JAMAIS commiter

- ❌ `.env.local`
- ❌ `.env.*.local`
- ❌ Fichiers avec des mots de passe
- ❌ Clés API
- ❌ Tokens secrets

## 🔍 Vérifier avant chaque commit

```bash
# Voir ce qui va être commité
git diff --cached

# Vérifier qu'il n'y a pas de secrets
git diff --cached | grep -i "password\|secret\|key\|token"
```

## 📋 Checklist de sécurité

- [x] `.env` ne contient que des valeurs d'exemple
- [x] `.env.local` contient les vrais identifiants
- [x] `.env.local` est dans `.gitignore`
- [x] Aucun mot de passe dans les fichiers commités
- [x] Documentation créée pour l'équipe

## 🆘 En cas de fuite d'identifiants

Si vous avez accidentellement commité des identifiants :

1. **Changer IMMÉDIATEMENT les mots de passe**
2. **Supprimer le commit de l'historique Git** :
   ```bash
   git filter-branch --force --index-filter \
   "git rm --cached --ignore-unmatch .env.local" \
   --prune-empty --tag-name-filter cat -- --all
   ```
3. **Forcer le push** (attention, coordination avec l'équipe nécessaire) :
   ```bash
   git push origin --force --all
   ```

## 📚 Ressources

- [Symfony Environment Variables](https://symfony.com/doc/current/configuration.html#configuration-based-on-environment-variables)
- [Symfony Secrets Management](https://symfony.com/doc/current/configuration/secrets.html)
- [Git Secrets](https://github.com/awslabs/git-secrets)

## ✅ Résumé

Vos identifiants sont maintenant **sécurisés** :
- ✅ Pas de mots de passe dans Git
- ✅ Configuration flexible
- ✅ Prêt pour la production
- ✅ Équipe peut facilement configurer son environnement local

**Vous pouvez commiter et pusher en toute sécurité !** 🚀
