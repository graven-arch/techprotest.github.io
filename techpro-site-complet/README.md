# 📘 Manuel d'installation et d'utilisation
# TechPro.tg — Site e-commerce + Panneau Admin AD
## TP Windows 10 + XAMPP + Active Directory

---

## 🗂️ Structure complète du projet

```
techpro-site/
│
├── 📄 install.php          ← ÉTAPE 1 : lancez ceci en premier !
├── 📄 config.php           ← Configuration (AD, DB) — déjà pré-rempli
├── 📄 index.php            ← Page d'accueil publique
├── 📄 produits.php         ← Catalogue produits (public)
├── 📄 produit.php          ← Fiche produit (public)
├── 📄 contact.php          ← Formulaire de contact (public)
├── 📄 a-propos.php         ← Page À propos (public)
│
├── 📁 includes/
│   ├── header.php          ← En-tête + navbar
│   ├── footer.php          ← Pied de page
│   ├── db.php              ← Connexion PDO MySQL
│   └── auth.php            ← Authentification LDAP/AD
│
└── 📁 admin/               ← Espace admin (protégé par AD)
    ├── login.php           ← Connexion admin via AD
    ├── layout.php          ← Gabarit admin (sidebar)
    ├── index.php           ← Tableau de bord admin
    ├── produits.php        ← Liste & gestion des produits
    ├── ajouter.php         ← Ajouter un produit
    ├── modifier.php        ← Modifier un produit
    ├── categories.php      ← Gestion des catégories
    ├── contacts.php        ← Messages du formulaire de contact
    └── logout.php          ← Déconnexion
```

---

## ⚙️ Installation en 4 étapes

### Étape 1 — Copier les fichiers dans XAMPP

Copiez le dossier **techpro-site** ici :
```
C:\xampp\htdocs\techpro-site\
```

---

### Étape 2 — Activer l'extension LDAP dans PHP

1. Ouvrez le fichier : `C:\xampp\php\php.ini`
2. Recherchez (Ctrl+F) la ligne :
   ```ini
   ;extension=ldap
   ```
3. **Retirez le point-virgule** pour obtenir :
   ```ini
   extension=ldap
   ```
4. Sauvegardez et **redémarrez Apache** depuis le panneau XAMPP.

> ⚠️ Sans cette étape, l'authentification AD ne fonctionnera pas.

---

### Étape 3 — Vérifier la configuration AD

Ouvrez le fichier `config.php`. Les paramètres suivants sont **déjà pré-remplis** pour votre TP :

```php
define('LDAP_HOST',   '10.0.10.3');   // ✅ IP de votre DC
define('LDAP_PORT',    389);
define('LDAP_DOMAIN', 'techpro.tg');
define('LDAP_BASEDN', 'DC=techpro,DC=tg');
```

> Si votre domaine n'est pas **techpro.tg**, modifiez `LDAP_DOMAIN` et `LDAP_BASEDN` en conséquence.

---

### Étape 4 — Lancer l'installateur (crée la base de données)

Ouvrez votre navigateur et allez à :
```
http://localhost/techpro-site/install.php
```

✅ Cela va automatiquement :
- Créer la base de données `techpro_db` dans MySQL
- Créer toutes les tables (produits, catégories, contacts...)
- Insérer **15 produits** de démonstration avec images
- Insérer **6 catégories** (Ordinateurs, Smartphones, Audio...)

Une fois installé, vous verrez un message de confirmation avec un lien vers le site.

> 🔒 **Supprimez `install.php`** après l'installation pour des raisons de sécurité.

---

## 🌐 Accès au site

| Page | URL |
|---|---|
| **Site public** | `http://localhost/techpro-site/` |
| **Admin (connexion AD)** | `http://localhost/techpro-site/admin/login.php` |

---

## 🔐 Comment fonctionne l'authentification AD

### Côté visiteur (public)
→ Aucune connexion requise. N'importe qui peut parcourir le site, voir les produits, envoyer un message via le formulaire de contact.

### Côté administrateur (vous)
1. Allez sur `http://localhost/techpro-site/admin/login.php`
2. Entrez votre **identifiant AD** (ex: `jean.dupont`) et votre **mot de passe AD**
3. Le serveur fait un `ldap_bind("jean.dupont@techpro.tg", motdepasse)` sur votre DC à `10.0.10.3`
4. Si succès → vos informations AD (nom, département, groupes) sont récupérées
5. Vous accédez au panneau d'administration

```
Client                    Serveur PHP (XAMPP)         DC Active Directory (10.0.10.3)
  |                              |                              |
  |-- POST username+password --> |                              |
  |                              |-- ldap_bind(user@domain) --> |
  |                              |<-- Succès / Échec ---------- |
  |                              |-- ldap_search (attributs) -> |
  |                              |<-- cn, mail, department ---- |
  |<-- Redirection dashboard --- |                              |
```

---

## 🛠️ Que peut faire l'administrateur ?

| Action | Où |
|---|---|
| Voir les statistiques (produits, messages) | Dashboard |
| Ajouter un produit (nom, prix, image, catégorie) | Admin → Ajouter |
| Modifier / supprimer un produit | Admin → Produits |
| Mettre un produit en vedette (page d'accueil) | Admin → Produits → ⭐ |
| Gérer les catégories | Admin → Catégories |
| Lire les messages de contact | Admin → Messages |
| Répondre par email | Admin → Messages → 📧 |

---

## 🖼️ Ajouter des images aux produits

Le site utilise des images **Unsplash** (libres de droit). Pour ajouter une image :

1. Allez sur [https://unsplash.com](https://unsplash.com)
2. Recherchez un produit (ex: "laptop", "smartphone", "headphones")
3. Cliquez sur une photo → Clic droit → **Copier l'adresse de l'image**
4. Ou utilisez directement ce format : `https://images.unsplash.com/photo-IDENTIFIANT?w=800&q=80`

---

## 🚧 Dépannage — Problèmes fréquents

### ❌ "Extension PHP LDAP non activée"
**Cause :** Le point-virgule devant `extension=ldap` n'a pas été retiré.
**Solution :** Vérifier `php.ini`, sauvegarder, redémarrer Apache.

### ❌ "Impossible de joindre le serveur LDAP"
**Cause :** Le DC n'est pas accessible depuis XAMPP.
**Solution :** Vérifier que le DC est démarré, tester avec `ping 10.0.10.3` depuis CMD.

### ❌ "Identifiants AD incorrects"
**Cause :** Mauvais identifiant ou mot de passe.
**Solution :** Vérifier que l'utilisateur existe dans l'AD, qu'il peut se connecter à une machine du domaine.

### ❌ Page blanche
**Cause :** Erreur PHP non affichée.
**Solution :** Dans `php.ini`, mettre `display_errors = On` et `error_reporting = E_ALL`, redémarrer Apache.

### ❌ "Erreur MySQL : SQLSTATE..."
**Cause :** MySQL n'est pas démarré dans XAMPP.
**Solution :** Ouvrir le panneau XAMPP et cliquer sur **Start** à côté de MySQL.

### ❌ install.php affiche une erreur de connexion MySQL
**Cause :** XAMPP a un mot de passe MySQL non vide.
**Solution :** Dans `config.php`, renseignez `DB_PASS` avec votre mot de passe MySQL.

---

## ✅ Checklist avant la démonstration TP

- [ ] XAMPP : Apache **démarré** ✓
- [ ] XAMPP : MySQL **démarré** ✓
- [ ] `extension=ldap` activée dans `php.ini` ✓
- [ ] `install.php` exécuté (base de données créée) ✓
- [ ] DC joignable : `ping 10.0.10.3` répond ✓
- [ ] Test connexion admin avec vos identifiants AD ✓
- [ ] Site accessible depuis un autre PC du réseau ✓

---

## 🌍 Rendre le site accessible depuis l'extérieur (clients)

Sur le PC XAMPP (Windows 10), vérifiez le **pare-feu Windows** :
1. Panneau de configuration → Pare-feu Windows Defender → Paramètres avancés
2. **Règle entrante** : autoriser le port **80 (TCP)**
3. Les clients du réseau accèdent via : `http://[IP-du-PC-XAMPP]/techpro-site/`

> Pour connaître l'IP de votre PC XAMPP : `ipconfig` dans CMD → IPv4

---

*TechPro.tg — Site réalisé pour TP Windows Server / Active Directory*
