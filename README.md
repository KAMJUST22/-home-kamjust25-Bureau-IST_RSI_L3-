# Gestion Informatisée d'une Clinique (PHP + Oracle)

Application web simple en **PHP/HTML/CSS** connectée à Oracle pour permettre à un agent de clinique de:
- se connecter avec login/mot de passe,
- consulter la liste des médecins et services,
- consulter la liste des patients,
- voir les consultations et traitements des **24 dernières heures**.

## 1) Prérequis

- PHP 8.x avec extension `oci8` activée.
- Oracle Database accessible depuis la machine PHP.
- Schéma Oracle déjà créé (script fourni dans `sql/schema_clinique.sql`).

## 2) Configuration

Copiez et adaptez `config/config.php`:

```php
return [
    'oracle' => [
        'username' => 'clinique',
        'password' => 'clinique2026',
        'connection_string' => 'localhost/XEPDB1',
        'charset' => 'AL32UTF8',
    ],
    'app' => [
        'name' => 'Clinique LA PAIX',
        'session_name' => 'CLINIQUE_SESSION',
    ],
];
```

## 3) Lancer en local

Depuis la racine du projet:

```bash
php -S 0.0.0.0:8080 -t public
```

Puis ouvrez `http://localhost:8080`.

## 4) Comptes de connexion agent

Dans cette version pédagogique, l'authentification lit les utilisateurs dans la table `AGENT`.
Un utilisateur de démonstration est inclus dans `sql/schema_clinique.sql`:

- login: `agent1`
- mot de passe: `agent123`

Le mot de passe est stocké via `password_hash` compatible PHP.

## 5) Structure

- `public/` : pages web (index, dashboard, listes)
- `includes/` : connexion BD, authentification, layout
- `config/` : paramètres
- `sql/` : scripts Oracle (DDL/DML)
- `public/assets/css/style.css` : style global

## 6) Sécurité appliquée

- Sessions PHP avec nom de session personnalisé.
- Requêtes Oracle préparées (`oci_parse` + `oci_bind_by_name`).
- Échappement HTML via `htmlspecialchars`.
- Contrôle d'accès (pages protégées accessibles uniquement connecté).

