-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 11 Mar 2021, 08:59
-- Wersja serwera: 10.4.13-MariaDB
-- Wersja PHP: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `lityskatalog`
--
-- CREATE DATABASE IF NOT EXISTS `lityskatalog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `lityskatalog`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `codes`
--

CREATE TABLE `codes` (
  `id` int(11) NOT NULL,
  `code` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `components`
--

CREATE TABLE `components` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `components`
--

INSERT INTO `components` (`id`, `name`, `data`) VALUES
(1, 'home_slider', '[{\"img\":\"https://picsum.photos/3000/2000\"}]'),
(2, 'sidebar_swiper', '[{\"img\":\"https://picsum.photos/3000/2000\",\"title\":\"1Lorem ipsum1\",\"description\":\"1Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis nobis saepe illum aut omnis vitae earum reiciendis aliquid a voluptate nesciunt eligendi, maxime laborum laboriosam facere doloribus id aliquam magnam!\"},{\"img\":\"\",\"title\":\"\",\"description\":\"\"},{\"img\":\"\",\"title\":\"\",\"description\":\"\"}]'),
(3, 'sidebar_text', '[{\"html\":\"Test\"}]'),
(4, 'rules', '[{\"text\":\"Lorem ipsum\"}]'),
(5, 'ad_home', '[{\"html\":\"\"}]'),
(6, 'ad_sidebar', '[{\"html\":\"\"}]'),
(7, 'head', '[{\"logo\":\"https:\\/\\/litys.github.io\\/logo_white.png\",\"footer\":\"\\u00a9 2021. Wszelkie prawa zastrze\\u017cone. <a href=\\\"https:\\/\\/litys.github.pl\\\">litys.github.io<\\/a> | W\\u0142a\\u015bciciel katalogu jest litys.github.io.\"}]'),
(8, 'stats', '[{\"bots\":\"0\",\"visits\":0}]');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `url` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `slug` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `img` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `ratings` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `keywords` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `recommended` tinyint(1) NOT NULL,
  `accepted` tinyint(1) NOT NULL,
  `email` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `settings`
--

INSERT INTO `settings` (`id`, `name`, `data`) VALUES
(1, 'sms_gate', '0'),
(2, 'posts_pagination', '15'),
(3, 'sms_gate_text', 'Tekst informacyjny'),
(4, 'website_title', 'LITYSkatalog'),
(5, 'website_icon', 'https://litys.github.io/favicon.png'),
(6, 'moderator_mode', '0'),
(7, 'website_description', 'Opis katalogu');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `slug` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `components`
--
ALTER TABLE `components`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `codes`
--
ALTER TABLE `codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `components`
--
ALTER TABLE `components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
