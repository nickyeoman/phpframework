CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `level` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `session` text DEFAULT NULL,
  `post` text DEFAULT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;