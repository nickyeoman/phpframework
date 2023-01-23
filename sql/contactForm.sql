CREATE TABLE `contactForm` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `unread` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Unread is true by default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;