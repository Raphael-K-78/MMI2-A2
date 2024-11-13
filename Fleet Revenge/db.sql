-- Création de la table users
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(16) NOT NULL,
  `user_password` varchar(32) NOT NULL
);

-- Création de la table games
CREATE TABLE `games` (
  `id_game` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `game` varchar(16) NOT NULL,
  `Player1` int DEFAULT NULL,
  `Player2` int DEFAULT NULL,
`finish` TINYINT(1) NOT NULL DEFAULT '1',
  FOREIGN KEY (`Player1`) REFERENCES `users`(`id_user`),
  FOREIGN KEY (`Player2`) REFERENCES `users`(`id_user`)
);

-- Création de la table vaisseaux
CREATE TABLE `vaisseaux` (
  `id_vaisseau` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `position` json DEFAULT NULL,
  `pv` int DEFAULT '100',
  `proprete` int DEFAULT '100',
  `vitesse` decimal(3,1) NOT NULL,
  `puissance` decimal(3,1) NOT NULL,
  `solidite` decimal(3,1) NOT NULL,
  `classe` varchar(50) NOT NULL,
  `id_user` int NOT NULL,
  `id_game` int NOT NULL,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`),
  FOREIGN KEY (`id_game`) REFERENCES `games`(`id_game`)
);

-- Création de la table humains
CREATE TABLE `humains` (
  `id_humain` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `life` tinyint(1) DEFAULT '1',
  `xp` int DEFAULT '0',
  `classe` varchar(50) NOT NULL,
  `id_user` int NOT NULL,
  `id_game` int NOT NULL,
  `mana`INT NOT NULL DEFAULT `100`,
    `id_vaisseau` int NOT NULL,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`),
  FOREIGN KEY (`id_game`) REFERENCES `games`(`id_game`),
  FOREIGN KEY (`id_vaisseau`) REFERENCES `vaisseaux`(`id_vaisseau`),
);

-- Création de la table log
CREATE TABLE `log` (
  `id_log` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` int NOT NULL,
  `id_game` int NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_humain` INT NOT NULL,
  `action` varchar(255) NOT NULL,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`),
  FOREIGN KEY (`id_game`) REFERENCES `games`(`id_game`)
);
