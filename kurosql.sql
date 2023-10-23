-- User Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userId` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `roleId` INT,
  `orgId` INT,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(`username`, `email`)
);

-- Role Table
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `roleId` INT AUTO_INCREMENT PRIMARY KEY,
  `roleName` VARCHAR(50) NOT NULL
);

-- Organization Table
DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
  `orgId` INT AUTO_INCREMENT PRIMARY KEY,
  `orgName` VARCHAR(255) NOT NULL,
  `apiKey` VARCHAR(255) NOT NULL,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(`apiKey`)
);

-- Login Logs Table with IP Address and Auto-Delete After 10 Entries
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `logId` INT AUTO_INCREMENT PRIMARY KEY,
  `userId` INT,
  `status` ENUM('Success', 'Failed') NOT NULL,
  `ipAddress` VARCHAR(50) NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`userId`) REFERENCES `users`(`userId`)
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
INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`) VALUES 
('admin', '$2y$10$R/LZ8/ojdHpO3xCw60albOtj5uECEaLS1SSyLEJvYy5D7vwAnSb.m', 'kuro@kuro.admin', 1, NULL);