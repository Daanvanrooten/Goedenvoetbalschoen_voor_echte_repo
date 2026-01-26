-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 26 jan 2026 om 12:05
-- Serverversie: 10.6.14-MariaDB
-- PHP-versie: 8.2.8

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
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `day_of_week` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '0=Sunday,1=Monday,...,6=Saturday',
  `day_of_month` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1-31 monthly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `task_categories`
--

CREATE TABLE `task_categories` (
  `category_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_hex` char(7) NOT NULL COMMENT '#RRGGBB'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`klas4s24_597912`@`localhost` SQL SECURITY DEFINER VIEW `slot_capacity_overview`  AS SELECT `ts`.`slot_id` AS `slot_id`, `ts`.`task_id` AS `task_id`, `ts`.`slot_date` AS `slot_date`, `ts`.`start_time` AS `start_time`, `ts`.`end_time` AS `end_time`, `ts`.`capacity` AS `capacity`, count(`tr`.`registration_id`) AS `registrations`, `ts`.`capacity`- count(`tr`.`registration_id`) AS `spots_left` FROM (`task_slots` `ts` left join `task_registrations` `tr` on(`tr`.`slot_id` = `ts`.`slot_id`)) GROUP BY `ts`.`slot_id`, `ts`.`task_id`, `ts`.`slot_date`, `ts`.`start_time`, `ts`.`end_time`, `ts`.`capacity` ;

--
-- Indexen voor geëxporteerde tabellen
--

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
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
