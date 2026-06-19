# Symfony Starter — Modèle vide

Squelette Symfony 6.4 prêt à l'emploi. Toute la structure est en place, à toi de remplir le contenu.

## Stack

- Symfony 6.4 (LTS) — PHP 8.1+
- Doctrine ORM + Migrations
- Twig + Bootstrap 5
- Symfony Security
- Symfony Mailer
- Symfony Messenger
- PHPUnit
- MakerBundle (`make:entity`, `make:controller`…)

## Structure src/

```
src/
├── Controller/
│   ├── Admin/          ← Controllers admin (réservé ROLE_ADMIN)
│   ├── HomeController.php
│   └── SecurityController.php
├── DataFixtures/
│   └── AppFixtures.php ← Données de test
├── Entity/
│   └── User.php        ← Entité utilisateur (à compléter)
├── EventSubscriber/    ← Vos event listeners
├── Form/
│   └── RegistrationFormType.php
├── Repository/
│   └── UserRepository.php
├── Security/
│   └── Voter/          ← Vos Voters d'autorisation
├── Service/            ← Vos services métier
└── Twig/               ← Extensions Twig custom
```

## Démarrage rapide

```bash
# 1. Configurer la base de données dans .env
#    Modifier la ligne DATABASE_URL selon votre setup

# 2. Créer la base de données
php bin/console doctrine:database:create

# 3. Générer et exécuter la migration initiale
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

# 4. Charger les fixtures (admin@exemple.fr / password)
php bin/console doctrine:fixtures:load

# 5. Lancer le serveur
symfony server:start
# ou
php -S localhost:8000 -t public/
```

## Comptes par défaut (après fixtures)

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@exemple.fr | password | ROLE_ADMIN |
| user@exemple.fr  | password | ROLE_USER  |

## Commandes MakerBundle

```bash
php bin/console make:entity        # Créer une entité
php bin/console make:controller    # Créer un controller
php bin/console make:form          # Créer un formulaire
php bin/console make:crud          # Générer le CRUD complet
php bin/console make:voter         # Créer un Voter
php bin/console make:subscriber    # Créer un EventSubscriber
php bin/console debug:router       # Voir toutes les routes
php bin/console debug:container    # Voir les services
```
