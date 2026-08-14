# Escape Game

<p align="center">
  <img src="image.png" width="1120" alt="Bannière Escape Game" />
</p>

<p align="center">
  <a href="https://github.com/lurgurbal/escape-game">
    <img src="https://img.shields.io/badge/Repository-GitHub-0D1117?style=for-the-badge&logo=github&labelColor=0B1020&color=4CC9F0" alt="GitHub Repository" />
  </a>
  <a href="YOUR_DEMO_LINK">
    <img src="https://img.shields.io/badge/Demo-Live%20Preview-0D1117?style=for-the-badge&logo=rocket&labelColor=0B1020&color=3B82F6" alt="Live Demo" />
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.x" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 8.0" />
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white" alt="CSS3" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black" alt="JavaScript" />
  <img src="https://img.shields.io/badge/Apache-HTTP%20Server-D22128?style=flat-square&logo=apache&logoColor=white" alt="Apache" />
</p>

<p align="center">
  <strong>Une expérience de jeu d’évasion inspirée de la science-fiction, conçue avec PHP, MySQL et des interfaces web modernes.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/github/stars/lurgurbal/escape-game?style=social" alt="GitHub stars" />
  <img src="https://img.shields.io/github/forks/lurgurbal/escape-game?style=social" alt="GitHub forks" />
  <img src="https://img.shields.io/github/issues/lurgurbal/escape-game" alt="Open issues" />
  <img src="https://img.shields.io/github/license/lurgurbal/escape-game" alt="License" />
</p>

---

## Aperçu

Escape Game est une aventure web de type escape room où les joueurs progressent à travers une série d’énigmes scientifiques, débloquent des niveaux et résolvent un défi narratif dans un laboratoire futuriste. L’application combine le gameplay, le suivi de profil, les droits d’accès selon les rôles et un tableau de bord administrateur dans un seul projet PHP/MySQL.

Le projet a été conçu pour offrir :

- un parcours complet d’évasion avec progression et suivi des scores
- plusieurs modes de jeu et une logique de difficulté
- un système d’authentification et de sessions structuré
- un tableau de bord administrateur pour surveiller les activités et les données du jeu
- une architecture réutilisable pour ajouter de nouvelles énigmes et règles de jeu

Les objectifs techniques principaux sont de créer une application propre, modulaire et maintenable en utilisant une logique PHP côté serveur, une base de données structurée, des sessions sécurisées et une interface frontend conviviale.

---

## Fonctionnalités

<div align="center">
  <table>
    <tr>
      <td width="50%" valign="top">
        <h3>Expérience joueur</h3>
        <ul>
          <li>Flux de connexion et d’inscription</li>
          <li>Niveaux progressifs et logique d’énigmes</li>
          <li>Plusieurs modes de jeu et niveaux de difficulté</li>
          <li>Suivi du profil utilisateur et des statistiques</li>
          <li>Système de validation des réponses et de retour utilisateur</li>
        </ul>
      </td>
      <td width="50%" valign="top">
        <h3>Administration</h3>
        <ul>
          <li>Tableau de bord admin et contrôles d’accès</li>
          <li>Suivi de l’activité des joueurs et des logs</li>
          <li>Gestion des rôles et des états du jeu</li>
          <li>Support de journalisation orienté audit</li>
          <li>Modularisation structurée du projet</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td width="50%" valign="top">
        <h3>Logique backend</h3>
        <ul>
          <li>Logique métier PHP et abstraction base de données</li>
          <li>Cycle de vie des sessions avec gestion de l’authentification</li>
          <li>Support de génération et validation des jetons CSRF</li>
          <li>Pratiques de nettoyage et validation des entrées</li>
          <li>Modules de fonctions réutilisables pour le jeu</li>
        </ul>
      </td>
      <td width="50%" valign="top">
        <h3>Évolutivité</h3>
        <ul>
          <li>Modules de jeu indépendants et fichiers d’énigmes séparés</li>
          <li>Ajout simple de nouvelles énigmes</li>
          <li>Architecture de scripts et d’admin extensible</li>
          <li>Organisation claire du dépôt</li>
          <li>Compatibilité avec le déploiement local ou distant</li>
        </ul>
      </td>
    </tr>
  </table>
</div>

---

## Aperçu visuel

<p align="center">
  <img src="YOUR_PROJECT_IMAGE" width="800" alt="Aperçu du projet" />
</p>

<p align="center">
  <a href="YOUR_DEMO_LINK">
    <img src="https://img.shields.io/badge/View%20Demo-YOUR_DEMO_LINK-0D1117?style=for-the-badge&color=5EEAD4&labelColor=0B1020" alt="Live Demo Button" />
  </a>
</p>

<p align="center">
  <img src="YOUR_DEMO_GIF" width="900" alt="Gameplay animation or demo gif" />
</p>

---

## Structure du projet

```text
escape-game/
├── admin/
│   ├── dashboard.php
│   ├── delete_role.php
│   ├── log_dashboard.php
│   └── update_role.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── styles.css
│   └── js/
│       └── script.js
├── enigmes/
│   ├── enigme1.php
│   ├── enigme2.php
│   ├── enigme3.php
│   ├── enigme4.php
│   └── enigme5.php
├── includes/
│   ├── auth.php
│   ├── autoload.php
│   ├── config.php
│   ├── database.php
│   ├── db_check.php
│   ├── GameFunctions.php
│   ├── GameFunctionsNew.php
│   ├── TestFunctions.php
│   └── log_rotation.php
├── logs/
│   └── log_config.php
├── README.md
├── escape_game.sql
├── game_modes.php
├── index.php
├── login.php
├── logout.php
├── modes.php
├── profile.php
├── quiz_results.php
├── quiz.php
├── register.php
├── start_game.php
├── welcome.php
└── LICENSE
```

### Dossiers importants

- `admin/` contient les interfaces d’administration et les outils de gestion des rôles.
- `includes/` regroupe la configuration globale, la logique de base de données, l’authentification et les fonctions utilitaires.
- `enigmes/` contient le contenu des puzzles et la logique spécifique aux niveaux.
- `assets/` contient les styles et les scripts frontend.
- `logs/` gère la configuration et la journalisation.
- `escape_game.sql` est le schéma de base et le fichier de données de départ du projet.

---

## Stack technique

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript" />
  <img src="https://img.shields.io/badge/Apache-2.4-D22128?style=for-the-badge&logo=apache&logoColor=white" alt="Apache" />
</p>

### Technologies principales

- PHP pour la logique serveur et la gestion des sessions
- MySQL pour la persistance des données du jeu
- HTML pour la structure des pages et la mise en page
- CSS pour le design visuel et le rendu responsive
- JavaScript pour les petites interactions côté navigateur
- Apache / environnement local de serveur web pour exécuter l’application

---

## Architecture

```text
+-----------------------+
| Client / Browser      |
| - login               |
| - profile             |
| - quiz / modes        |
+----------+------------+
           |
           v
+-----------------------+
| Frontend Layer        |
| HTML + CSS + JS       |
| UI pages & interactions|
+----------+------------+
           |
           v
+-----------------------+
| PHP Application       |
| auth.php              |
| GameFunctions.php     |
| config.php            |
| session validation    |
+----------+------------+
           |
           v
+-----------------------+
| Server / Web Host     |
| Apache / PHP runtime  |
+----------+------------+
           |
           v
+-----------------------+
| Database Layer        |
| MySQL / escape_game.sql|
| users, levels, logs   |
+-----------------------+
```

Cette architecture centralise la logique du jeu dans PHP tout en gardant une séparation claire entre la présentation, l’authentification et la persistance des données.

---

## Installation

### Prérequis

- PHP 8.x
- MySQL 8.x ou MariaDB compatible
- Apache ou un serveur PHP local
- Un navigateur moderne
- Git

### Cloner le dépôt

```bash
git clone https://github.com/lurgurbal/escape-game.git
cd escape-game
```

### Configurer la base de données

1. Créez une base de données MySQL.
2. Importez le schéma du projet :

```bash
mysql -u your_user -p your_database < escape_game.sql
```

3. Mettez à jour les paramètres de connexion dans les fichiers de configuration dans `includes/`.

Les valeurs de configuration typiques incluent :

- l’hôte de la base de données
- le nom de la base de données
- l’utilisateur de la base de données
- le mot de passe
- le chemin de base de l’application

### Configuration locale

Si vous utilisez XAMPP ou un environnement Apache local :

1. Placez le dossier du projet dans votre dossier web, par exemple `C:/xampp/htdocs/escape-game`.
2. Démarrez Apache et MySQL.
3. Ouvrez l’application dans votre navigateur :

```text
http://localhost/escapegame
```

### Lancer le projet

```bash
php -S localhost:8000
```

Puis accédez à :

```text
http://localhost:8000
```

---

## Utilisation

Après le lancement du projet :

1. Créez un compte utilisateur ou connectez-vous.
2. Parcourez les modes de jeu disponibles.
3. Résolvez la suite d’énigmes pour débloquer le niveau suivant.
4. Poursuivez chaque défi jusqu’à la fin.
5. Consultez votre profil et vos résultats après chaque session.

### Pages principales de l’application

- `welcome.php` : page d’accueil et point d’entrée
- `login.php` : page de connexion
- `register.php` : création de compte
- `modes.php` : modes de jeu disponibles
- `index.php` : progression principale du jeu
- `quiz.php` et `quiz_results.php` : quiz et résultats du jeu
- `profile.php` : suivi du profil et de la progression
- `admin/dashboard.php` : contrôles et supervision administrateur

---

## Développement

Le projet repose sur une architecture côté serveur simple mais structurée. Les développeurs peuvent l’étendre en ajoutant de nouvelles logiques d’énigmes, en améliorant les outils d’administration ou en introduisant de nouveaux modes de jeu.

### Workflow recommandé

1. Gardez la logique métier dans `includes/`
2. Utilisez des fichiers dédiés aux énigmes dans `enigmes/`
3. Conservez le style visuel dans `assets/css/`
4. Privilégiez les fonctions réutilisables plutôt que la duplication
5. Validez toutes les entrées utilisateur avant les interactions avec la base de données
6. Testez l’authentification, les sessions et la navigation après chaque modification majeure

### Fichiers importants

- `includes/auth.php` : authentification, connexion, logique de session et sécurisation
- `includes/database.php` : configuration de la connexion à la base de données
- `includes/GameFunctions.php` : logique principale du gameplay
- `includes/config.php` : valeurs de configuration
- `escape_game.sql` : schéma et définitions initiales de la base de données
- `admin/dashboard.php` : interfaces de gestion administrative

---

## Feuille de route

- [x] Flux d’inscription et de connexion utilisateur
- [x] Contrôle d’accès basé sur les sessions
- [x] Progression du jeu et logique par niveaux
- [x] Structure du tableau de bord administrateur
- [x] Vérifications d’accès selon les rôles
- [x] Structure des énigmes et plusieurs pages de défis
- [ ] Amélioration du design responsive
- [ ] Analytique plus avancée du jeu
- [ ] Outils d’administration plus complets
- [ ] Modes de jeu et packs de contenu supplémentaires
- [ ] Énigmes modulaires basées sur JSON ou base de données
- [ ] Meilleure couverture de tests automatisés
- [ ] Renforcement final du déploiement et revue de performance

---

## Sécurité

Ce projet intègre plusieurs pratiques orientées sécurité dans son implémentation actuelle, mais elles doivent être vérifiées et renforcées avant tout déploiement en production.

### Éléments déjà présents

- hachage des mots de passe avec PHP `password_hash()`
- initialisation des sessions avec des cookies sécurisés
- contrôles d’accès selon les rôles pour les fonctionnalités admin
- logique de suivi des tentatives de connexion
- validation et nettoyage des entrées dans les flux clés
- régénération de session après connexion

### Points importants

- Ne pas exposer les identifiants sensibles dans des fichiers publics ou dans le code côté client.
- Utiliser des mots de passe robustes pour la base de données en production.
- Maintenir à jour le serveur, PHP et MySQL.
- Vérifier toutes les requêtes SQL avant le déploiement.
- En environnement de production, ajouter des protections supplémentaires comme :
  - la mise en place de HTTPS
  - une validation CSRF plus stricte sur toutes les actions modifiant l’état
  - la limitation des tentatives et du débit
  - une séparation de configuration selon l’environnement
  - la journalisation et l’alerte applicative

> La sécurité est multi-couches et doit être traitée comme une tâche continue, pas comme une étape unique.

---

## Contribuer

Les contributions sont les bienvenues. Pour participer :

1. Forkez le dépôt
2. Créez une branche de fonctionnalité :

```bash
git checkout -b feature/nom-de-votre-fonctionnalite
```

3. Validez vos changements :

```bash
git commit -m "Ajout de votre fonctionnalité"
```

4. Poushez vers votre fork :

```bash
git push origin feature/nom-de-votre-fonctionnalite
```

5. Ouvrez une Pull Request sur GitHub

Avant de soumettre des modifications, vérifiez que le projet fonctionne toujours localement et que vos changements ne cassent pas le gameplay ou l’authentification existante.

---

## Licence

Ce projet est sous licence MIT.

Consultez le fichier [LICENSE](LICENSE) pour plus de détails.

---

## Auteur

<p align="center">
  <img src="https://img.shields.io/badge/Developer-Lurgur%20Bal-0D1117?style=for-the-badge&labelColor=0B1020&color=7DD3FC" alt="Nom du développeur" />
</p>

- Nom : lurgurbal
- Rôle : Développeur Full Stack / Développeur Web
- Formation : Informatique / Développement Web
- GitHub : https://github.com/lurgurbal

---

<p align="center">
  <strong>Escape Game</strong><br />
  Une expérience de puzzle scientifique pensée pour les esprits curieux.
</p>

<p align="center">
  <a href="#top">Back to top</a>
</p>
