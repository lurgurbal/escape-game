# Escape Game

<p align="center">
  <img src="YOUR_PROJECT_IMAGE" width="1120" alt="Escape Game banner" />
</p>

<p align="center">
  <a href="https://github.com/YOUR_USERNAME/escape-game">
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
  <strong>A science-fiction inspired escape room experience built with PHP, MySQL, and modern web interfaces.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/github/stars/YOUR_USERNAME/escape-game?style=social" alt="GitHub stars" />
  <img src="https://img.shields.io/github/forks/YOUR_USERNAME/escape-game?style=social" alt="GitHub forks" />
  <img src="https://img.shields.io/github/issues/YOUR_USERNAME/escape-game" alt="Open issues" />
  <img src="https://img.shields.io/github/license/YOUR_USERNAME/escape-game" alt="License" />
</p>

---

## Overview

Escape Game is a web-based puzzle adventure where players progress through a sequence of scientific riddles, unlock levels, and solve a narrative-driven challenge in a futuristic laboratory setting. The application combines gameplay mechanics, profile tracking, role-based access, and an admin dashboard in a single PHP/MySQL project.

The project was designed to provide:

- a complete escape room flow with progression and score tracking
- multiple game modes and difficulty-based logic
- a structured authentication and session system
- an admin dashboard for monitoring activity and game data
- a reusable architecture for adding new enigmas and gameplay rules

The primary technical goals are to create a clean, modular and maintainable application using server-side PHP logic, structured database access, secure sessions, and a user-friendly frontend.

---

## Features

<div align="center">
  <table>
    <tr>
      <td width="50%" valign="top">
        <h3>Player Experience</h3>
        <ul>
          <li>Login and registration flow</li>
          <li>Progressive game levels and puzzle logic</li>
          <li>Multiple game modes and difficulty settings</li>
          <li>User profile and statistics tracking</li>
          <li>Answer validation and feedback system</li>
        </ul>
      </td>
      <td width="50%" valign="top">
        <h3>Administration</h3>
        <ul>
          <li>Admin dashboard and role access checks</li>
          <li>Player activity and log monitoring</li>
          <li>Ability to manage roles and game states</li>
          <li>Audit-oriented logging support</li>
          <li>Structured project modularization</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td width="50%" valign="top">
        <h3>Backend Logic</h3>
        <ul>
          <li>PHP-based business logic and database abstraction</li>
          <li>Session-aware authentication lifecycle</li>
          <li>CSRF token generation and validation support</li>
          <li>Input sanitization and validation practices</li>
          <li>Reusable game function modules</li>
        </ul>
      </td>
      <td width="50%" valign="top">
        <h3>Scalability</h3>
        <ul>
          <li>Independent game modules and puzzle files</li>
          <li>Simple integration of new enigmas</li>
          <li>Expandable script and admin architecture</li>
          <li>Clear project directory organization</li>
          <li>Support for local and hosted deployment</li>
        </ul>
      </td>
    </tr>
  </table>
</div>

---

## Preview

<p align="center">
  <img src="YOUR_PROJECT_IMAGE" width="800" alt="Project preview" />
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

## Project Structure

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

### Key directories

- `admin/` contains administration interfaces and role management tools.
- `includes/` holds global configuration, database logic, authentication, and helper functions.
- `enigmes/` stores puzzle content and level-specific logic.
- `assets/` contains styling and frontend scripts.
- `logs/` manages logging and trace configuration.
- `escape_game.sql` is the schema and baseline project data file.

---

## Tech Stack

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript" />
  <img src="https://img.shields.io/badge/Apache-2.4-D22128?style=for-the-badge&logo=apache&logoColor=white" alt="Apache" />
</p>

### Core technologies

- PHP for server-side logic and session handling
- MySQL for persistence and game data
- HTML for page structure and game layout
- CSS for visual design and responsive styling
- JavaScript for light browser interactivity
- Apache / local web server environment for running the app

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

This architecture keeps the game logic centralized in PHP while preserving a clear separation between presentation, authentication, and persistence.

---

## Installation

### Prerequisites

- PHP 8.x
- MySQL 8.x or compatible MariaDB
- Apache or another local PHP server
- A modern browser
- Git

### Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/escape-game.git
cd escape-game
```

### Configure the database

1. Create a MySQL database.
2. Import the project schema:

```bash
mysql -u your_user -p your_database < escape_game.sql
```

3. Update the connection settings in the configuration files under `includes/`.

Typical configuration values include:

- database host
- database name
- database user
- database password
- application base path

### Local setup

If using XAMPP or a local Apache environment:

1. Place the project folder in your web root, such as `C:/xampp/htdocs/escape-game`.
2. Start Apache and MySQL.
3. Open the application in your browser:

```text
http://localhost/escapegame
```

### Launch

```bash
php -S localhost:8000
```

Then access:

```text
http://localhost:8000
```

---

## Usage

After launching the project:

1. Register a user account or log in.
2. Navigate through the available game modes.
3. Solve the puzzle chain to unlock the next level.
4. Proceed through each challenge until completion.
5. Check your profile and results after each session.

### Main application pages

- `welcome.php`: entry point and welcome flow
- `login.php`: authentication page
- `register.php`: account creation
- `modes.php`: available game modes
- `index.php`: main game progression
- `quiz.php` and `quiz_results.php`: quiz-driven gameplay and outcomes
- `profile.php`: user progress and gameplay summary
- `admin/dashboard.php`: admin controls and monitoring

---

## Development

The project uses a simple but structured server-side architecture. Developers can extend the game by adding new puzzle logic, updating admin tools, or introducing additional gameplay modes.

### Recommended workflow

1. Keep business logic in `includes/`
2. Use dedicated puzzle files in `enigmes/`
3. Keep UI styling in `assets/css/`
4. Prefer reusable functions instead of duplicating logic
5. Validate all user input before database interaction
6. Test authentication, session states, and navigation flows after every major change

### Important files

- `includes/auth.php`: authentication, login flow, session logic, and session hardening
- `includes/database.php`: database connection setup
- `includes/GameFunctions.php`: gameplay core logic
- `includes/config.php`: configuration values
- `escape_game.sql`: schema and initial database definitions
- `admin/dashboard.php`: administrative management interfaces

---

## Roadmap

- [x] User registration and login flow
- [x] Session-based access control
- [x] Game progression and level-based logic
- [x] Admin dashboard scaffolding
- [x] Role-related access checks
- [x] Puzzle structure and multiple challenge pages
- [ ] Improved responsive UI polish
- [ ] More advanced game analytics
- [ ] Expanded admin management tools
- [ ] Additional game modes and content packs
- [ ] Modular JSON or database-driven enigmas
- [ ] Better automated testing coverage
- [ ] Final deployment hardening and performance review

---

## Security

This project includes several security-oriented practices in its current implementation, but they should be reviewed and extended for production deployment.

### Included considerations

- password hashing with PHP `password_hash()`
- session initialization with secure cookie settings
- role-based access checks for admin features
- login attempt tracking logic
- input validation and sanitization in key flows
- session regeneration after login

### Important notes

- Do not expose sensitive credentials in public files or client-side scripts.
- Use strong database credentials in production.
- Keep server software, PHP, and MySQL updated.
- Review all database queries before deployment.
- For production use, add additional protections such as:
  - HTTPS enforcement
  - stricter CSRF validation on all state-changing actions
  - rate-limiting
  - environment-based configuration separation
  - application logging and alerting

> Security is layered and should be treated as an ongoing maintenance task rather than a one-time setup.

---

## Contributing

Contributions are welcome. To contribute:

1. Fork the repository
2. Create a feature branch:

```bash
git checkout -b feature/your-feature-name
```

3. Commit your changes:

```bash
git commit -m "Add your feature"
```

4. Push to your fork:

```bash
git push origin feature/your-feature-name
```

5. Open a Pull Request on GitHub

Before submitting changes, make sure the project still runs locally and that your updates do not break existing gameplay or authentication behavior.

---

## License

This project is licensed under the MIT License.

See the [LICENSE](LICENSE) file for full details.

---

## Author

<p align="center">
  <img src="https://img.shields.io/badge/Developer-YOUR_NAME-0D1117?style=for-the-badge&labelColor=0B1020&color=7DD3FC" alt="Developer name" />
</p>

- Name: YOUR_NAME
- Role: Full Stack Developer / Web Developer / Student Developer
- Education: YOUR_EDUCATION_OR_FIELD
- GitHub: https://github.com/YOUR_USERNAME

---

<p align="center">
  <strong>Escape Game</strong><br />
  A science-driven puzzle experience built for curious minds.
</p>

<p align="center">
  <a href="#top">Back to top</a>
</p>
