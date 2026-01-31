--
-- Datenbank: `schlucht`
--
-- Tabellenstruktur für Tabelle `users`
--
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabellenstruktur für Tabelle `rate_limits`
CREATE TABLE rate_limits (
    identifier VARCHAR(255) PRIMARY KEY,
    attempts TEXT,
    updated_at TIMESTAMP
);

