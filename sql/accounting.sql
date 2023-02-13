CREATE TABLE `acc_chartofaccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(10),
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('Asset', 'Liability', 'Equity', 'Revenue', 'Expense') NOT NULL
);

CREATE TABLE transactions (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  transaction_date DATE NOT NULL,
  transaction_description VARCHAR(255) NOT NULL,
  transaction_total DECIMAL(10, 2) NOT NULL
);

CREATE TABLE receipts (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `internal_id` int(11) NOT NULL AUTO_INCREMENT UNIQUE,
  `external_id` int(11),
  `receipt_date` datetime NOT NULL
);

CREATE TABLE `receipts_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `receipts_id` int(11) UNSIGNED DEFAULT NULL,
  `transactions_id` int(11) UNSIGNED DEFAULT NULL
);
