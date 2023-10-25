-- Role Table (Reference table)
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `roleId` INT AUTO_INCREMENT PRIMARY KEY,
  `roleName` VARCHAR(50) NOT NULL UNIQUE
);

-- Organization Table
DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
  `orgId` INT AUTO_INCREMENT PRIMARY KEY,
  `orgName` VARCHAR(100) NOT NULL,
  `apiKey` VARCHAR(255) NOT NULL UNIQUE,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Table (Maintaining Foreign Key relationships)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userId` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(50) NOT NULL UNIQUE,
  `roleId` INT,
  `orgId` INT,
  `status` TINYINT NOT NULL DEFAULT 0, -- (0 = active, 1 = banned)
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`roleId`) REFERENCES `roles`(`roleId`) ON DELETE SET NULL,
  FOREIGN KEY (`orgId`) REFERENCES `organizations`(`orgId`) ON DELETE SET NULL
);

-- Login Logs Table
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `logId` INT AUTO_INCREMENT PRIMARY KEY,
  `userId` INT,
  `orgId` INT,
  `status` TINYINT NOT NULL, -- (0 = Failed, 1 = Success)
  `ipAddress` VARCHAR(50) NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON DELETE CASCADE,
  FOREIGN KEY (`orgId`) REFERENCES `organizations`(`orgId`) ON DELETE CASCADE
);

-- Foreign Keys for Users Table
ALTER TABLE `users`
  ADD FOREIGN KEY (`roleId`) REFERENCES `roles`(`roleId`),
  ADD FOREIGN KEY (`orgId`) REFERENCES `organizations`(`orgId`);

-- Trigger to Automatically Delete Logs After 10 Entries
DELIMITER //
CREATE TRIGGER after_log_insert 
AFTER INSERT ON login_logs 
FOR EACH ROW 
BEGIN
  DECLARE log_count INT;
  DECLARE excess_logs INT;

  -- Count the total logs for the user
  SELECT COUNT(*) INTO log_count
  FROM login_logs 
  WHERE userId = NEW.userId;

  -- Calculate excess logs
  SET excess_logs = log_count - 10;

  -- Delete the oldest logs if there are more than 10 logs
  IF excess_logs > 0 THEN
    DELETE FROM login_logs 
    WHERE userId = NEW.userId 
    ORDER BY timestamp ASC 
    LIMIT excess_logs;
  END IF;
END //
DELIMITER ;

-- Insert Roles Data (Super Admin, Organization Admin, User)
INSERT INTO `roles` (`roleName`) VALUES
('Super Admin'),
('Organization Admin'),
('User');

-- Insert Example Organizations
INSERT INTO `organizations` (`orgName`, `apiKey`) VALUES 
('Kuro', 'API123KEY'),
('Test', 'API456KEY');

-- Insert Example Users (Super Admin, Org Admin, Regular User)
-- Passwords are bcrypt hashed "password"
INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`, `status`) VALUES 
('admin', '$2y$10$R/LZ8/ojdHpO3xCw60albOtj5uECEaLS1SSyLEJvYy5D7vwAnSb.m', 'kuro@kuro.admin', 1, 1, 0);