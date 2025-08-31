-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 30, 2025 at 01:52 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ezyro_38868148_library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `available` tinyint(1) DEFAULT '1',
  `cover_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `available`, `cover_photo`) VALUES
(1, 'Harry Potter', 'J.K. Rowling', 1, 'uploads/1745948693_education-day-scene-fantasy-style-aesthetic_23-2151040233.png'),
(2, 'To Kill a Mockingbird', 'Harper Lee', 1, 'https://img.freepik.com/premium-photo/silhouette-bird-perching-metal-against-orange-clear-sky_1048944-6163598.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(3, '1984', 'George Orwell', 1, 'https://img.freepik.com/premium-vector/awesome-1984-t-shirt-design_613956-3808.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(4, 'The Great Gatsby', 'F. Scott Fitzgerald', 1, 'https://img.freepik.com/free-photo/party-decor-golden-decoration-black-background-with-ballons_8353-7983.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(5, 'Moby-Dick', 'Herman Melville', 1, 'https://img.freepik.com/free-photo/realistic-manta-ray-sea-water_23-2151461153.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(6, 'War and Peace', 'Leo Tolstoy', 1, 'https://img.freepik.com/free-photo/apocalyptic-war-zone-landscape-with-destruction_23-2150985611.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(7, 'The Catcher in the Rye', 'J.D. Salinger', 1, 'https://img.freepik.com/premium-photo/fantasy-illustration-scarecrow-standing-wheat-field_856795-40667.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(8, 'The Hobbit', 'J.R.R. Tolkien', 1, 'https://img.freepik.com/free-photo/stunning-fantasy-videogame-landscape_23-2150927826.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(9, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 1, 'https://img.freepik.com/free-photo/open-book-concept-fiction-storytelling_23-2150793621.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(10, 'The Diary of a Young Girl', 'Anne Frank', 1, 'https://img.freepik.com/premium-photo/artist-paints-picture-outdoors-early-sunny-morning_81340-1014.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(11, 'Jane Eyre', 'Charlotte Brontë', 1, 'https://img.freepik.com/premium-photo/black-bird-sits-stack-books-with-words-never-stop-learning-it_862863-10.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(12, 'Brave New World', 'Aldous Huxley', 1, 'https://img.freepik.com/premium-photo/book-with-mountain-landscape-forest-top_1325692-12631.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740'),
(13, 'The Alchemist', 'Paulo Coelho', 1, 'https://img.freepik.com/free-photo/open-book-with-fairytale-scene_52683-107844.jpg?uid=R183750515&ga=GA1.1.375319987.1741079138&semt=ais_hybrid&w=740');

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

DROP TABLE IF EXISTS `borrowed_books`;
CREATE TABLE IF NOT EXISTS `borrowed_books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `return_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`id`, `user_id`, `book_id`, `borrow_date`, `return_date`) VALUES
(1, 2, 1, '2025-04-30 16:22:28', '2025-04-30'),
(2, 2, 1, '2025-04-30 16:23:04', '2025-04-30'),
(3, 2, 1, '2025-04-30 16:23:13', '2025-04-30'),
(4, 2, 1, '2025-04-30 16:24:59', '2025-04-30'),
(5, 2, 2, '2025-04-30 16:25:04', '2025-04-30'),
(6, 2, 1, '2025-05-01 08:23:28', '2025-05-01'),
(7, 2, 1, '2025-08-06 09:11:25', '2025-08-06'),
(8, 2, 2, '2025-08-06 09:11:33', '2025-08-06'),
(9, 4, 1, '2025-08-09 11:22:25', '2025-08-09'),
(10, 4, 1, '2025-08-09 11:25:56', '2025-08-09'),
(11, 4, 1, '2025-08-09 11:29:54', '2025-08-09'),
(12, 4, 2, '2025-08-09 11:29:55', '2025-08-09'),
(13, 4, 3, '2025-08-09 11:29:56', '2025-08-09');

-- --------------------------------------------------------

--
-- Table structure for table `email_resets`
--

DROP TABLE IF EXISTS `email_resets`;
CREATE TABLE IF NOT EXISTS `email_resets` (
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_resets`
--

INSERT INTO `email_resets` (`user_id`, `token_hash`, `expires_at`, `created_at`) VALUES
(1, '$2y$10$EqMJuAWNBbxyBHuGOZ1T9.Ahkrjcoy4s8v1TvlL.ometa02xrgtLq', '2025-08-09 11:45:31', '2025-08-09 17:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_verifications`
--

INSERT INTO `email_verifications` (`id`, `user_id`, `token_hash`, `expires_at`, `used`, `created_at`) VALUES
(1, 4, '$2y$10$YBPQm2YYXR3qEP8hv9zvkOl7r89wuBQEPb7qLL7w.08Epp0RPjYGC', '2025-08-09 10:56:43', 1, '2025-08-09 16:11:43'),
(2, 5, '$2y$10$NHpeov9m.IC5246JLPWUP.zZG66U.lb0oy1CFNq4jPkI//ttqquey', '2025-08-09 11:32:23', 1, '2025-08-09 16:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `is_verified` tinyint(1) DEFAULT '0',
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_verified`, `verification_code`, `verification_expires`, `created_at`, `updated_at`) VALUES
(4, 'hathisa', 'hatheesha6504@gmail.com', '$2y$10$G.go82qTFsgdkHyuXmRHquwvQYpfspz5ecUDW44QE9QKMDlFcLKEW', 'admin', 1, NULL, NULL, '2025-08-09 10:41:43', '2025-08-30 01:49:38'),
(5, 'thushan', 'thushanmihiran09@gmail.com', '$2y$10$lH1w4MFCVsEm.tNsOsITSOOidlVnJaXoDrLqV7W1ugGrsaJ58CK3e', 'user', 1, NULL, NULL, '2025-08-09 11:17:23', '2025-08-09 11:18:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
