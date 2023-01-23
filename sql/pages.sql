CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `intro` text DEFAULT NULL,
  `body` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `draft` tinyint(1) NOT NULL DEFAULT 1,
  `changefreq` varchar(7) NOT NULL DEFAULT 'monthly',
  `priority` decimal(10,0) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;