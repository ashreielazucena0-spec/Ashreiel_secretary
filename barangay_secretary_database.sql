-- Ashreiel Barangay Secretary System
-- MySQL 8.0+ database
-- Generated from the uploaded Ashreiel_secretary project structure.
-- Database: barangay_secretary

CREATE DATABASE IF NOT EXISTS `barangay_secretary`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `barangay_secretary`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `meetings`;
DROP TABLE IF EXISTS `cases`;
DROP TABLE IF EXISTS `requests`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `households`;
DROP TABLE IF EXISTS `officials`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `residents`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `residents` (
  `id` VARCHAR(20) NOT NULL,
  `first` VARCHAR(100) NOT NULL,
  `middle` VARCHAR(100) NULL,
  `last` VARCHAR(100) NOT NULL,
  `birth` DATE NULL,
  `sex` ENUM('Male','Female') NULL,
  `civil` ENUM('Single','Married','Widowed','Separated') NULL,
  `contact` VARCHAR(30) NULL,
  `purok` VARCHAR(100) NULL,
  `occupation` VARCHAR(150) NULL,
  `voter` ENUM('Registered','Not Registered') NULL,
  `senior` ENUM('Yes','No') NOT NULL DEFAULT 'No',
  `pwd` ENUM('Yes','No') NOT NULL DEFAULT 'No',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_residents_name` (`last`,`first`),
  KEY `idx_residents_purok` (`purok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` VARCHAR(20) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `role` ENUM('Admin','Secretary') NOT NULL DEFAULT 'Secretary',
  `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `households` (
  `id` VARCHAR(20) NOT NULL,
  `head` VARCHAR(20) NOT NULL,
  `purok` VARCHAR(100) NULL,
  `members` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_households_purok` (`purok`),
  CONSTRAINT `fk_households_head`
    FOREIGN KEY (`head`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `certificates` (
  `id` VARCHAR(30) NOT NULL,
  `resident` VARCHAR(20) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `purpose` VARCHAR(255) NULL,
  `date` DATE NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_certificates_resident` (`resident`),
  KEY `idx_certificates_date` (`date`),
  CONSTRAINT `fk_certificates_resident`
    FOREIGN KEY (`resident`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `requests` (
  `id` VARCHAR(30) NOT NULL,
  `resident` VARCHAR(20) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `purpose` VARCHAR(255) NULL,
  `date` DATE NULL,
  `status` ENUM('Pending','Processing','Ready','Released') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_requests_resident` (`resident`),
  KEY `idx_requests_status` (`status`),
  CONSTRAINT `fk_requests_resident`
    FOREIGN KEY (`resident`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cases` (
  `id` VARCHAR(30) NOT NULL,
  `complainant` VARCHAR(20) NOT NULL,
  `respondent` VARCHAR(20) NOT NULL,
  `nature` VARCHAR(255) NOT NULL,
  `date` DATE NULL,
  `status` ENUM('Pending','Resolved') NOT NULL DEFAULT 'Pending',
  `details` TEXT NULL,
  `action` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cases_date` (`date`),
  KEY `idx_cases_status` (`status`),
  CONSTRAINT `fk_cases_complainant`
    FOREIGN KEY (`complainant`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_cases_respondent`
    FOREIGN KEY (`respondent`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `meetings` (
  `id` VARCHAR(20) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `date` DATE NULL,
  `time` TIME NULL,
  `venue` VARCHAR(200) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_meetings_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documents` (
  `id` VARCHAR(30) NOT NULL,
  `number` VARCHAR(100) NULL,
  `title` VARCHAR(255) NOT NULL,
  `type` ENUM('Resolution','Ordinance') NOT NULL,
  `date` DATE NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_documents_number` (`number`),
  KEY `idx_documents_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` VARCHAR(30) NOT NULL,
  `resident` VARCHAR(20) NOT NULL,
  `type` VARCHAR(150) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `date` DATE NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payments_resident` (`resident`),
  KEY `idx_payments_date` (`date`),
  CONSTRAINT `fk_payments_resident`
    FOREIGN KEY (`resident`) REFERENCES `residents` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `officials` (
  `id` VARCHAR(20) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `term` VARCHAR(50) NULL,
  `contact` VARCHAR(30) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_officials_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `time` VARCHAR(30) NOT NULL,
  `user` VARCHAR(100) NOT NULL,
  `action` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logs_time` (`time`),
  KEY `idx_logs_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `residents` (`id`, `first`, `middle`, `last`, `birth`, `sex`, `civil`, `contact`, `purok`, `occupation`, `voter`, `senior`, `pwd`) VALUES
('R-001', 'Jay-en', 'rosal', 'Pasco', '2007-01-19', 'Male', 'Single', '09940223962', 'Purok Falcata', 'kawatan', 'Registered', 'No', 'No'),
('R-002', 'Jermie', 'Encarnacion', 'Capadiso', '2007-04-28', 'Female', 'Single', '09317178781', 'Purok Acasia', 'Teacher', 'Registered', 'No', 'No'),
('R-003', 'Ashreiel', 'Carreon', 'Azucena', '2005-05-12', 'Male', 'Single', '09163983310', 'Purok Subis', 'Student', 'Registered', 'No', 'No');
INSERT INTO `users` (`id`, `username`, `name`, `role`, `status`, `password`) VALUES
('U-001', 'admin', 'Administrator', 'Admin', 'Active', 'admin123'),
('U-002', 'secretary', 'Maria Santos', 'Secretary', 'Inactive', 'secretary123'),
('U-003', 'wailhi', 'Ashreiel Azucena', 'Secretary', 'Active', 'maogani');
INSERT INTO `households` (`id`, `head`, `purok`, `members`) VALUES
('HH-001', 'R-001', 'Purok Falcata', '4'),
('HH-003', 'R-002', 'Purok Malipayon', '2');
INSERT INTO `certificates` (`id`, `resident`, `type`, `purpose`, `date`, `amount`) VALUES
('CERT-001', 'R-002', 'Barangay Clearance', 'School', '2026-08-27', '100'),
('CERT-002', 'R-001', 'Certificate of Indigency', 'hospital', '2026-09-01', '100');
INSERT INTO `requests` (`id`, `resident`, `type`, `purpose`, `date`, `status`) VALUES
('REQ-001', 'R-001', 'Certificate of Residency', 'School', '2026-08-27', 'Processing');
INSERT INTO `cases` (`id`, `complainant`, `respondent`, `nature`, `date`, `status`, `details`, `action`) VALUES
('B-2026-001', 'R-001', 'R-001', 'sabaan', '2026-08-27', 'Resolved', '', '');
INSERT INTO `meetings` (`id`, `title`, `date`, `time`, `venue`) VALUES
('M-001', 'Barangay Assembly', '2026-08-30', '09:00', 'Barangay Hall'),
('M-002', 'Christmas Party', '2026-12-20', '13:00', 'Barangay Hall');
INSERT INTO `documents` (`id`, `number`, `title`, `type`, `date`) VALUES
('RES-2026-001', 'RES-2026-001', 'Waste Management Resolution', 'Resolution', '2026-08-27');
INSERT INTO `payments` (`id`, `resident`, `type`, `amount`, `date`) VALUES
('OR-001', 'R-001', 'Barangay Clearance', '50', '2026-08-27');
INSERT INTO `officials` (`id`, `name`, `position`, `term`, `contact`) VALUES
('O-001', 'Juan Dela Cruz', 'Punong Barangay', '2023-2026', '09171234567'),
('O-002', 'Mary Jane L. Gansad', 'Barangay Secretary', '2023-2026', '09181234567');
INSERT INTO `activity_logs` (`time`, `user`, `action`) VALUES
('2026-09-01 06:58 AM', 'admin', 'Added a new record in Meetings & Events'),
('2026-09-01 06:51 AM', 'admin', 'Added a new record in Residents'),
('2026-09-01 06:47 AM', 'admin', 'Updated a record (O-001) in Officials'),
('2026-09-01 06:46 AM', 'admin', 'Updated a record (O-002) in Officials'),
('2026-09-01 06:45 AM', 'admin', 'Logged into the system'),
('2026-09-01 06:44 AM', 'admin', 'Logged out of the system'),
('2026-09-01 06:40 AM', 'admin', 'Logged into the system'),
('2026-09-01 06:39 AM', 'admin', 'Logged out of the system'),
('2026-09-01 06:33 AM', 'admin', 'Printed certificate (CERT-002)'),
('2026-09-01 06:32 AM', 'admin', 'Logged into the system'),
('2026-09-01 06:31 AM', 'admin', 'Logged out of the system'),
('2026-09-01 04:58 AM', 'admin', 'Printed certificate (CERT-002)'),
('2026-09-01 04:57 AM', 'admin', 'Printed certificate (CERT-002)'),
('2026-09-01 04:57 AM', 'admin', 'Added a new record in Certificates'),
('2026-09-01 04:54 AM', 'admin', 'Logged into the system'),
('2026-09-01 04:53 AM', 'admin', 'Logged out of the system'),
('2026-09-01 04:53 AM', 'admin', 'Deactivated user account (U-002)'),
('2026-09-01 04:53 AM', 'admin', 'Activated user account (U-003)'),
('2026-09-01 04:52 AM', 'admin', 'Deactivated user account (U-003)'),
('2026-09-01 04:52 AM', 'admin', 'Logged into the system'),
('2026-09-01 04:49 AM', 'admin', 'Logged out of the system'),
('2026-09-01 04:46 AM', 'admin', 'Updated a record (REQ-001) in Requests'),
('2026-09-01 04:46 AM', 'admin', 'Printed certificate (CERT-001)'),
('2026-09-01 04:42 AM', 'admin', 'Logged into the system'),
('2026-08-27 07:10 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:10 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:02 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:02 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:01 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:01 AM', 'wailhi', 'Updated a record (U-002) in User Management'),
('2026-08-27 07:01 AM', 'wailhi', 'Logged into the system'),
('2026-08-27 07:01 AM', 'wailhi', 'Created a new account (Secretary)'),
('2026-08-27 07:00 AM', 'admin', 'Logged out of the system'),
('2026-08-27 06:54 AM', 'admin', 'Printed certificate (CERT-001)'),
('2026-08-27 06:47 AM', 'admin', 'Printed certificate (CERT-001)'),
('2026-08-27 06:47 AM', 'admin', 'Added a new record in Certificates'),
('2026-08-27 06:47 AM', 'admin', 'Deleted a record (CERT-001) from Certificates'),
('2026-08-27 06:46 AM', 'admin', 'Deleted a record (HH-002) from Households'),
('2026-08-27 06:46 AM', 'admin', 'Added a new record in Households'),
('2026-08-27 06:46 AM', 'admin', 'Added a new record in Residents'),
('2026-08-27 06:42 AM', 'admin', 'Printed certificate (CERT-001)'),
('2026-08-27 06:38 AM', 'admin', 'Printed certificate (CERT-001)'),
('2026-08-27 06:38 AM', 'admin', 'Logged into the system'),
('2026-08-27 03:04 AM', 'admin', 'Logged into the system'),
('2026-08-27 03:04 AM', 'secretary', 'Logged out of the system'),
('2026-08-27 03:04 AM', 'secretary', 'Logged into the system'),
('2026-08-27 03:04 AM', 'admin', 'Logged out of the system'),
('2026-08-27 03:04 AM', 'admin', 'Updated a record (U-002) in User Management'),
('2026-08-27 03:03 AM', 'admin', 'Logged into the system'),
('2026-08-27 03:03 AM', 'admin', 'Logged out of the system'),
('2026-08-27 03:03 AM', 'admin', 'Updated a record (U-002) in User Management'),
('2026-08-27 02:59 AM', 'admin', 'Updated a record (CERT-001) in Certificates'),
('2026-08-27 02:58 AM', 'admin', 'Updated a record (B-2026-001) in Complaints / Blotter'),
('2026-08-27 02:58 AM', 'admin', 'Added a new record in Complaints / Blotter'),
('2026-08-27 02:57 AM', 'admin', 'Updated a record (REQ-001) in Requests'),
('2026-08-27 02:56 AM', 'admin', 'Updated a record (REQ-001) in Requests'),
('2026-08-27 02:55 AM', 'admin', 'Updated a record (CERT-001) in Certificates'),
('2026-08-27 02:55 AM', 'admin', 'Updated a record (CERT-001) in Certificates'),
('2026-08-27 02:54 AM', 'admin', 'Added a new record in Certificates'),
('2026-08-27 02:53 AM', 'admin', 'Updated a record (HH-001) in Households'),
('2026-08-27 02:53 AM', 'admin', 'Added a new record in Residents'),
('2026-08-27 02:51 AM', 'admin', 'Logged into the system'),
('2026-08-27 02:51 AM', 'admin', 'Logged out of the system'),
('2026-08-27 02:50 AM', 'admin', 'Deleted a record (B-2026-001) from Complaints / Blotter'),
('2026-08-27 02:44 AM', 'admin', 'Deleted a record (CERT-001) from Certificates'),
('2026-08-27 02:43 AM', 'admin', 'Updated a record (REQ-001) in Requests'),
('2026-08-27 02:41 AM', 'admin', 'Logged into the system'),
('2026-08-27 02:41 AM', 'secretary', 'Logged out of the system'),
('2026-08-27 02:41 AM', 'secretary', 'Logged into the system'),
('2026-08-27 02:40 AM', 'admin', 'Logged out of the system'),
('2026-08-27 02:40 AM', 'admin', 'Updated a record (U-002) in User Management'),
('2026-08-27 02:40 AM', 'admin', 'Logged into the system'),
('2026-08-27 02:39 AM', 'admin', 'Logged out of the system'),
('2026-08-27 02:39 AM', 'admin', 'Updated a record (U-002) in User Management'),
('2026-08-27 02:38 AM', 'admin', 'Logged into the system'),
('2026-08-27 02:38 AM', 'admin', 'Logged out of the system'),
('2026-08-27 02:38 AM', 'admin', 'Deleted a record (R-002) from Residents'),
('2026-08-27 02:38 AM', 'admin', 'Deleted a record (R-001) from Residents'),
('2026-08-27 02:38 AM', 'admin', 'Deleted a record (R-003) from Residents'),
('2026-08-27 02:37 AM', 'admin', 'Reset the system to demo data'),
('2026-08-27 02:37 AM', 'system', 'Database initialized');

-- Useful reporting views for the dashboard.
DROP VIEW IF EXISTS `v_resident_names`;
CREATE VIEW `v_resident_names` AS
SELECT id, CONCAT_WS(' ', first, NULLIF(middle,''), last) AS full_name,
       sex, civil, purok, contact, occupation, voter, senior, pwd
FROM residents;

DROP VIEW IF EXISTS `v_certificate_details`;
CREATE VIEW `v_certificate_details` AS
SELECT c.id, c.type, c.purpose, c.date, c.amount,
       r.id AS resident_id,
       CONCAT_WS(' ', r.first, NULLIF(r.middle,''), r.last) AS resident_name
FROM certificates c
JOIN residents r ON r.id = c.resident;

DROP VIEW IF EXISTS `v_request_details`;
CREATE VIEW `v_request_details` AS
SELECT q.id, q.type, q.purpose, q.date, q.status,
       r.id AS resident_id,
       CONCAT_WS(' ', r.first, NULLIF(r.middle,''), r.last) AS resident_name
FROM requests q
JOIN residents r ON r.id = q.resident;

DROP VIEW IF EXISTS `v_payment_details`;
CREATE VIEW `v_payment_details` AS
SELECT p.id, p.type, p.amount, p.date,
       r.id AS resident_id,
       CONCAT_WS(' ', r.first, NULLIF(r.middle,''), r.last) AS resident_name
FROM payments p
JOIN residents r ON r.id = p.resident;

-- The current uploaded PHP project uses writable/data/db.json through
-- App\Libraries\DataStore. This SQL database is the relational database
-- target for converting that layer to MySQL.
