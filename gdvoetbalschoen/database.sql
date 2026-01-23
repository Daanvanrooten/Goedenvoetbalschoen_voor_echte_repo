-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 22 jan 2026 om 11:03
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
-- Database: `goudenvoetbalschoen_database`
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

-- --------------------------------------------------------

--
-- Structuur voor de view `slot_capacity_overview`
--
DROP TABLE IF EXISTS `slot_capacity_overview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `slot_capacity_overview`  AS SELECT `ts`.`slot_id` AS `slot_id`, `ts`.`task_id` AS `task_id`, `ts`.`slot_date` AS `slot_date`, `ts`.`start_time` AS `start_time`, `ts`.`end_time` AS `end_time`, `ts`.`capacity` AS `capacity`, count(`tr`.`registration_id`) AS `registrations`, `ts`.`capacity`- count(`tr`.`registration_id`) AS `spots_left` FROM (`task_slots` `ts` left join `task_registrations` `tr` on(`tr`.`slot_id` = `ts`.`slot_id`)) GROUP BY `ts`.`slot_id`, `ts`.`task_id`, `ts`.`slot_date`, `ts`.`start_time`, `ts`.`end_time`, `ts`.`capacity` ;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `uniq_email_verifications_token` (`token`),
  ADD KEY `fk_email_verifications_user` (`user_id`);

--
-- Indexen voor tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexen voor tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_tasks_category` (`category_id`);

--
-- Indexen voor tabel `task_categories`
--
ALTER TABLE `task_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `uniq_task_categories_name` (`name`);

--
-- Indexen voor tabel `task_registrations`
--
ALTER TABLE `task_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `uniq_task_registration` (`slot_id`,`user_id`),
  ADD KEY `idx_task_registrations_user` (`user_id`),
  ADD KEY `idx_task_registrations_slot` (`slot_id`);

--
-- Indexen voor tabel `task_slots`
--
ALTER TABLE `task_slots`
  ADD PRIMARY KEY (`slot_id`),
  ADD KEY `idx_task_slots_date` (`slot_date`),
  ADD KEY `idx_task_slots_task` (`task_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uniq_users_email` (`email`),
  ADD UNIQUE KEY `uniq_users_username` (`username`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `fk_email_verifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_category` FOREIGN KEY (`category_id`) REFERENCES `task_categories` (`category_id`) ON DELETE SET NULL;

--
-- Beperkingen voor tabel `task_registrations`
--
ALTER TABLE `task_registrations`
  ADD CONSTRAINT `fk_task_registrations_slot` FOREIGN KEY (`slot_id`) REFERENCES `task_slots` (`slot_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_registrations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `task_slots`
--
ALTER TABLE `task_slots`
  ADD CONSTRAINT `fk_task_slots_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
