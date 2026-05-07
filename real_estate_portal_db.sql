-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 02:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `real_estate_portal_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOrUpdateUser` (IN `p_userId` INT, IN `p_userName` VARCHAR(50), IN `p_contactInfo` VARCHAR(200), IN `p_passwordHash` VARCHAR(255), IN `p_userType` ENUM('agent','buyer','renter'))   BEGIN
    IF p_userId IS NULL THEN
        INSERT INTO Users(userName, contactInfo, passwordHash, userType)
        VALUES(p_userName, p_contactInfo, p_passwordHash, p_userType);
    ELSE
        UPDATE Users
        SET userName = p_userName,
            contactInfo = p_contactInfo,
            passwordHash = p_passwordHash,
            userType = p_userType
        WHERE userId = p_userId;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessTransaction` (IN `p_propertyId` INT, IN `p_userId` INT, IN `p_type` ENUM('sale','rental'), IN `p_amount` DECIMAL(12,2))   BEGIN
    INSERT INTO Transactions(propertyId, userId, transactionType, transactionDate, amount)
    VALUES(p_propertyId, p_userId, p_type, NOW(), p_amount);

    IF p_type = 'sale' THEN
        UPDATE Properties SET status = 'sold' WHERE propertyId = p_propertyId;
    ELSE
        UPDATE Properties SET status = 'rented' WHERE propertyId = p_propertyId;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favoriteId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `savedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favoriteId`, `userId`, `propertyId`, `savedDate`) VALUES
(1, 8, 4, '2026-05-04 20:11:25'),
(2, 12, 5, '2026-05-04 21:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiryId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `inquiryDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`inquiryId`, `userId`, `propertyId`, `message`, `inquiryDate`) VALUES
(1, 6, 4, 'I am interested in buying this', '2026-04-24 17:07:53'),
(2, 8, 4, 'I am interested in this building.', '2026-05-04 20:11:18'),
(3, 11, 4, 'I am interested in this property. When is the next viewing.', '2026-05-04 20:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `propertyId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `propertyType` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('available','sold','rented') DEFAULT 'available',
  `agentId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`propertyId`, `title`, `propertyType`, `address`, `city`, `price`, `status`, `agentId`) VALUES
(4, 'Test House', 'House', '1248 Hogwarts', 'New York', 500000.00, 'available', 4),
(5, 'Apple Home', 'Apartment', '456 Fulton Rd', 'Brooklyn', 500000.00, 'rented', 10);

-- --------------------------------------------------------

--
-- Stand-in structure for view `propertylistingview`
-- (See below for the actual view)
--
CREATE TABLE `propertylistingview` (
`propertyId` int(11)
,`title` varchar(100)
,`propertyType` varchar(50)
,`city` varchar(100)
,`price` decimal(12,2)
,`status` enum('available','sold','rented')
,`agentName` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `transactionType` enum('sale','rental') NOT NULL,
  `transactionDate` datetime NOT NULL,
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `transactions`
--
DELIMITER $$
CREATE TRIGGER `AfterTransactionInsert` AFTER INSERT ON `transactions` FOR EACH ROW BEGIN
    IF NEW.transactionType = 'sale' THEN
        UPDATE Properties SET status = 'sold' WHERE propertyId = NEW.propertyId;
    ELSE
        UPDATE Properties SET status = 'rented' WHERE propertyId = NEW.propertyId;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `contactInfo` varchar(200) DEFAULT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `userType` enum('agent','buyer','renter') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userName`, `contactInfo`, `passwordHash`, `userType`) VALUES
(4, 'agent_maria', 'maria@agency.com', '$2y$10$QEUh7xaDj1HEYgBZgBTeau83a8dLyJphRYrU3Ed.XVCVJkpkBZxAa', 'agent'),
(5, 'buyer_james', 'james@email.com', '$2y$10$aCr5qkwUbh.WcgkYsu9YceaPXK9Qh2TbKWbpOPfR6umqmAj9GxOhe', 'buyer'),
(6, 'renter_lisa', 'lisa@email.com', '$2y$10$MaKSSooMHWTgWJNbKN1bjeic3MGdu4BRfO8O0LYEFU87reTW.hxjW', 'renter'),
(8, 'Pooja Sharma', '1234567890', '$2y$10$DmuBBgIjCSMECqI3QId1auqpbgVnZF6ZzKE27khj2g.otpkDuKxAO', 'renter'),
(9, 'John Smith', '987654321', '$2y$10$Eh5n5Rh2rNlzoNdysM4cB.E/LS51YJcg7Bp3EX4U.xrqJVfL9tZlK', 'agent'),
(10, 'Joe Smith', '1234567846', '$2y$10$iX/KSbo8TAkgjs.aBSIOZeNpX3K3yNsSWPHtsZHOk5fHnZUPz1hzy', 'agent'),
(11, 'Kate Charles', '2323121222', '$2y$10$Vu5uUIoZl65vscinpPXGAuPoxepvFBTBRX62hEEhz5.g8.9Ych8Q.', 'buyer'),
(12, 'James Charter', '598t798648', '$2y$10$hq2NpyYA5f6mvQsXpdrSgeXNT.kXEd8psrQCD9IrF1pjFgvh89XUi', 'buyer');

-- --------------------------------------------------------

--
-- Structure for view `propertylistingview`
--
DROP TABLE IF EXISTS `propertylistingview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `propertylistingview`  AS SELECT `p`.`propertyId` AS `propertyId`, `p`.`title` AS `title`, `p`.`propertyType` AS `propertyType`, `p`.`city` AS `city`, `p`.`price` AS `price`, `p`.`status` AS `status`, `u`.`userName` AS `agentName` FROM (`properties` `p` join `users` `u` on(`p`.`agentId` = `u`.`userId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favoriteId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiryId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`propertyId`),
  ADD KEY `agentId` (`agentId`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionId`),
  ADD KEY `propertyId` (`propertyId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `userName` (`userName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favoriteId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `propertyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`);

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`agentId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
