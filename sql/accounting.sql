CREATE TABLE `acc_chartofaccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(10),
  `account_name` VARCHAR(255) NOT NULL,
  `account_type` ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
  `notes` text
);

CREATE TABLE `acc_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `business_name` VARCHAR(155) NOT NULL,
  `external_id` int(11),
  `receipt_date` datetime NOT NULL,
  `receipt_amount` DECIMAL(10, 2) NOT NULL,
  `notes` text
);

CREATE TABLE `acc_receipt_breakdown` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `receipt_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `item` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `notes` text
);