CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(30) NOT NULL,
  `password` varchar(70) NOT NULL,
  `email` varchar(255) NOT NULL,
  `validate` varchar(32) DEFAULT NULL,
  `confirmationToken` varchar(255) DEFAULT NULL,
  `reset` varchar(32) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `blocked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;