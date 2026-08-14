-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 28 avr. 2025 à 11:05
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `escape_game`
--

-- --------------------------------------------------------

--
-- Structure de la table `enigmes`
--

CREATE TABLE `enigmes` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `consigne` text NOT NULL,
  `reponse_attendue` varchar(255) NOT NULL,
  `indice` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enigmes`
--

INSERT INTO `enigmes` (`id`, `titre`, `consigne`, `reponse_attendue`, `indice`) VALUES
(1, 'Calcul énergétique', 'Le laboratoire utilise 5 ordinateurs fonctionnant à 300W chacun pendant 8 heures par jour. Le coût de l\'électricité est de 0.15€ par kWh. Quel est le coût quotidien (en €) ? Arrondir à 2 décimales.', '1.80', NULL),
(2, 'Mot de passe caché', 'Trouvez le mot de passe caché dans le code source de cette page web. Ce mot de passe permettra de débloquer le système de sécurité.', 'quantum2023', NULL),
(3, 'Physique - Chute libre', 'Un objet est lâché d\'une hauteur de 78.4 mètres. En négligeant la résistance de l\'air, calculez sa vitesse (en m/s) lorsqu\'il atteint le sol. Utilisez g = 9.8 m/s².', '39.2', NULL),
(4, 'Injection SQL', 'Corrigez la vulnérabilité SQL dans cette requête: SELECT * FROM users WHERE username = \'$input\' AND password = \'$pass\'. Utilisez des requêtes préparées.', 'SELECT * FROM users WHERE username = ? AND password = ?', NULL),
(5, 'Déchiffrement', 'Le mot de passe suivant a été haché avec MD5: 5f4dcc3b5aa765d61d8327deb882cf99. Quel est le mot de passe original ?', 'password', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `journal`
--

CREATE TABLE `journal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enigme_id` int(11) NOT NULL,
  `reponse_donnee` varchar(255) NOT NULL,
  `resultat` tinyint(1) NOT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `journal`
--

INSERT INTO `journal` (`id`, `user_id`, `enigme_id`, `reponse_donnee`, `resultat`, `date`) VALUES
(21, 4, 1, '1.8', 0, '2025-04-22 12:54:26'),
(22, 4, 1, '1.80', 1, '2025-04-22 12:54:43'),
(23, 4, 2, '1.80', 0, '2025-04-22 12:56:42'),
(24, 4, 2, 'quantum2023', 1, '2025-04-22 12:57:12'),
(25, 4, 3, '39.2', 1, '2025-04-22 13:02:10'),
(26, 4, 4, 'SELECT * FROM users WHERE username = &#039;$input&#039; AND password = &#039;$pass&#039;;', 0, '2025-04-22 13:03:37'),
(27, 4, 4, 'SELECT * FROM users WHERE username = ? AND password = ?', 1, '2025-04-22 13:04:04');

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL,
  `last_attempt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `mot_de_passe_hash` varchar(255) NOT NULL,
  `niveau_actuel` int(11) DEFAULT 1,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `mot_de_passe_hash`, `niveau_actuel`, `date_inscription`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, '2025-04-01 14:17:40'),
(3, 'matteo', '$2y$10$e.AUOYle7qdZM4HoKKMiDuamq0LJ/TMcz/FD47rv9NUceQQxpt.sO', 3, '2025-04-22 12:25:52'),
(4, 'Taha', '$2y$10$.GdknbTU1LHAUQOViCEZXew1bu0rXAndY6omfttGWNbdppuObSnmS', 5, '2025-04-22 12:52:31');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `enigmes`
--
ALTER TABLE `enigmes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `enigme_id` (`enigme_id`);

--
-- Index pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `journal`
--
ALTER TABLE `journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `journal_ibfk_2` FOREIGN KEY (`enigme_id`) REFERENCES `enigmes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
