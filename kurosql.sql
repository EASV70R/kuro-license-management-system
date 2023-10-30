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
  `status` INT NOT NULL DEFAULT 0, -- (0 = active, 1 = banned)
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
  `status` INT NOT NULL, -- (0 = Failed, 1 = Success)
  `ipAddress` VARCHAR(50) NOT NULL,
  `apiKeyUsed` VARCHAR(255) NOT NULL,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON DELETE CASCADE,
  FOREIGN KEY (`orgId`) REFERENCES `organizations`(`orgId`) ON DELETE CASCADE
);

-- Licenses Table
DROP TABLE IF EXISTS `licenses`;
CREATE TABLE `licenses` (
  `licenseId` INT AUTO_INCREMENT PRIMARY KEY,
  `licenseKey` VARCHAR(255) NOT NULL UNIQUE,
  `startDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expiryDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `orgId` INT,
  `createdBy` INT,
  `userId` INT DEFAULT NULL,
  `status` TINYINT NOT NULL DEFAULT 0,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`orgId`) REFERENCES `organizations`(`orgId`) ON DELETE CASCADE,
  FOREIGN KEY (`createdBy`) REFERENCES `users`(`userId`) ON DELETE SET NULL,
  FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON DELETE SET NULL
);


-- License Logs Table
DROP TABLE IF EXISTS `license_logs`;
CREATE TABLE `license_logs` (
  `logId` INT AUTO_INCREMENT PRIMARY KEY,
  `licenseId` INT,
  `userId` INT,
  `action` VARCHAR(255), -- e.g., 'Activated', 'Expired', 'Created'
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`licenseId`) REFERENCES `licenses`(`licenseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON DELETE CASCADE
);

-- CleanUpLogs Event
DELIMITER //
CREATE EVENT CleanUpLogs
ON SCHEDULE EVERY 5 MINUTE
DO
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE oldUserId INT;
  DECLARE cur CURSOR FOR SELECT DISTINCT userId FROM login_logs;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;

  read_loop: LOOP
    FETCH cur INTO oldUserId;
    IF done THEN
      LEAVE read_loop;
    END IF;

    SET @rowCount = (SELECT COUNT(*) FROM login_logs WHERE userId = oldUserId);
    SET @deleteCount = @rowCount - 10;

    IF @deleteCount > 0 THEN
      SET @sql = CONCAT('DELETE FROM login_logs WHERE userId = ', oldUserId, ' ORDER BY timestamp ASC LIMIT ', @deleteCount);
      PREPARE stmt FROM @sql;
      EXECUTE stmt;
      DEALLOCATE PREPARE stmt;
    END IF;

  END LOOP;

  CLOSE cur;
END //
DELIMITER ;

-- Insert Roles Data (Super Admin, Organization Admin, User)
INSERT INTO `roles` (`roleName`) VALUES
('Super Admin'),
('Organization Admin'),
('User');

-- Insert Example Organizations
INSERT INTO `organizations` (`orgName`, `apiKey`) VALUES 
('Kuro', '5270283d392663007843f7081aee8b'),
('Test', '89093e9d76a7a0ce48f675bb5bd704');

-- Insert Example Users (Super Admin, Org Admin, Regular User)
-- Passwords are bcrypt hashed "password"
INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`, `status`) VALUES 
('admin', '$2y$10$R/LZ8/ojdHpO3xCw60albOtj5uECEaLS1SSyLEJvYy5D7vwAnSb.m', 'kuro@kuro.dk', 1, 1, 0);
INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`, `status`) VALUES 
('test', '$2y$10$R/LZ8/ojdHpO3xCw60albOtj5uECEaLS1SSyLEJvYy5D7vwAnSb.m', 'test@test.dk', 2, 1, 0);

INSERT INTO `licenses` (`licenseKey`, `startDate`, `expiryDate`, `orgId`, `createdBy`, `userId`, `status`) VALUES 
('84d6daf1a9208c4a', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 1, 1, 1, 1);
INSERT INTO `licenses` (`licenseKey`, `startDate`, `expiryDate`, `orgId`, `createdBy`, `userId`, `status`) VALUES 
('ea1fa95c166d9326', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 1, 1, 2, 1);
