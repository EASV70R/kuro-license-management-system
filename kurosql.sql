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
('Kuro', 'API123KEY'),
('Test', 'API456KEY');

-- Insert Example Users (Super Admin, Org Admin, Regular User)
-- Passwords are bcrypt hashed "password"
INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`, `status`) VALUES 
('admin', '$2y$10$R/LZ8/ojdHpO3xCw60albOtj5uECEaLS1SSyLEJvYy5D7vwAnSb.m', 'kuro@kuro.admin', 1, 1, 0);