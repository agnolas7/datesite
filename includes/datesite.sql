-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 06:34 AM
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
-- Database: `datesite`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_compatibility_answers`
--

CREATE TABLE `admin_compatibility_answers` (
  `id` int(11) NOT NULL,
  `music_genres` varchar(500) DEFAULT NULL,
  `movie_genres` varchar(500) DEFAULT NULL,
  `weekend_activities` varchar(500) DEFAULT NULL,
  `humor_style` varchar(500) DEFAULT NULL,
  `energy_level` varchar(100) DEFAULT NULL,
  `planning_style` varchar(100) DEFAULT NULL,
  `food_preference` varchar(100) DEFAULT NULL,
  `coffee_preference` varchar(100) DEFAULT NULL,
  `crowd_preference` varchar(100) DEFAULT NULL,
  `conversation_style` varchar(100) DEFAULT NULL,
  `spontaneity_level` varchar(100) DEFAULT NULL,
  `sleep_type` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `owner_username` varchar(50) DEFAULT NULL,
  `phone_habits` varchar(100) DEFAULT NULL,
  `social_battery` varchar(100) DEFAULT NULL,
  `getting_to_know` varchar(100) DEFAULT NULL,
  `first_date_priority` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_compatibility_answers`
--

INSERT INTO `admin_compatibility_answers` (`id`, `music_genres`, `movie_genres`, `weekend_activities`, `humor_style`, `energy_level`, `planning_style`, `food_preference`, `coffee_preference`, `crowd_preference`, `conversation_style`, `spontaneity_level`, `sleep_type`, `updated_at`, `owner_username`, `phone_habits`, `social_battery`, `getting_to_know`, `first_date_priority`) VALUES
(1, 'OPM, Indie, R&B', 'Romance, Comedy, Crime / Mystery', 'Food trips, Night drives, Nature / outdoors, Random drives, Bar / chill night out', 'Stupid jokes, Sarcasm, Witty / clever, Memes only', 'Medium — depends on the day', 'I have a rough idea', 'Anything as long as it\'s good', 'Neither', 'Small group is fine', 'Funny and random', 'Very spontaneous', 'Somewhere in between', '2026-03-11 07:45:29', NULL, NULL, NULL, NULL, NULL),
(2, 'OPM, Indie, Classical, Jazz, Electronic / EDM, Hip-hop / Rap, R&B', 'Romance, Comedy, Documentary, Thriller, K-drama', 'Coffee shop hopping, Watching movies at home, Art galleries, Going to the mall, Concerts / events, Random drives', 'Physical comedy, Dark humor, Witty / clever, Self-deprecating, Dry humor', 'Low — chill lang talaga', 'I plan everything in advance', 'I like trying new stuff', '', 'Small group is fine', 'Deep / meaningful talks', 'Very spontaneous', 'I sleep at random hours', '2026-03-13 04:17:05', 'noriel7', 'Checked occasionally', '', 'Depends on the vibe', 'Good conversation');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'noriel', 'noriel');

-- --------------------------------------------------------

--
-- Table structure for table `maybe_reasons`
--

CREATE TABLE `maybe_reasons` (
  `id` int(11) NOT NULL,
  `owner_username` varchar(50) DEFAULT NULL,
  `reason` varchar(500) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maybe_reasons`
--

INSERT INTO `maybe_reasons` (`id`, `owner_username`, `reason`, `response_id`, `submitted_at`) VALUES
(1, 'noriel7', 'i just don\'t like you like that ????', NULL, '2026-03-13 13:58:26'),
(2, NULL, 'i don\'t know you well enough yet', NULL, '2026-03-13 23:01:53'),
(3, NULL, 'the timing isn\'t right for me', NULL, '2026-03-15 02:44:45'),
(4, 'noriel7', 'the timing isn\'t right for me', NULL, '2026-03-15 02:46:57'),
(5, 'noriel7', 'corny mo kasi', NULL, '2026-03-15 06:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `message_text` longtext NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `response_id`, `message_text`, `sent_at`) VALUES
(1, 20, 'sdasdasd', '2026-03-13 04:38:26'),
(2, 22, 'joke lang', '2026-03-13 13:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `responder_compatibility_answers`
--

CREATE TABLE `responder_compatibility_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `music_genres` varchar(500) DEFAULT NULL,
  `movie_genres` varchar(500) DEFAULT NULL,
  `weekend_activities` varchar(500) DEFAULT NULL,
  `humor_style` varchar(500) DEFAULT NULL,
  `energy_level` varchar(100) DEFAULT NULL,
  `planning_style` varchar(100) DEFAULT NULL,
  `food_preference` varchar(100) DEFAULT NULL,
  `coffee_preference` varchar(100) DEFAULT NULL,
  `crowd_preference` varchar(100) DEFAULT NULL,
  `conversation_style` varchar(100) DEFAULT NULL,
  `spontaneity_level` varchar(100) DEFAULT NULL,
  `sleep_type` varchar(100) DEFAULT NULL,
  `compatibility_score` decimal(5,2) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_username` varchar(50) DEFAULT NULL,
  `phone_habits` varchar(100) DEFAULT NULL,
  `social_battery` varchar(100) DEFAULT NULL,
  `getting_to_know` varchar(100) DEFAULT NULL,
  `first_date_priority` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responder_compatibility_answers`
--

INSERT INTO `responder_compatibility_answers` (`id`, `response_id`, `music_genres`, `movie_genres`, `weekend_activities`, `humor_style`, `energy_level`, `planning_style`, `food_preference`, `coffee_preference`, `crowd_preference`, `conversation_style`, `spontaneity_level`, `sleep_type`, `compatibility_score`, `submitted_at`, `owner_username`, `phone_habits`, `social_battery`, `getting_to_know`, `first_date_priority`) VALUES
(1, 4, 'Classical, Country', 'Fantasy', 'Coffee shop hopping', 'Dark humor', 'High — always doing something', 'I plan everything in advance', 'Anything as long as it\'s good', 'Coffee, always', 'Very quiet, few people', 'Getting to know each other slowly', 'Very spontaneous', 'Night owl — 12am+', 0.00, '2026-03-11 07:43:30', NULL, NULL, NULL, NULL, NULL),
(2, 5, 'OPM, Pop, R&B', 'Romance, Comedy, K-drama', 'Nature / outdoors, Bar / chill night out', 'Stupid jokes, Sarcasm, Wholesome, Witty / clever', 'Low — chill lang talaga', 'I plan everything in advance', 'I eat the same 5 things', 'Coffee, always', 'Very quiet, few people', 'Getting to know each other slowly', 'A little structure please', 'Night owl — 12am+', 14.50, '2026-03-11 07:49:11', NULL, NULL, NULL, NULL, NULL),
(3, 7, 'OPM', 'Sci-fi', 'Arcade / games', 'Memes only', 'Medium — depends on the day', 'What\'s a plan?', 'Anything as long as it\'s good', 'Milk tea, always', 'Small group is fine', 'Deep / meaningful talks', 'I need an itinerary', 'Night owl — 12am+', 0.00, '2026-03-11 08:24:34', NULL, NULL, NULL, NULL, NULL),
(4, 15, 'Indie, K-pop, Country, Alternative, OPM, Jazz, R&B, Hip-hop / Rap, Pop, Rock', 'Comedy, Fantasy, Crime / Mystery, Animation', 'Coffee shop hopping, Random drives, Concerts / events', 'Dark humor, Self-deprecating, Wholesome, Dry humor', 'High — always doing something', 'What\'s a plan?', 'Filipino food always', 'Milk tea, always', 'Small group is fine', 'Bahala na ang daloy', 'A little structure please', 'Somewhere in between', 27.00, '2026-03-13 03:14:45', NULL, 'Barely touched', 'Watching something together', 'Depends on the vibe', 'Good food'),
(5, 17, 'OPM, Pop, Jazz, Alternative, Classical, K-pop, Country, Indie', 'Comedy, Documentary, Animation, Crime / Mystery, Romance, Sci-fi, Fantasy, Horror, Thriller, Action, Anime, K-drama', 'Coffee shop hopping, Food trips, Night drives, Concerts / events, Going to the mall, Arcade / games, Watching movies at home, Nature / outdoors, Just staying in bed, Bar / chill night out', 'Dry humor, Wholesome, Memes only, Physical comedy', 'High — always doing something', 'I plan everything in advance', 'I like trying new stuff', 'Coffee, always', 'Very quiet, few people', 'Getting to know each other slowly', 'Very spontaneous', 'Night owl — 12am+', 50.00, '2026-03-13 03:54:40', NULL, 'Barely touched', 'Quiet place, just talking', 'Open up quickly', 'Good conversation'),
(6, 18, 'OPM, Pop, K-pop, Jazz, Country, Classical', 'Comedy, Fantasy, Animation, Documentary, Horror', 'Coffee shop hopping, Food trips, Just staying in bed, Arcade / games, Art galleries, Random drives', 'Dark humor, Self-deprecating, Witty / clever, Memes only', 'Low — chill lang talaga', 'I plan everything in advance', 'Filipino food always', 'Coffee, always', 'Very quiet, few people', 'Getting to know each other slowly', 'Very spontaneous', 'Morning person', 36.80, '2026-03-13 04:01:11', NULL, 'Barely touched', 'Watching something together', 'Open up quickly', 'Just good company'),
(7, 22, 'Pop, Indie, Jazz, Country, K-pop', 'Comedy, Romance, Sci-fi, Fantasy, Documentary, Animation', 'Coffee shop hopping, Watching movies at home, Arcade / games, Just staying in bed', 'Dry humor, Memes only', 'Low — chill lang talaga', 'I plan everything in advance', 'Filipino food always', 'Coffee, always', 'Very quiet, few people', 'Deep / meaningful talks', 'Very spontaneous', 'Night owl — 12am+', 43.60, '2026-03-13 13:23:15', NULL, 'Barely touched', 'Quiet place, just talking', 'Open up quickly', 'Good conversation'),
(8, 31, 'R&B, Indie, OPM, Pop', 'Romance, Comedy, Action, Crime / Mystery, Horror', 'Random drives, Bar / chill night out, Night drives', 'Stupid jokes, Witty / clever, Memes only, Sarcasm, Wholesome', 'Medium — depends on the day', 'I just wing it', 'Anything as long as it\'s good', 'Coffee, always', 'Small group is fine', 'Funny and random', 'Very spontaneous', 'Somewhere in between', 27.50, '2026-03-15 06:16:26', NULL, 'We\'ll probably both check it', 'Chill food trip', 'Take some time to warm up', 'Just good company');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `communication` varchar(255) DEFAULT NULL,
  `best_time` varchar(100) DEFAULT NULL,
  `food_drink` text DEFAULT NULL,
  `dealbreaker` varchar(255) DEFAULT NULL,
  `date_type` varchar(100) DEFAULT NULL,
  `spontaneity` varchar(100) DEFAULT NULL,
  `energy` varchar(100) DEFAULT NULL,
  `mood` varchar(100) DEFAULT NULL,
  `crowd` varchar(100) DEFAULT NULL,
  `convo_style` varchar(100) DEFAULT NULL,
  `vibes` text DEFAULT NULL,
  `custom_vibe` text DEFAULT NULL,
  `walking` varchar(100) DEFAULT NULL,
  `awkwardness` varchar(100) DEFAULT NULL,
  `convo_difficulty` varchar(100) DEFAULT NULL,
  `compatibility_score` int(11) DEFAULT NULL,
  `scheduled_date` varchar(100) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_username` varchar(50) DEFAULT NULL,
  `flower` varchar(100) DEFAULT NULL,
  `craving` varchar(255) DEFAULT NULL,
  `temperature` varchar(100) DEFAULT NULL,
  `dislikes` varchar(255) DEFAULT NULL,
  `dessert` varchar(100) DEFAULT NULL,
  `maybe_reason` varchar(500) DEFAULT NULL,
  `curfew` varchar(255) DEFAULT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `distance` varchar(255) DEFAULT NULL,
  `place_in_mind` varchar(100) DEFAULT NULL,
  `place_name` varchar(255) DEFAULT NULL,
  `place_timing` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `name`, `age`, `city`, `communication`, `best_time`, `food_drink`, `dealbreaker`, `date_type`, `spontaneity`, `energy`, `mood`, `crowd`, `convo_style`, `vibes`, `custom_vibe`, `walking`, `awkwardness`, `convo_difficulty`, `compatibility_score`, `scheduled_date`, `submitted_at`, `owner_username`, `flower`, `craving`, `temperature`, `dislikes`, `dessert`, `maybe_reason`, `curfew`, `parents`, `distance`, `place_in_mind`, `place_name`, `place_timing`) VALUES
(1, 'tanginamo', '20', 'Candaba, Region III', 'Instagram, Shopee message, I', 'Night vibes', 'ulol', 'None I', 'Random spontaneous hang out', 'Yes please', 'Medium', 'Playful', 'Some people', 'Random funny stuff', 'Street food crawl, Beer and smoke', 'qqeqwewq', 'Minimal', 'Very', 'Legendary boss fight', 69, '2026-03-12T16:08', '2026-03-11 06:07:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'ulol', '20', 'asds', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 06:58:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'ulol', '20', 'asds', 'Instagram, Shopee message, liham, In our dreams', 'Night vibes', 'matcha and fries', 'Pineapple on pizza', 'Cozy indoor date', 'A little structure', 'Active', 'Adventurous', 'Some people', 'Getting to know each other', 'Night drive, Street food crawl, Parking lot hangout', 'asdas', 'Some walking', 'A little', 'Medium difficulty', 79, NULL, '2026-03-11 06:58:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'gago', '20', 'sa  bahay', 'Instagram', 'Night vibes', 'ikaw', 'Bad music taste', 'Surprise me', '', '', '', '', '', '', '', '', '', '', 77, NULL, '2026-03-11 07:14:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'check compatibility', '20', 'sa campus', 'Instagram', 'Night vibes', 'fries and beer', 'Bad music taste', 'Random spontaneous hang out', 'Yes please', 'Chill', 'Playful', 'Some people', 'Random funny stuff', 'Night drive, Arcade / games, Parking lot hangout, Beer and smoke', 'sheesh', 'A lot', 'A little', 'Medium difficulty', 92, NULL, '2026-03-11 07:46:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'dsd', '19', 'sd', 'Messenger', 'Morning coffee', 's', 'Pineapple on pizza', 'Random spontaneous hang out', 'A little structure', 'Active', 'Adventurous', 'Busy', 'Random funny stuff', 'Beer and smoke', 's', 'Some walking', 'Smooth', 'Medium difficulty', 78, '2026-04-01 15:22', '2026-03-11 08:19:41', 'noriel7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'noriel7', '19', 'sd', 'Shopee message', 'Afternoon hangout', 'sd', 'None I', 'Outdoor adventure', 'Let\'s wing it', '', 'Adventurous', '', '', '', '', '', '', '', 70, '2026-03-26 02:03', '2026-03-11 08:23:25', 'noriel7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'juan', '19', 'sa campus', 'Instagram', 'Morning coffee', 's', 'Bad music taste', 'Cozy indoor date', 'A little structure', 'Active', 'Adventurous', 'Some people', 'Random funny stuff', 'Beer and smoke', '', 'Some walking', '', '', 97, '2026-03-10 16:31', '2026-03-11 08:27:02', 'juan123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'sdsd', '19', 'asd', 'Instagram, Shopee message', 'Morning coffee', 'sds', 'Bad music taste', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 11:44:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'sd', '20', 'sd', 'Instagram', 'Morning coffee', 'sd', 'Bad music taste', 'Outdoor adventure', 'Yes please', 'Chill', 'Slightly awkward but fun', 'Some people', 'Random funny stuff', 'Coffee shop', 'asdas', 'A lot', 'A little', 'Medium difficulty', 94, '2026-03-19 10:36', '2026-03-11 12:07:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, '836', '19', 'sa  bahay', 'Instagram', 'Morning coffee', 'sa', 'Bad music taste', 'Cozy indoor date', 'Yes please', 'Chill', 'Relaxed', 'Quiet', 'Deep talks', 'Coffee shop, Beer and smoke, Dinner', '', 'If we get lost we get lost', 'A little', 'Easy mode', 75, '2026-03-16 08:38', '2026-03-11 12:36:24', 'noriel7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'sd', '19', 'sd', 'Telegram', 'Morning coffee', 'sds', 'Bad music taste', 'Outdoor adventure', 'Yes please', 'Chill', 'Playful', 'Quiet', 'Deep talks', 'Watch a movie', 'sd', 'If we get lost we get lost', 'A little', 'Easy mode', 83, '2026-03-16 10:35', '2026-03-13 02:32:29', NULL, 'Sunflower ????', 'sda', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, '1037', '22', 'sa campus', 'Instagram', 'Morning coffee', 'sd', 'ds', 'Cozy indoor date', 'Chaos', 'Chill', 'Relaxed', 'Quiet', 'Deep talks', 'Coffee shop', 'sd', 'If we get lost we get lost', 'A little', 'Easy mode', 89, '2026-03-16 12:40', '2026-03-13 02:38:03', 'noriel7', 'Sunflower ????', '', 'I get cold easily ????', 'Loud places', 'Chocolate ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'baby', '20', 'sa campus', 'Instagram', 'Night vibes', 'matcha and fries', 'Pineapple on pizza', 'Random spontaneous hang out', 'A little structure', '', 'Playful', '', '', 'Coffee shop, Night drive, Arcade / games, Watch a movie, Stroll, Parking lot hangout, Beer and smoke', 'holdap', '', 'Smooth', 'Medium difficulty', 78, '2026-03-18 19:30', '2026-03-13 02:46:48', 'noriel7', 'Tulip ????', 'ilocos empanada', 'I get cold easily ????', 'Very crowded spots', 'Not really a dessert person', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'savesched', '19', 'sda', 'Instagram', 'Morning coffee', 'sdas', 'Bad music taste', 'Cozy indoor date', 'Yes please', 'Chill', 'Relaxed', 'Quiet', 'Getting to know each other', 'Watch a movie', 'sd', 'Minimal', 'Very', 'Easy mode', 69, '2026-03-17 13:01', '2026-03-13 03:00:51', 'noriel7', 'Sunflower ????', 'sd', 'I get cold easily ????', 'Walking too much', 'Chocolate ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'aasda', '19', 'asds', 'Instagram', 'Morning coffee', 'matcha and fries', 'Bad music taste', 'Cozy indoor date', 'Yes please', 'Chill', 'Relaxed', 'Quiet', 'Deep talks', 'Coffee shop', '', '', 'Very', 'Legendary boss fight', 77, '2026-03-26 23:35', '2026-03-13 03:27:11', 'noriel7', 'Sunflower ????', 'sda', 'I get cold easily ????', 'Loud places', 'Chocolate ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'chekc', '20', 'das', 'Messenger', 'Morning coffee', 'sada', 'Bad music taste', 'Cozy indoor date', 'A little structure', 'Medium', 'Playful', 'Some people', 'Random funny stuff', 'Coffee shop, Lunch', 'd', 'Some walking', 'A little', 'Legendary boss fight', 84, '2026-03-17 02:53', '2026-03-13 03:52:00', 'noriel7', 'Sunflower ????', 'sdas', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'comp', '22', 'dsd', 'Messenger, In our dreams', 'Morning coffee', 'sdasd', 'Bad music taste', 'Cozy indoor date', 'Yes please', 'Chill', '', 'Quiet', 'Deep talks', 'Coffee shop, Parking lot hangout', '', 'Minimal', 'A little', 'Easy mode', 91, '2026-03-25 12:02', '2026-03-13 03:59:32', 'noriel7', 'Sunflower ????', 'asd', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'editsched', '20', 'sdasdad', 'Instagram', 'Morning coffee', 'sd', 'Bad music taste', '', '', '', '', '', '', '', '', '', '', '', 97, '2026-12-23 00:28', '2026-03-13 04:27:33', NULL, 'Sunflower ????', 'das', 'I get cold easily ????', 'Walking too much', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'view deets', '20', 'sda', 'Telegram', 'Morning coffee', 'dasd', 'Bad music taste', '', '', '', '', '', '', '', '', '', '', '', 80, '2026-03-18 12:32', '2026-03-13 04:30:48', 'noriel7', 'Sunflower ????', 'sd', 'I get cold easily ????', 'Walking too much', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'downlaod', '19', 'df', 'Instagram', 'Morning coffee', 'sd', 'None I', 'Cozy indoor date', 'Yes please', 'Chill', 'Relaxed', 'Quiet', 'Deep talks', 'Coffee shop, Street food crawl', 'sds', 'Minimal', 'Very', 'Easy mode', 78, '2026-03-17 12:48', '2026-03-13 04:45:09', 'juan123', 'Sunflower ????', 'sda', 'I get cold easily ????', 'Walking too much', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'qmarks', '20', 'sa  bahay', 'Instagram', 'Morning coffee', 'ulol', 'Bad music taste', '', '', '', '', '', '', '', '', '', '', '', 84, '2026-03-25 11:25', '2026-03-13 13:18:52', 'noriel7', 'Lily ????', 'matcchsa', 'I get hot easily ????', 'Loud places, Very crowded spots, Long waiting lines', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'tesdsss', '20', 'cabanatuan', 'Instagram', 'Morning coffee', 'xsdsa', 'Bad music taste', 'Cozy indoor date', '', '', '', '', '', '', '', '', '', '', 92, NULL, '2026-03-13 23:54:51', 'noriel7', 'Sunflower ????', 'sd', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'check', '19', 'cabanatuan', 'Instagram, I', 'Morning coffee', 'sdsds', 'None I', '', '', '', '', '', '', '', '', '', '', '', 86, '2026-03-26 11:12', '2026-03-14 15:10:55', NULL, 'Sunflower ????', 'sd', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'salonga', '20', 's', 'Instagram', 'Morning coffee', 'sd', 'Bad music taste', '', '', '', '', '', '', '', '', '', '', '', 76, '2026-03-19 23:18', '2026-03-14 15:15:54', NULL, 'Sunflower ????', 'sd', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'd', '20', 's', 'Instagram', 'Morning coffee', 's', 'Bad music taste', '', '', '', '', '', '', '', '', '', '', '', 89, '2026-03-16 23:22', '2026-03-14 15:17:22', NULL, 'Sunflower ????', 's', 'I get cold easily ????', 'Loud places', 'Chocolate ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'download', '21', 'sd', 'Instagram', 'Morning coffee', 'sda', 'Bad music taste', '', '', '', '', '', '', 'Lunch', '', '', '', '', 98, NULL, '2026-03-14 15:27:39', 'noriel7', 'Sunflower ????', 'sd', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'downlaod1', '19', 'sd', 'Instagram', 'Morning coffee', 'sda', 'Bad music taste', '', '', '', '', '', '', 'Dinner', '', '', '', '', 73, NULL, '2026-03-14 15:29:00', 'noriel7', 'Sunflower ????', 'sda', 'I get cold easily ????', 'Loud places', 'Ice cream ????', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'final', '20', 'sa campus', 'Instagram', 'Night vibes', 'matcha and fries', 'None I', '', '', '', '', '', '', '', '', '', '', '', 84, NULL, '2026-03-15 02:53:35', 'noriel7', 'Tulip ????', 'dubai chwey', 'I get cold easily ????', 'None really', 'Ice cream ????', 'the timing isn\'t right for me', NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'hh`', '19', 'guyyghg', 'Shopee message', 'Sunset — yeah that time', 'jhkj', 'bad music taste, that one matters', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 03:41:56', 'noriel7', 'k', 'h', 'i get cold easily, i need a jacket everywhere', '', 'i don\'t really eat dessert', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Noriel', '22', 'sa campus', 'Instagram, Messenger, Text', 'Night — better vibes honestly', 'fries and beer', 'bad music taste, that one matters', 'totally spontaneous, figure it out as we go', 'loose plan is fine, just a general idea', 'low — let\'s just sit somewhere and exist', 'we\'re both gonna be awkward and that\'s okay', '', 'random topics, wherever it goes', 'drinks and chill, picnic, museum date, beach or nature', 'd', 'minimal, i\'m not here to exercise', '', '', 86, '2026-03-21 20:00', '2026-03-15 06:09:41', 'noriel7', 'Tulip ????', '', 'honestly doesn\'t matter to me', 'loud places, super crowded spots', 'ice cream, always', 'corny mo kasi', NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'ulol', '21', 'asd', 'Instagram', 'Morning', 'asd', 'slow walkers, i will lose my mind', 'totally spontaneous, figure it out as we go', 'loose plan is fine, just a general idea', 'depends on my mood that day honestly', 'chill and no pressure', 'i\'ll leave it to you', 'real stuff — get to know each other properly', 'just drive, no destination', '', 'minimal, i\'m not here to exercise', 'both — depends on who i\'m with', 'Array', 98, '2026-03-19 17:08', '2026-03-15 07:56:04', 'noriel7', 'Rose, none', 'asd', 'i get cold easily, i need a jacket everywhere', 'loud places', 'ice cream', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'final testing', '21', 'cabanatuan', 'Instagram, Messenger', 'Night', 'UHHH dk', 'none honestly, i\'m pretty chill', '', '', '', '', '', '', '', '', '', '', '', 87, NULL, '2026-03-15 09:17:09', 'noriel7', 'Rose, none', 'dubai chewy', 'i get cold easily, i need a jacket everywhere', 'none really', 'ice cream', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'asd', '21', 'sds', 'Instagram', 'Morning', 'sdasdas', 'slow walkers, i will lose my mind', 'do something, not just sit around', 'loose plan is fine, just a general idea', 'lowkey and relaxed', 'we\'re both gonna be awkward and that\'s okay', 'ideally just us, somewhere quiet', 'real stuff — get to know each other properly', '', '', 'minimal, i\'m not here to exercise', 'talker — i will carry the conversation, don\'t worry', 'Array', 94, '2026-04-02 18:27', '2026-03-15 10:00:31', 'noriel7', 'Rose, none', 'asd', 'i\'m usually fine with most places', 'loud places', 'ice cream', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'final testingggg', '20', 'sdajsdhasgdjasgd', 'Instagram, Telegram', 'Morning', 'asdasdas', 'bad music taste, that one matters', 'something lowkey, stay-in type', 'i like knowing what we\'re doing beforehand', 'lowkey and relaxed', 'chill and no pressure', 'ideally just us, somewhere quiet', 'get to know each other properly', 'coffee shop', 'sadsad', 'minimal, i\'m not here to exercise', 'talker — i will carry the conversation, don\'t worry', 'Array', 96, '2026-03-17 18:30', '2026-03-15 10:26:17', 'noriel7', 'Rose, none', 'asdasda', 'i get cold easily, i need a jacket everywhere', 'loud places', 'ice cream', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'juan final testing', '19', 'asdsa', 'Instagram', 'Morning', 'sdasdas', 'bad music taste, that one matters', 'something lowkey, stay-in type', 'i like knowing what we\'re doing beforehand', 'lowkey and relaxed', 'chill and no pressure', 'ideally just us, somewhere quiet', 'get to know each other properly', 'coffee shop', '', 'minimal, i\'m not here to exercise', 'talker — i will carry the conversation, don\'t worry', 'Array', 84, '2026-03-18 18:34', '2026-03-15 10:32:34', 'juan123', 'Rose, none', 'asdas', 'i get cold easily, i need a jacket everywhere', 'loud places', 'ice cream', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'juan legit final testing', '21', 'asdasd', 'Instagram', 'Morning', 'asdasd', 'bad music taste, that one matters', 'something lowkey, stay-in type', 'i like knowing what we\'re doing beforehand', 'lowkey and relaxed', 'chill and no pressure', 'ideally just us, somewhere quiet', 'get to know each other properly', 'coffee shop', '', 'minimal, i\'m not here to exercise', 'talker — i will carry the conversation, don\'t worry', 'Array', 83, '2026-03-18 18:40', '2026-03-15 10:36:35', 'juan123', 'Rose, none', 'asdasda', 'i get cold easily, i need a jacket everywhere', 'loud places', 'ice cream', NULL, 'yes, i have a strict curfew', 'very strict — they need to know everything', 'close by only, around our area', 'yes', 'sa kingina', 'another_day'),
(38, 'sdasd', '20', 'sdasd', 'Instagram', 'Morning', 'dsads', 'bad music taste, that one matters', '', '', '', '', '', '', '', '', '', '', '', 86, '2026-03-18 18:46', '2026-03-15 10:43:50', 'juan123', 'Rose, none', 'asds', 'i get cold easily, i need a jacket everywhere', 'loud places', 'ice cream', NULL, '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `site_owners`
--

CREATE TABLE `site_owners` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_items` text DEFAULT NULL,
  `promise_text` varchar(500) DEFAULT 'di ako masamang tao promise, go out with me please',
  `whyyy_text` varchar(255) DEFAULT 'okay okay let me make my case first...',
  `resume_expectations` text DEFAULT NULL,
  `resume_skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_owners`
--

INSERT INTO `site_owners` (`id`, `username`, `password`, `created_at`, `profile_items`, `promise_text`, `whyyy_text`, `resume_expectations`, `resume_skills`) VALUES
(1, 'noriel7', '$2y$10$g40ei83djInI9WEsSxmvOu7CWprbzMHyMrq4WX0pPoPeYFpko3AEy', '2026-03-11 08:14:55', '[\"\\ud83c\\udf5c will always share food\",\"\\ud83c\\udf19 good late night company\",\"\\ud83d\\udde3\\ufe0f actually listens when you talk\",\"\\ud83d\\ude02 kinda funny naman\",\"\\ud83d\\ude97 may wheels (important)\",\"checkkk\"]', 'checkkkk', 'okay okay let me tasngdjahsgdjhasgdjh', '[\"i pay attention to the small things you mention. if you say you\'ve been craving something, i\'ll remember it.\",\"i don\'t just buy gifts \\u2014 i make them. cards, playlists, little things that took actual thought and time.\",\"if something needs fixing, i fix it. if you need help carrying something, i\'m already carrying it.\",\"i check in. not in an overwhelming way \\u2014 just a \\\"how was your day\\\" kind of way that actually means it.\",\"i\'ll plan the date so you don\'t have to think about it. just show up.\",\"i\'m the kind of person who stays until the end \\u2014 of the movie, the conversation, the night.\",\"i\'ll make sure you get home safe. always.\",\"checkkk\"]', '[\"\\u2726 active listener\",\"\\u2726 gift maker (not just buyer)\",\"\\u2726 remembers what you said\",\"\\u2726 drives\",\"\\u2726 pays for food\",\"\\u2726 actually funny\",\"\\u2726 good playlist curator\",\"\\u2726 will not ghost\",\"\\u2726 opens doors\",\"\\u2726 no weird expectations\",\"\\u2726 night drive certified \\ud83d\\ude97\",\"\\u2726 makes the effort\",\"checkkkk\"]'),
(2, 'juan123', '$2y$10$UAhB9wCaIYd8q/qdmS.9yuR0ZainhM2r3l1oLpB4rh5LIWg8jpOwq', '2026-03-11 08:22:52', NULL, 'di ako masamang tao promise, go out with me please', 'okay okay let me make my case first...', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_compatibility_answers`
--
ALTER TABLE `admin_compatibility_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maybe_reasons`
--
ALTER TABLE `maybe_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id` (`response_id`);

--
-- Indexes for table `responder_compatibility_answers`
--
ALTER TABLE `responder_compatibility_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_owners`
--
ALTER TABLE `site_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_compatibility_answers`
--
ALTER TABLE `admin_compatibility_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maybe_reasons`
--
ALTER TABLE `maybe_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `responder_compatibility_answers`
--
ALTER TABLE `responder_compatibility_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `site_owners`
--
ALTER TABLE `site_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `responses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
