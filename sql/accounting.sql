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
  transaction_amount DECIMAL(10, 2) NOT NULL
);

CREATE TABLE journal_entries (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  transaction_id INT NOT NULL,
  account_id INT NOT NULL,
  entry_type ENUM('Debit', 'Credit') NOT NULL,
  entry_amount DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
  FOREIGN KEY (account_id) REFERENCES accounts(account_id)
);

CREATE TABLE parties (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  party_name VARCHAR(255) NOT NULL,
  party_type ENUM('Customer', 'Supplier', 'Employee') NOT NULL
);

CREATE TABLE ledger (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  account_id INT NOT NULL,
  ledger_date DATE NOT NULL,
  debit_amount DECIMAL(10, 2) NOT NULL,
  credit_amount DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (account_id) REFERENCES accounts(account_id)
);
