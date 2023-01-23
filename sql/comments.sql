CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pageid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
);