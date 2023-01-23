CREATE TABLE `userGroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) UNSIGNED NOT NULL,
  `groupName` varchar(40) NOT NULL
);