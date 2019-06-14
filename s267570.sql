-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Giu 14, 2019 alle 11:03
-- Versione del server: 5.7.26-0ubuntu0.16.04.1
-- Versione PHP: 7.0.33-0ubuntu0.16.04.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s267570`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `bookings`
--

CREATE TABLE `bookings` (
  `seat` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `status` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `user` varchar(128) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `bookings`
--

INSERT INTO `bookings` (`seat`, `status`, `user`) VALUES
('A_4', 'booked', 'u1@p.it'),
('B_2', 'sold', 'u2@p.it'),
('B_3', 'sold', 'u2@p.it'),
('B_4', 'sold', 'u2@p.it'),
('D_4', 'booked', 'u1@p.it'),
('F_4', 'booked', 'u2@p.it');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `mail` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(512) COLLATE latin1_general_ci NOT NULL,
  `salt` varchar(256) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`mail`, `password`, `salt`) VALUES
('u1@p.it', 'a88a9264d860bbbd6a9ed2d7e0e3157d2b391979e4e67d24d7ff0b719c4aedb3', '64ce1d75b5'),
('u2@p.it', 'f8ab3a9f354af52454cb28deabed258f7354d141a725cc516cecc77b0621b6aa', 'cfa4f641b3');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`seat`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`mail`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
