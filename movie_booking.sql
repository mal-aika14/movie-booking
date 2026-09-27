-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2026 at 02:04 PM
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
-- Database: `movie_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Admin', 'admin@moviebook.com', '$2b$12$89qMhcZgR8WAKSAMQKW.OupXkAx4mT8qzUHfyc0MEHihEDfm0Buqm');

-- --------------------------------------------------------

--
-- Table structure for table `booked_seats`
--

CREATE TABLE `booked_seats` (
  `id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked_seats`
--

INSERT INTO `booked_seats` (`id`, `show_id`, `booking_id`, `seat_number`) VALUES
(1, 4, 14, 'B4'),
(2, 4, 14, 'B7'),
(3, 4, 14, 'B5'),
(4, 4, 14, 'B6'),
(5, 15, 15, 'C1'),
(6, 15, 15, 'C2'),
(7, 15, 15, 'C3'),
(8, 15, 15, 'C4'),
(9, 15, 15, 'C5'),
(10, 8, 16, 'B4'),
(11, 8, 16, 'B5'),
(12, 8, 16, 'B6'),
(13, 8, 16, 'B7'),
(17, 16, 18, 'B3'),
(18, 16, 18, 'B4'),
(19, 16, 18, 'B5'),
(20, 16, 18, 'B6'),
(21, 3, 19, 'B4'),
(22, 3, 19, 'B5'),
(23, 3, 19, 'B6'),
(24, 3, 19, 'B7'),
(25, 3, 19, 'B8'),
(26, 18, 20, 'C3'),
(27, 18, 20, 'C4'),
(28, 18, 20, 'C5'),
(29, 18, 20, 'C2'),
(30, 18, 20, 'C1'),
(31, 18, 20, 'C6'),
(32, 16, 21, 'C5'),
(33, 16, 21, 'C4'),
(34, 16, 21, 'C6'),
(35, 16, 21, 'C7'),
(36, 7, 22, 'B1'),
(37, 7, 22, 'B2'),
(38, 7, 22, 'B3'),
(39, 7, 22, 'B4'),
(40, 20, 23, 'C4'),
(41, 20, 23, 'C5'),
(42, 20, 23, 'C7'),
(43, 20, 23, 'C6'),
(44, 20, 23, 'C8'),
(45, 23, 24, 'C10'),
(46, 23, 24, 'C9'),
(47, 23, 24, 'C8'),
(48, 23, 24, 'C6'),
(49, 23, 24, 'C7'),
(50, 23, 25, 'B1'),
(51, 23, 25, 'B2'),
(52, 23, 25, 'B3'),
(53, 23, 25, 'B4'),
(54, 23, 25, 'B6'),
(55, 23, 25, 'B5'),
(56, 4, 26, 'D6'),
(57, 4, 26, 'D7'),
(58, 4, 26, 'D8'),
(59, 4, 26, 'D9');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `number` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `seats` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'Cash',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `show_id` int(11) NOT NULL,
  `seat_class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `customer_name`, `number`, `movie_id`, `seats`, `amount`, `payment_method`, `booking_date`, `show_id`, `seat_class_id`) VALUES
(8, NULL, 'hadi', 274592543, 16, 5, 2000.00, 'Cash', '2026-08-23 06:00:52', 5, 3),
(10, NULL, 'hareem', 333473805, 21, 7, 2800.00, 'Cash', '2026-08-23 06:13:02', 16, 3),
(11, NULL, 'Hafsa', 38462926, 14, 1, 400.00, 'Cash', '2026-08-27 11:19:20', 4, 3),
(12, NULL, 'Hadi', 336582957, 31, 4, 3400.00, 'Cash', '2026-08-29 06:22:07', 17, 5),
(13, NULL, 'nayab', 336582957, 16, 3, 3100.00, 'Cash', '2026-08-29 06:31:13', 5, 4),
(14, 4, 'mariam', 336582957, 14, 4, 3400.00, 'Cash', '2026-08-30 07:59:15', 4, 5),
(15, 4, 'maria', 336582957, 20, 5, 5300.00, 'Cash', '2026-08-30 08:00:02', 15, 4),
(18, NULL, 'hijab', 839217199, 21, 4, 4000.00, 'Cash', '2026-08-31 15:03:15', 16, 5),
(19, 7, 'maham', 335272845, 12, 5, 4400.00, 'Cash', '2026-09-01 07:50:42', 3, 5),
(20, 7, 'maham', 335272845, 25, 6, 7000.00, 'Cash', '2026-09-01 07:52:29', 18, 4),
(21, 1, 'rida', 839217199, 21, 4, 5200.00, 'Cash', '2026-09-03 07:11:34', 16, 4),
(22, 8, 'zarnab', 336582957, 14, 4, 3400.00, 'Cash', '2026-09-03 10:47:17', 7, 5),
(23, 8, 'Ali', 336582957, 34, 5, 6500.00, 'Online', '2026-09-03 11:19:09', 20, 4),
(24, NULL, 'Kubra', 2147483647, 35, 5, 6500.00, 'Online', '2026-09-04 05:12:03', 23, 4),
(25, 9, 'Kubra', 336582957, 35, 6, 7800.00, 'Online', '2026-09-04 05:14:30', 23, 4);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Alaya Noor', 'alaya@gmail.com', 'Booking realted query', 'I need help with booking a movie ticket.', '2026-08-31 06:00:51');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `genre` varchar(100) NOT NULL,
  `poster_url` varchar(255) NOT NULL,
  `avg_rating` decimal(2,1) NOT NULL,
  `trailer_url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `director` varchar(100) NOT NULL,
  `release_year` int(11) NOT NULL,
  `cast_names` varchar(255) NOT NULL,
  `is_coming_soon` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `genre`, `poster_url`, `avg_rating`, `trailer_url`, `description`, `director`, `release_year`, `cast_names`, `is_coming_soon`) VALUES
(11, 'AVENGERS', 'Action', 'images/avengers.jpg', 4.0, 'https://www.youtube.com/watch?v=X1aFkAkFASk', 'The Avengers is an action-packed superhero movie in which Iron Man, Captain America, Thor, Hulk, Black Widow, and Hawkeye come together to stop Loki and his alien army from taking over Earth. Despite their different personalities and abilities, the heroes must work as a team to protect humanity and save the world.', 'Joss Whedon', 2012, 'Robert Downey Jr. — Tony Stark / Iron Man, Chris Evans — Steve Rogers / Captain America, Chris Hemsworth — Thor, Mark Ruffalo — Bruce Banner / Hulk, Scarlett Johansson — Natasha Romanoff / Black Widow, Jeremy Renner — Clint Barton / Hawkeye, Tom Hiddlesto', 0),
(12, 'spider', 'Action', 'images/spider.jpg', 4.2, 'https://www.youtube.com/watch?v=62bIsvRcPv0', 'Spider-Man is a superhero action movie about Peter Parker, a teenager who gains extraordinary powers after being bitten by a genetically modified spider. He learns to use his abilities to protect innocent people while facing the dangerous villain Green Goblin.', 'Sam Raimi', 2002, 'Tobey Maguire — Peter Parker / Spider-Man, Willem Dafoe — Norman Osborn / Green Goblin, an Kirsten Dunst — Mary Jane Watson, James Franco — Harry Osborn, J.K. Simmons — J. Jonah Jameson, Rosemary Harris — Aunt May, Cliff Robertson — Uncle Ben, Elizabeth B', 0),
(14, 'Black Panther', 'action', 'images/black panther.jfif', 4.3, 'https://www.youtube.com/watch?v=xjDjIWPwcPU', 'Black Panther is a superhero action film about T’Challa, the king of the fictional African nation of Wakanda. After the death of his father, T’Challa becomes king and must protect his country from a powerful enemy, Erik Killmonger, who challenges his claim to the throne.', 'Ryan Coogler', 2018, 'Chadwick Boseman, Michael B. Jordan, Lupita Nyongo, Danai Gurira, Martin Freeman, Daniel Kaluuya, Letitia Wright, Winston Duke, Angela Bassett, Forest Whitaker, Andy Serkis, Sterling K. Brown', 0),
(15, 'Wonka', 'Adventure', 'images/wonka.jfif', 4.3, 'https://www.youtube.com/watch?v=otNh9bTjXWg', 'Wonka is a fantasy adventure movie about a young Willy Wonka and his journey to become the world’s most famous chocolatier. With his imagination, creativity, and magical chocolates, he faces powerful competitors and overcomes many challenges while trying to build his chocolate business.', 'Paul King', 2023, 'Timothée Chalamet — Willy Wonka, Olivia Colman — Mrs. Scrubbit, Hugh Grant — Lofty the Oompa-Loompa, Calah Lane — Noodle, Keegan-Michael Key — Chief of Police, Paterson Joseph — Arthur Slugworth, Matt Lucas — Prodnose, Jim Carter — Mr. Bucket, Rowan Atkin', 0),
(16, 'Jumanji', 'Adventure', 'images/jumanji.jfif', 4.0, 'https://www.youtube.com/watch?v=2QKg5SZ_35I', ' Welcome to the Jungle is an action-adventure comedy movie about four teenagers who are transported into a mysterious video game. They must take on the identities of their chosen characters, work together, and complete the game to escape Jumanji and return home.', 'Jake Kasdan', 2017, 'Dwayne Johnson — Dr. Xander Bravestone, Kevin Hart — Franklin Mouse Finbar, Jack Black — Professor Sheldon Oberon, Karen Gillan — Ruby Roundhouse, Nick Jonas — Jefferson Seaplane McDonough, Bobby Cannavale — Van Pelt, Alex Wolff — Spencer Gilpin, Ser\'Dari', 0),
(17, 'Tary zameen pr', 'Drama', 'images/tary.jfif', 4.5, 'https://www.youtube.com/watch?v=l5mP7W58AsI', 'Taare Zameen Par is an emotional drama about Ishaan, a young boy who struggles in school because of dyslexia. When a caring art teacher recognizes his talent and understands his difficulties, he helps Ishaan gain confidence and discover his true potential.', 'Aamir Khan', 2007, 'Darsheel Safary — Ishaan Awasthi, Aamir Khan — Ram Shankar Nikumbh, Tisca Chopra — Maya Awasthi, Vipin Sharma — Nandkishore Awasthi, Tanay Chheda — Rajan Damodran, Sachet Engineer — Yohaan Awasthi.', 0),
(18, 'Carry on jatta 2', 'Punjabi Comedy', 'images/jatta.jfif', 4.0, 'https://www.youtube.com/watch?v=e-hOqxaXBPo', 'Carry On Jatta 2 is a Punjabi comedy film about a man who pretends to be an orphan so that he can marry the woman he loves. His plan leads to a series of hilarious misunderstandings, lies, and chaotic situations involving both families.', 'Smeep Kang', 2018, 'Gippy Grewal, Sonam Bajwa, Binnu Dhillon, Gurpreet Ghuggi, Jaswinder Bhalla, Karamjit Anmol, Upasana Singh, B. N. Sharma', 0),
(19, 'Insidious', 'Horror', 'images/insidious.jfif', 4.3, 'https://www.youtube.com/watch?v=jxU8FU3o75A', 'Insidious is a supernatural horror film about a family whose young son mysteriously falls into a coma. As strange and terrifying events begin happening in their home, his parents discover that his spirit has become trapped in a dark supernatural realm known as The Further. They must uncover the truth and find a way to bring him back before it is too late.', 'James Wan', 2010, 'Patrick Wilson, Rose Byrne, Lin Shaye, Ty Simpkins, Barbara Hershey, Leigh Whannell, Angus Sampson, Andrew Astor', 0),
(20, 'Dora and the Lost City of Gold', 'Adventure', 'images/dora.jfif', 4.3, 'https://www.youtube.com/watch?v=ksj69JaBrAo', '**Dora and the Lost City of Gold** is a family adventure movie about Dora, a brave and curious teenager who moves to the city after spending most of her childhood exploring the jungle. When her parents disappear while searching for the legendary Incan city of Parapata, Dora and her friends are kidnapped by treasure hunters. Together, they must survive dangerous challenges, solve ancient mysteries, and find the lost city before the treasure falls into the wrong hands.\r\n', ' James Bobin', 2019, 'Isabela Moner — Dora, Eugenio Derbez — Alejandro, Michael Peña — Cole, Eva Longoria — Elena, Jeff Wahlberg — Diego, Danny Trejo — Boots (voice), Benicio del Toro — Swiper (voice), Temuera Morrison — Powell, Madeleine Madden — Sammy, Sammy Nicholas Coombe ', 0),
(21, 'Chal mera putt 2', 'Punjabi Comedy', 'images/chal.jpg', 4.7, 'https://www.youtube.com/watch?v=N1l670JGSlo', 'Chal Mera Putt 2 is a Punjabi comedy-drama film that follows a group of Pakistani and Indian immigrants living in the UK. They face financial struggles, friendship challenges, and difficulties related to their immigration status, while supporting each other through humor and emotional moments.', 'Janjot Singh', 2020, 'Amrinder Gill, Simi Chahal, Garry Sandhu, Gurshabad, Iftikhar Thakur, Nasir Chinyoti, Akram Udas, Hardeep Gill, Zafri Khan, and Nirmal Rishi.', 0),
(22, 'Bhaji in Problem', 'Punjabi Comedy', 'images/problem.jpg', 4.0, 'https://www.youtube.com/watch?v=qkMCvZw_LAc', 'Bhaji in Problem is a Punjabi comedy film about a man whose peaceful life becomes complicated when his friend asks him to help hide a secret. A series of funny misunderstandings and unexpected situations leads to hilarious chaos.', 'Smeep Kang', 2013, 'Gippy Grewal, Gurpreet Ghuggi, Raghuveer Yadav, Om Puri, B.N. Sharma, Misha Bajwa, Khushboo Grewal, Japji Khaira, and Harish Verma.', 0),
(23, 'Kala shah Kala', 'Punjabi Comedy', 'images/kala.jfif', 4.8, 'https://www.youtube.com/watch?v=iyk4qsV2Tew', 'Kala Shah Kala is a Punjabi romantic comedy about a dark-skinned man who struggles to find love because of society’s obsession with fair skin. His life changes when he meets a woman who sees beyond his appearance, leading to romance, family drama, and plenty of comedy.', 'Amarjit Saron', 2019, 'Binnu Dhillon, Sargun Mehta, Jordan Sandhu, Gurpreet Ghuggi, Karamjit Anmol, Harby Sangha, and Anita Devgan.', 0),
(24, 'Gudiya', 'Horror', 'images/gudiya.jfif', 4.2, 'https://www.youtube.com/watch?v=b7ZZ3lJPHe0', 'Gudiya is a Punjabi horror thriller about a mysterious supernatural presence that brings fear and danger into the lives of the people involved. The story combines suspense, mystery, and supernatural horror.', 'Rahul Chandre, Arunish Raaj Rohilla', 2023, 'Yuvraj Hans, Sawan Rupowali, Aarushi, Vindu Dara Singh, Shivendra Mahal, Himanshu Arora, Saurav Bagga, Samreet Bajwa, Sunita Dhir, Arunish Raaj Rohilla.', 0),
(25, 'Jatt nu churail Takri', 'Punjabi Horror Comedy', 'images/jatt.jfif', 4.6, 'https://www.youtube.com/watch?v=D4LyZPfnznE', 'The story follows Jalaludeen, a man who believes that marriage is the source of all his problems. After getting married to Rani, he begins to suspect that she is actually a supernatural being or a witch. What follows is a mix of horror, comedy, romance, and hilarious situations as he tries to uncover the truth about his wife.', 'Vikas Vashisht', 2024, 'Gippy Grewal, Sargun Mehta, Roopi Gill, Nirmal Rishi, B. N. Sharma, Ravinder Mand, Amrit Amby, Deedar Gill', 1),
(28, 'King', 'Action', 'images/king.avif', 4.5, 'https://www.youtube.com/watch?v=Uu2QK9Z9X5E', 'King follows a powerful mentor and his protégé as they become involved in a dangerous underworld filled with enemies, high-stakes missions, and intense action. Shah Rukh Khan plays the central character, while Suhana Khan is part of the mentor-protégé storyline. The film is being directed by Siddharth Anand and is scheduled to release in cinemas on 24 December 2026.', 'Siddharth Anand', 2026, 'Shah Rukh Khan, Suhana Khan, Abhishek Bachchan, Deepika Padukone, Anil Kapoor, Jackie Shroff, Jaideep Ahlawat, Raghav Juyal, Rani Mukerji, Arshad Warsi, Abhay Verma', 1),
(31, 'Moana', 'Animation', 'images/moana.jpg', 4.5, 'https://www.youtube.com/watch?v=LKFuXETZUsI', 'Moana is an animated adventure film about a brave young girl named Moana, who is chosen by the ocean to restore the heart of the goddess Te Fiti. She sets out on a dangerous journey across the sea and meets the legendary demigod Maui. Together, they face monsters, challenges, and dangerous obstacles. Along the way, Moana discovers her courage, identity, and true destiny.', 'Ron Clements, John Musker', 2016, 'Auliʻi Cravalho, Dwayne Johnson, Rachel House, Temuera Morrison, Nicole Scherzinger, Alan Tudyk, Jemaine Clement, Christopher Jackson', 1),
(32, 'Toy Story 4', 'Animation', 'images/toy.jpg', 4.6, 'https://www.youtube.com/watch?v=wmiIUN-7qhE', 'Toy Story 4 is an animated adventure film about Woody, Buzz Lightyear, and their toy friends as they begin a new chapter with Bonnie. When Bonnie creates a new toy named Forky, Woody becomes determined to keep him safe. During a road trip, Woody meets his old friend Bo Peep and faces new challenges that make him question his purpose and future. The film explores friendship, loyalty, love, and finding your own path.', 'Josh Cooley', 2026, 'Tom Hanks, Tim Allen, Annie Potts, Tony Hale, Keanu Reeves, Christina Hendricks, Jordan Peele, Keegan-Michael Key, Joan Cusack, Wallace Shawn, John Ratzenberger', 1),
(33, 'Carry on jatta 4', 'Punjabi Comedy', 'images/jatta4.jfif', 4.5, 'https://www.youtube.com/watch?v=fsDBAr42UUg', 'Carry On Jatta 4 is a Punjabi comedy film filled with lies, misunderstandings, fake relationships, and hilarious chaos. The story follows a complicated web of deception that creates trouble for a family and leads to a series of funny situations.', 'Smeep Kang', 2026, 'Gippy Grewal, Binnu Dhillon, Sargun Mehta, Gurpreet Ghuggi, Jaswinder Bhalla, Karamjit Anmol, Jasmin Bajwa, Sweetaj Brar, Shinda Grewal, Pukhraj Bhalla, B. N. Sharma, Seema Kaushal', 1),
(34, 'Maa', 'Horror', 'images/maa.jpg', 5.0, 'https://www.youtube.com/watch?v=zwtZj6YB9xk', 'Maa is a supernatural horror film about a mother who faces a terrifying evil while trying to protect her daughter. As dark supernatural forces emerge, she must confront the horrifying secrets connected to the past and fight to save her family.', 'Vishal Furia', 2025, 'Kajol, Ronit Roy, Indraneil Sengupta, Kherin Sharma, Jitin Gulati, Suraj Singh', 0),
(35, 'Shaitan', 'Horror', 'images/shaitan.jfif', 5.0, 'https://www.youtube.com/watch?v=upDhKSx7P7E', 'Shaitaan is a supernatural horror thriller about a family whose peaceful life is disrupted when a mysterious stranger enters their home and takes control of their daughter through dark supernatural powers. The parents must fight against this evil force to save their daughter.', 'Vikas Bahl', 2024, 'Ajay Devgn, R. Madhavan, Jyothika, Janki Bodiwala, Anngad Raaj', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `movie_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 34, 13, 5.0, 'Maa is a decent horror-thriller with a strong emotional story. Kajol\'s acting is impressive, but the horror could have been scarier. Overall, it\'s a good one-time watch.', '2026-09-05 06:32:02'),
(2, 16, 13, 4.0, 'Jumanji is an entertaining adventure-comedy with plenty of action, humor, and fun. The cast has great chemistry, and the video-game concept makes the story exciting. Overall, it’s a light, enjoyable movie that’s perfect for a fun movie night.', '2026-09-05 06:41:50'),
(3, 18, 13, 4.0, 'Carry on jatta 2 is a hilarious Punjabi comedy packed with misunderstandings, chaos, and entertaining performances. The story is simple but full of funny situations, making it an enjoyable watch. Overall, it’s a fun, light-hearted movie, especially for fans of Punjabi comedy.', '2026-09-05 06:56:24'),
(4, 23, 13, 4.8, 'Kala shah kala is a fun Punjabi romantic comedy with a good mix of humor, romance, and emotion. The story is entertaining, and the performances make it an enjoyable watch. Overall, it’s a light-hearted movie that’s perfect if you want something funny and easy to watch.', '2026-09-05 07:13:25'),
(5, 22, 13, 4.0, 'Bhaji in problem is a hilarious Punjabi comedy filled with misunderstandings, crazy situations, and entertaining performances. The story keeps things fun and engaging, with plenty of laughter throughout. Overall, it’s a light-hearted movie that’s great for a relaxing and funny movie night.', '2026-09-05 07:07:06'),
(6, 35, 13, 5.0, 'Shaitan is a gripping supernatural thriller with a dark and intense atmosphere. The story keeps you curious, while the performances—especially the emotional and villainous moments—make it engaging. Overall, it’s a suspenseful movie with a strong mix of horror, drama, and psychological tension.', '2026-09-05 07:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `seat_classes`
--

CREATE TABLE `seat_classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(60) NOT NULL,
  `adult_price` decimal(10,2) NOT NULL,
  `kid_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat_classes`
--

INSERT INTO `seat_classes` (`id`, `class_name`, `adult_price`, `kid_price`) VALUES
(3, 'Silver', 700.00, 500.00),
(4, 'Platinum', 1300.00, 900.00),
(5, 'Gold', 1000.00, 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theatre_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`id`, `movie_id`, `theatre_id`, `show_date`, `show_time`) VALUES
(2, 11, 4, '2026-12-14', '03:02:00'),
(3, 12, 6, '2026-09-22', '17:30:00'),
(4, 14, 6, '2026-12-09', '15:00:00'),
(5, 16, 6, '2026-09-07', '15:00:00'),
(6, 15, 7, '2026-09-30', '00:30:00'),
(7, 14, 8, '2024-05-01', '11:00:00'),
(9, 11, 8, '2026-08-01', '17:00:00'),
(10, 15, 6, '2026-09-20', '21:30:00'),
(11, 17, 7, '2010-08-30', '19:00:00'),
(12, 18, 8, '2026-12-01', '21:00:00'),
(14, 19, 8, '2026-10-05', '14:00:00'),
(15, 20, 6, '2025-10-10', '22:30:00'),
(16, 21, 7, '2026-01-04', '18:00:00'),
(17, 31, 7, '2026-08-31', '20:00:00'),
(18, 25, 6, '2026-09-06', '17:00:00'),
(19, 21, 6, '2026-09-15', '19:00:00'),
(20, 34, 6, '2026-09-05', '20:30:00'),
(21, 24, 7, '2026-09-08', '00:00:00'),
(22, 23, 8, '2026-09-10', '16:30:00'),
(23, 35, 7, '2026-09-19', '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `theatres`
--

CREATE TABLE `theatres` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theatres`
--

INSERT INTO `theatres` (`id`, `name`, `location`) VALUES
(6, 'Nueplex Cinema', 'DHA Phase 8, Karachi'),
(7, 'Atrium', 'Saddar karachi'),
(8, 'cinepax', 'DHA Phase 8, Karachi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(2, 'Alaya Noor', 'alaya@gmail.com', '$2y$10$9Bi9rgsVDZmQEPMCKWwY1eu2SgtRlXwCd20kAw/tOqBTtLqiiaR0C'),
(3, 'Dua', 'dua@gmail.com', '$2y$10$aQCGGQYAh6gDsLlWGl.A2.C1MX0d9QObGORU6K9aioJOx/9gorWky'),
(4, 'hareem', 'hareem@gmail.com', '$2y$10$CgQD9IwVA59qp6KMa58anuKvTnTxcFvsBFgoTGz5lDCRrCHeRqiDq'),
(5, 'aqsa', 'aqsa@gmail.com', '$2y$10$ZZU.q4VsKFlKehYMrlqi4u8I.95/dBR8C3xKC7f9d1HQ43MYvJlhq'),
(6, 'amna', 'amna@gmail.com', '$2y$10$0nF1HB2pgpDhblhIcA5ffuf.FlzSXNOd9hHK52A7WgaPKBfLeTMaK'),
(7, 'maham', 'maham@gmail.com', '$2y$10$ZjTscMl9piemqCKOAathxOXBO6I9XAZ5pz2GM/rmjYTVUSmPGt/i.'),
(8, 'Zarnab Fatima', 'zarnab@gmail.com', '$2y$10$uph.jketUeUhtvGMgrvFyeaKaPNH5L3z7Pr/DFjdOQz/bja4k7JDm'),
(9, 'Kubra', 'kubra@gmail.com', '$2y$10$s945f2G.v2Vmw6cFjScFf.y/ec2zuifVW39Jc.hinzIZy7gsIst3q'),
(10, 'mirha', 'mirha@gmail.com', '$2y$10$vxagw7kVm0V9uLoJtpxAGeWDXWkfR3N0otWigEr425GO0gt5kZoYu'),
(12, 'Malaika', 'malaikaa@gmail.com', '$2y$10$u/Pi2W/1IY/XDqUobGA3G.JGeqVnHWm4a3G3ib88jxWyXEL/CkDlC'),
(13, 'manahil', 'manahil@gmail.com', '$2y$10$CEQuv6G5/SUXzNJmtXcEi.NBTzw2ufyNcho3gFB8TDZaZ.NoxScai'),
(14, 'Rohan', 'rohan@gmail.com', '$2y$10$u3DlBpjL7vp2tE0i44zsn.OT1SEx7uqHbIDciLAW4pvbDhQiZVd2e');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `booked_seats`
--
ALTER TABLE `booked_seats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`movie_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seat_classes`
--
ALTER TABLE `seat_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theatres`
--
ALTER TABLE `theatres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booked_seats`
--
ALTER TABLE `booked_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `seat_classes`
--
ALTER TABLE `seat_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `theatres`
--
ALTER TABLE `theatres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
