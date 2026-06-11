-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 11:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `video_game_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `cover_image`, `release_date`) VALUES
(395, 'League of Evil', 'https://media.rawg.io/media/screenshots/66c/66c5525694fe80dce1ba2901214470d9.jpeg', '2011-02-03'),
(1140, 'World of Goo', 'https://media.rawg.io/media/games/d03/d030347839f74454afcd1008248b08ae.jpg', '2008-10-13'),
(1575, 'World of Tanks Blitz', 'https://media.rawg.io/media/screenshots/587/58785538b2d3a7226fa2c07d36e02b2b.jpeg', '2014-06-28'),
(2866, 'LEGO Marvel\'s Avengers', 'https://media.rawg.io/media/screenshots/d4e/d4e0720282f967886af9cffd2ebb5e1d.jpg', '2016-01-26'),
(2923, 'Hardware: Rivals', 'https://media.rawg.io/media/screenshots/cc2/cc23696a29992979fbc6d1b4f30d2db5.jpg', '2016-01-05'),
(4535, 'Call of Duty 4: Modern Warfare', 'https://media.rawg.io/media/games/9fb/9fbaea2168caea1f806546dfdaaeb1da.jpg', '2007-11-05'),
(12146, 'World to the West', 'https://media.rawg.io/media/screenshots/712/7124ba0575688acd9ab67fff771d60ee.jpg', '2017-05-04'),
(13799, 'Cabals: Card Blitz', 'https://media.rawg.io/media/screenshots/a13/a13754cb4941a51fb2a4ed0f0c80f320.jpg', '2017-02-08'),
(14331, 'Call of Duty 2', 'https://media.rawg.io/media/games/50c/50c69996b917ae50d8d319f6ce9bed37.jpg', '2005-10-25'),
(19369, 'Call of Duty', 'https://media.rawg.io/media/games/9c5/9c5bc0b6e67102bc96dcf1ba41509e42.jpg', '2003-10-29'),
(19572, 'Call of Juarez', 'https://media.rawg.io/media/games/a41/a4126f8dc70a3e664b18b983722ed082.jpg', '2006-09-06'),
(22508, 'Overwatch', 'https://media.rawg.io/media/games/4ea/4ea507ceebeabb43edbc09468f5aaac6.jpg', '2016-05-24'),
(23598, 'League of Legends', 'https://media.rawg.io/media/games/78b/78bc81e247fc7e77af700cbd632a9297.jpg', '2009-10-27'),
(23599, 'World of Warcraft', 'https://media.rawg.io/media/games/0d9/0d930ea604ee240c5af30c58f73ddf48.jpg', '2004-11-23'),
(28420, 'Blitz: The League', 'https://media.rawg.io/media/screenshots/ef3/ef36f649dfef1c5650262787810c925b.jpg', '2006-10-30'),
(39685, 'World of Warcraft: Cataclysm', 'https://media.rawg.io/media/games/4b9/4b9b27acc65760d166a9d65ee5bb6330.jpg', '2010-12-07'),
(43432, 'World of Warcraft: Legion', 'https://media.rawg.io/media/games/e61/e61f516a8a17eaa80ce484e5fd751e5b.jpg', '2016-08-30'),
(58175, 'God of War (2018)', 'https://media.rawg.io/media/games/4be/4be6a6ad0364751a96229c56bf69be59.jpg', '2018-04-20'),
(58859, 'Marvel’s Avengers', 'https://media.rawg.io/media/games/1eb/1ebef06e55f756974654c35b9aedb127.jpg', '2020-09-04'),
(388308, 'Overwatch 2', 'https://media.rawg.io/media/games/95a/95a10817d1fc648cff1153f3fa8ef6c5.jpg', '2022-10-04'),
(388309, 'Diablo IV', 'https://media.rawg.io/media/games/77d/77d51f8f4a07c3eecb0f8504027b1bf0.jpg', '2023-06-06'),
(388315, 'World of Warcraft: Shadowlands', 'https://media.rawg.io/media/games/1bf/1bff5a69755eaeef9d37b4e0a14e9bca.jpg', '2020-11-23'),
(401805, 'Genshin Impact', 'https://media.rawg.io/media/games/c38/c38bdb5da139005777176d33c463d70f.jpg', '2020-09-28'),
(418467, 'Call of Duty: Warzone', 'https://media.rawg.io/media/games/7e3/7e327a055bedb9b6d1be86593bef473d.jpg', '2020-03-10'),
(419292, 'I Wanna Be the Boshy', 'https://media.rawg.io/media/screenshots/f04/f047058e2fb58411713acd9f639ac71c.jpg', '2010-11-02'),
(476123, 'Marvel\'s Avengers BETA', 'https://media.rawg.io/media/screenshots/901/9015626add85a0b1c17dd61a0b147267.jpg', '2020-08-06'),
(482918, 'Marvel’s Avengers Beta', 'https://media.rawg.io/media/screenshots/23f/23fbdea10349d7cbba6a9c537ef16d58.jpg', '2020-08-21'),
(965470, 'Counter-Strike 2', 'https://media.rawg.io/media/games/ec4/ec4b02bdb3eb5c6212992c19bc05697e.jpg', '2023-09-27'),
(973384, 'God of War Ragnarok: Valhalla', 'https://media.rawg.io/media/screenshots/1d3/1d37ed8c21e856d71c06874c17916981.jpg', '2023-12-12'),
(993875, 'Marvel Rivals', 'https://media.rawg.io/media/screenshots/3f0/3f0fdfc7c71655366aa83ab80ecab9b8.jpg', '2024-12-06');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `role`, `password_hash`, `created_at`) VALUES
(2, 'Bbarac', 'bbarac@gmail.com', 'admin', '$2y$10$s4HaIR1cg4gRpxuKS21REOqjlV/PMK08A4m/rVJcNAEzUcKto0APi', '2026-06-08 12:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_games`
--

CREATE TABLE `user_games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `status` enum('playing','completed','dropped','plan_to_play') DEFAULT 'plan_to_play',
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_games`
--

INSERT INTO `user_games` (`id`, `user_id`, `game_id`, `status`, `rating`, `review`, `added_at`) VALUES
(6, 2, 4535, 'plan_to_play', 5, NULL, '2026-06-09 17:57:49'),
(8, 2, 14331, 'plan_to_play', 2, NULL, '2026-06-10 15:21:14'),
(10, 2, 419292, 'plan_to_play', 5, NULL, '2026-06-10 15:42:50'),
(11, 2, 58175, 'plan_to_play', 5, NULL, '2026-06-10 15:44:02'),
(12, 2, 973384, 'plan_to_play', 4, NULL, '2026-06-10 15:44:09'),
(16, 2, 401805, 'playing', 4, NULL, '2026-06-10 15:55:22'),
(28, 2, 1140, 'plan_to_play', 5, NULL, '2026-06-10 17:02:21'),
(30, 2, 39685, 'completed', 4, NULL, '2026-06-10 17:33:51'),
(31, 2, 19572, 'playing', 5, NULL, '2026-06-11 18:12:59'),
(39, 2, 993875, 'playing', 5, NULL, '2026-06-11 21:03:45'),
(44, 2, 2923, 'completed', 5, NULL, '2026-06-11 21:07:22'),
(45, 2, 22508, 'plan_to_play', NULL, NULL, '2026-06-11 21:18:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_games`
--
ALTER TABLE `user_games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_game_unique` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_games`
--
ALTER TABLE `user_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_games`
--
ALTER TABLE `user_games`
  ADD CONSTRAINT `user_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
