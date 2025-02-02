-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 11:23 PM
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
-- Database: `playlisthub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'kavish', '81dc9bdb52d04dc20036dbd8313ed055'),
(2, 'kavisha', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlists`
--

INSERT INTO `playlists` (`playlist_id`, `user_id`, `title`, `description`) VALUES
(1, 1, 'vbf', ''),
(2, 1, 'dg', 'fhh'),
(6, 0, '', NULL),
(7, 0, '', NULL),
(8, 0, '', NULL),
(9, 0, '', NULL),
(12, 0, '', NULL),
(13, 0, '', NULL),
(16, 10, 'goble', NULL),
(19, 10, 'kavi', NULL),
(20, 10, 'kavi', NULL),
(23, 11, 'summer arc', NULL),
(24, 11, 'Hello', NULL),
(26, 11, 'MOMO', NULL),
(29, 11, 'josmon', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `playlist_song_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist_songs`
--

INSERT INTO `playlist_songs` (`playlist_song_id`, `playlist_id`, `song_id`) VALUES
(11, 1, 6),
(12, 1, 7),
(16, 1, 1),
(18, 16, 13),
(21, 19, 16),
(22, 16, 17),
(26, 24, 21),
(27, 24, 22),
(28, 24, 23),
(29, 29, 24);

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `song_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `album` varchar(100) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`song_id`, `title`, `artist`, `album`, `genre`, `duration`) VALUES
(1, 'Shape of You', 'Eid Sheeran', 'Divide', 'Pop', '00:03:53'),
(2, 'Blinding Lights', 'The Weeknd', 'After Hours', 'Synthwave', '00:03:20'),
(3, 'Bad Guy', 'Billie Eilish', 'When We All Fall Asleep, Where Do We Go?', 'Pop', '00:03:14'),
(4, 'Dance Monkey', 'Tones and I', 'The Kids Are Coming', 'Pop', '00:03:29'),
(5, 'Someone You Loved', 'Lewis Capaldi', 'Divinely Uninspired to a Hellish Extent', 'Pop', '00:03:02'),
(6, 'ranjhana', 'arjit', 'ee', 'Pop', '21:51:00'),
(7, 'wanna play', 'ss', 'as', 'Rock', '20:51:00'),
(9, 'Die with a smile', 'Bruno Mars', 'single', 'Pop', '13:57:00'),
(10, 'dead', 'Bruno Mars', 'single', NULL, '15:11:00'),
(13, 'sdfv', 'dcf', 'dcw', 'Hip-Hop', '18:43:00'),
(14, 'Die with a smile', 'Bruno Mars', 'silk route', 'Jazz', '04:03:00'),
(15, 'ranjhana', 'arjit', 'single', 'Classical', '03:18:00'),
(16, 'woops', 'taylor swift', 'rand', 'Rock', '12:57:00'),
(17, 'Bole jo koyal', 'Thala', 'CSK', 'Jazz', '19:06:00'),
(20, 'naksha', 'seedhe maut', 'dh9', 'Hip-Hop', '03:50:00'),
(21, 'tere naame', 'seedhe maut', 'dh9', 'Classical', '09:11:00'),
(22, 'saiyaan', 'kailash kher', 'dj', 'Classical', '05:09:00'),
(23, 'stuck on you', 'lionel Richie', 'single', 'Country', '03:12:00'),
(24, 'chutmaile chutesami', 'janvi', '', 'Jazz', '03:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(1, 'kavish', 'kavishtoraskar@gmail.com', '$2y$10$F/l4qklB/L79NBq1HMfl5ujuLWwqPcV6yVFQVGsldqv2DAoHZFeSC'),
(8, 'kavisha', 'kavi@gmail.com', '$2y$10$gp9Ne/HSjw0u9lrsAizq2.GCiWzv9R/ntvEYUdWKep5kED5ONrSL.'),
(10, 'kavishaaaa', 'abc@123', '$2y$10$.AGnn1UEW1MjGmHRZxphDeAhthiyhmHgo3iAwQujrD1JBjTiWJxru'),
(11, 'jass', 'madar@gmail.com', '$2y$10$7FwKcpuQnw6zUgpFksJIxOyEwAM8AJsvZfrV.tK32zVJHUJCW0C3q'),
(12, 'harsh', 'harsh@gmail.com', '$2y$10$aWU2kNOw90m.1gCGh3kHtu0EY1V2aiPkD5ImUZEYNhOIXUSV/hg.i');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `follower_id` (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`playlist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`playlist_song_id`),
  ADD KEY `playlist_id` (`playlist_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`song_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `playlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  MODIFY `playlist_song_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `song_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD CONSTRAINT `playlist_songs_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`playlist_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_songs_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`song_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
