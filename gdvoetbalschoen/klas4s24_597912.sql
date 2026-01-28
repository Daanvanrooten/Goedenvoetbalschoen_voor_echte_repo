-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 23 jan 2026 om 11:44
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `klas4s24_597912`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `email_verifications`
--

CREATE TABLE `email_verifications` (
  `token_id` int(20) UNSIGNED NOT NULL,
  `user_id` int(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `roles`
--

CREATE TABLE `roles` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `roles`
--

INSERT INTO `roles` (`role_id`, `name`) VALUES
(2, 'Admin'),
(1, 'User');

-- --------------------------------------------------------

--
-- Stand-in structuur voor view `slot_capacity_overview`
-- (Zie onder voor de actuele view)
--
CREATE TABLE `slot_capacity_overview` (
`slot_id` int(20) unsigned
,`task_id` int(20) unsigned
,`slot_date` date
,`start_time` time
,`end_time` time
,`capacity` int(10) unsigned
,`registrations` bigint(21)
,`spots_left` bigint(21) unsigned
);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `frequency` enum('DAILY','WEEKLY','MONTHLY') DEFAULT NULL,
  `interval_value` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `day` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Dag van de maand (1-31)',
  `week` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Weeknummer (1-53)',
  `month` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Maand (1-12)',
  `year` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Jaar (bv. 2026)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `category_id`, `is_active`, `created_at`, `updated_at`, `frequency`, `interval_value`, `start_time`, `end_time`, `day`, `week`, `month`, `year`) VALUES
(11, '123132321', 'w12312123', 1, 1, '2026-01-23 11:07:59', NULL, NULL, 1, '14:07:00', '17:07:00', 24, 4, 1, 2026),
(12, 'e1eewqe', '123123', 1, 1, '2026-01-23 11:13:14', NULL, 'MONTHLY', 1, '13:12:00', '17:12:00', 24, 4, 1, 2026),
(13, 'ewqeweqwe', '1233213', 1, 1, '2026-01-23 11:14:41', NULL, NULL, 1, '12:14:00', '16:14:00', 10, 2, 1, 2026);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `task_categories`
--

CREATE TABLE `task_categories` (
  `category_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_hex` char(7) NOT NULL COMMENT '#RRGGBB'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `task_categories`
--

INSERT INTO `task_categories` (`category_id`, `name`, `color_hex`) VALUES
(1, 'test 1234', '#cccccc');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `task_registrations`
--

CREATE TABLE `task_registrations` (
  `registration_id` int(20) UNSIGNED NOT NULL,
  `slot_id` int(20) UNSIGNED NOT NULL,
  `user_id` int(20) UNSIGNED NOT NULL,
  `registered_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `task_slots`
--

CREATE TABLE `task_slots` (
  `slot_id` int(20) UNSIGNED NOT NULL,
  `task_id` int(20) UNSIGNED NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `location` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `task_slots`
--

INSERT INTO `task_slots` (`slot_id`, `task_id`, `slot_date`, `start_time`, `end_time`, `capacity`, `location`, `created_at`, `updated_at`) VALUES
(6, 11, '2026-01-24', '14:07:00', '17:07:00', 5, NULL, '2026-01-23 11:07:59', NULL),
(7, 12, '2026-01-24', '13:12:00', '17:12:00', 5, NULL, '2026-01-23 11:13:14', NULL),
(8, 13, '2026-01-10', '12:14:00', '16:14:00', 5, NULL, '2026-01-23 11:14:41', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(20) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefoonnummer` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `first_name`, `last_name`, `email`, `telefoonnummer`, `username`, `password_hash`, `is_active`, `is_email_verified`, `created_at`, `updated_at`) VALUES
(1, 1, 'Daan', 'van Rooten', 'djaanvanrooten@gmail.com', '', 'admin', '$2y$10$vCl3g3wQh01LlFASuZ3o7.FpmqjL5IvcfqZh.n4rLxASYXxlawjZS', 1, 0, '2026-01-22 11:11:27', NULL),
(2, 2, 'Daan', 'van Rooten', 'daanvanrooten@gmail.com', '', 'Daan', '$2y$10$4M7./LSCZfowvCA9jU1/GewpbW7bX/UjGdGLD63xZvLX5cF.rAQAe', 1, 0, '2026-01-22 11:13:23', NULL),
(5, 1, 'Daan', 'van Rooten', 'daa12321312nvanrooten@gmail.com', '0648228072', 'Daan231', '$2y$10$eAF2K9FdgaLArrm0f1meMesntL6s6W4YPJ3ceBtm2V0FS4XsTxnt6', 1, 0, '2026-01-23 10:20:48', NULL);

-- --------------------------------------------------------

--
-- Structuur voor de view `slot_capacity_overview`
--
DROP TABLE IF EXISTS `slot_capacity_overview`;

CREATE VIEW `slot_capacity_overview` AS 
SELECT 
    `ts`.`slot_id`, 
    `ts`.`task_id`, 
    `ts`.`slot_date`, 
    `ts`.`start_time`, 
    `ts`.`end_time`, 
    `ts`.`capacity`, 
    COUNT(`tr`.`registration_id`) AS `registrations`, 
    `ts`.`capacity` - COUNT(`tr`.`registration_id`) AS `spots_left` 
FROM `task_slots` `ts` 
LEFT JOIN `task_registrations` `tr` ON `tr`.`slot_id` = `ts`.`slot_id` 
GROUP BY `ts`.`slot_id`, `ts`.`task_id`, `ts`.`slot_date`, `ts`.`start_time`, `ts`.`end_time`, `ts`.`capacity`;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `fk_email_verifications_user` (`user_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uniq_users_email` (`email`),
  ADD UNIQUE KEY `uniq_users_username` (`username`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT voor een tabel `task_categories`
--
ALTER TABLE `task_categories`
  MODIFY `category_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT voor een tabel `task_registrations`
--
ALTER TABLE `task_registrations`
  MODIFY `registration_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `task_slots`
--
ALTER TABLE `task_slots`
  MODIFY `slot_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `token_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
