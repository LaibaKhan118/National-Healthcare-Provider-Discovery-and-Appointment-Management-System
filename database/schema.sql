-- healthcare_system_schema.sql
-- Complete Database Schema for Healthcare System
-- Run this in MySQL to create all tables, views, triggers, and stored procedures

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';

-- ============================================================================
-- TABLES
-- ============================================================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int NOT NULL DEFAULT 3,
  `account_status` enum('active','suspended') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `admin_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
  `doctor_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL UNIQUE,
  `license_number` varchar(50),
  `specialization` varchar(100),
  `experience_years` int,
  `consultation_fee` decimal(10,2),
  `bio` text,
  `city` varchar(100),
  `hospital_affiliation` varchar(200),
  `is_verified` tinyint(1) DEFAULT 0,
  CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `patient_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL UNIQUE,
  `phone` varchar(20),
  `address` text,
  `date_of_birth` date,
  CONSTRAINT `fk_patient_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `specializations`;
CREATE TABLE `specializations` (
  `specialization_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `specialization_name` varchar(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `doctor_specializations`;
CREATE TABLE `doctor_specializations` (
  `doctor_id` int NOT NULL,
  `specialization_id` int NOT NULL,
  PRIMARY KEY (`doctor_id`, `specialization_id`),
  CONSTRAINT `fk_doc_spec_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_spec_spec` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`specialization_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hospitals`;
CREATE TABLE `hospitals` (
  `hospital_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `hospital_name` varchar(200) NOT NULL,
  `city` varchar(100),
  `address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `doctor_hospitals`;
CREATE TABLE `doctor_hospitals` (
  `doctor_id` int NOT NULL,
  `hospital_id` int NOT NULL,
  PRIMARY KEY (`doctor_id`, `hospital_id`),
  CONSTRAINT `fk_doc_hosp_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_hosp_hosp` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `availability`;
CREATE TABLE `availability` (
  `availability_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `doctor_id` int NOT NULL,
  `day_of_week` int NOT NULL CHECK (`day_of_week` BETWEEN 1 AND 7),
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_booked` tinyint(1) DEFAULT 0,
  UNIQUE KEY `unique_slot` (`doctor_id`, `day_of_week`, `start_time`, `end_time`),
  CONSTRAINT `fk_avail_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `appointment_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `doctor_id` int NOT NULL,
  `patient_id` int,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_status` enum('pending','completed','cancelled','no_show') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` int NOT NULL UNIQUE,
  `patient_id` int,
  `doctor_id` int NOT NULL,
  `rating` int NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_review_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `appointment_notes`;
CREATE TABLE `appointment_notes` (
  `note_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` int NOT NULL UNIQUE,
  `note_content` longtext,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_note_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VIEWS
-- ============================================================================

DROP VIEW IF EXISTS `top_doctors_view`;
CREATE VIEW `top_doctors_view` AS
SELECT 
  d.doctor_id,
  CONCAT(u.first_name, ' ', u.last_name) as full_name,
  d.specialization,
  d.experience_years,
  d.consultation_fee,
  d.city,
  COUNT(DISTINCT r.review_id) as review_count,
  COALESCE(AVG(r.rating), 0) as avg_rating
FROM doctors d
JOIN users u ON d.user_id = u.user_id
LEFT JOIN reviews r ON r.doctor_id = d.doctor_id
WHERE d.is_verified = 1
GROUP BY d.doctor_id, u.first_name, u.last_name, d.specialization, d.experience_years, d.consultation_fee, d.city;

DROP VIEW IF EXISTS `doctor_appointments_view`;
CREATE VIEW `doctor_appointments_view` AS
SELECT 
  a.appointment_id,
  a.doctor_id,
  a.patient_id,
  a.appointment_date,
  a.appointment_time,
  a.appointment_status,
  CONCAT(pu.first_name, ' ', pu.last_name) as patient_name,
  pu.email as patient_email
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN users pu ON p.user_id = pu.user_id;

DROP VIEW IF EXISTS `patient_appointment_history_view`;
CREATE VIEW `patient_appointment_history_view` AS
SELECT 
  a.appointment_id,
  a.patient_id,
  a.doctor_id,
  a.appointment_date,
  a.appointment_time,
  a.appointment_status,
  CONCAT(du.first_name, ' ', du.last_name) as doctor_name,
  d.specialization,
  d.consultation_fee
FROM appointments a
JOIN doctors d ON a.doctor_id = d.doctor_id
JOIN users du ON d.user_id = du.user_id;

DROP VIEW IF EXISTS `doctor_ranking_breakdown_view`;
CREATE VIEW `doctor_ranking_breakdown_view` AS
SELECT 
  d.doctor_id,
  CONCAT(u.first_name, ' ', u.last_name) as full_name,
  COUNT(DISTINCT a.appointment_id) as completed_appointments,
  COALESCE(AVG(r.rating), 0) as avg_rating,
  COUNT(DISTINCT r.review_id) as total_reviews,
  d.experience_years,
  d.consultation_fee
FROM doctors d
JOIN users u ON d.user_id = u.user_id
LEFT JOIN appointments a ON d.doctor_id = a.doctor_id AND a.appointment_status = 'completed'
LEFT JOIN reviews r ON a.appointment_id = r.appointment_id
WHERE d.is_verified = 1
GROUP BY d.doctor_id, u.first_name, u.last_name, d.experience_years, d.consultation_fee;

DROP VIEW IF EXISTS `admin_system_stats_view`;
CREATE VIEW `admin_system_stats_view` AS
SELECT 
  COUNT(DISTINCT CASE WHEN role_id = 1 THEN user_id END) as total_admins,
  COUNT(DISTINCT CASE WHEN role_id = 2 THEN user_id END) as total_doctors,
  COUNT(DISTINCT CASE WHEN role_id = 3 THEN user_id END) as total_patients,
  COUNT(DISTINCT CASE WHEN role_id = 2 AND account_status = 'suspended' THEN user_id END) as suspended_doctors,
  COUNT(DISTINCT CASE WHEN account_status = 'suspended' THEN user_id END) as total_suspended
FROM users;

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_book_appointment$$
CREATE PROCEDURE sp_book_appointment(
  IN p_doctor_id INT,
  IN p_patient_id INT,
  IN p_availability_id INT,
  OUT p_appointment_id INT
)
BEGIN
  DECLARE v_appointment_date DATE;
  DECLARE v_appointment_time TIME;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET p_appointment_id = -1;
  END;
  
  START TRANSACTION;
  
  SELECT CURDATE() INTO v_appointment_date;
  SELECT start_time INTO v_appointment_time FROM availability WHERE availability_id = p_availability_id;
  
  INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, appointment_status)
  VALUES (p_doctor_id, p_patient_id, v_appointment_date, v_appointment_time, 'pending');
  
  SET p_appointment_id = LAST_INSERT_ID();
  
  UPDATE availability SET is_booked = 1 WHERE availability_id = p_availability_id;
  
  COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_cancel_appointment$$
CREATE PROCEDURE sp_cancel_appointment(IN p_appointment_id INT)
BEGIN
  DECLARE v_doctor_id INT;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
  END;
  
  START TRANSACTION;
  
  SELECT doctor_id INTO v_doctor_id FROM appointments WHERE appointment_id = p_appointment_id;
  
  UPDATE appointments SET appointment_status = 'cancelled' WHERE appointment_id = p_appointment_id;
  
  UPDATE availability SET is_booked = 0 WHERE doctor_id = v_doctor_id AND is_booked = 1 LIMIT 1;
  
  COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_mark_appointment$$
CREATE PROCEDURE sp_mark_appointment(
  IN p_appointment_id INT,
  IN p_status VARCHAR(20)
)
BEGIN
  UPDATE appointments SET appointment_status = p_status WHERE appointment_id = p_appointment_id;
END$$

DROP PROCEDURE IF EXISTS sp_approve_doctor$$
CREATE PROCEDURE sp_approve_doctor(IN p_doctor_id INT)
BEGIN
  UPDATE doctors SET is_verified = 1 WHERE doctor_id = p_doctor_id;
END$$

DROP PROCEDURE IF EXISTS sp_remove_review$$
CREATE PROCEDURE sp_remove_review(IN p_review_id INT)
BEGIN
  DELETE FROM reviews WHERE review_id = p_review_id;
END$$

DELIMITER ;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

DELIMITER $$

DROP TRIGGER IF EXISTS after_review_insert$$
CREATE TRIGGER after_review_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
  -- Trigger for future analytics logging if needed
END$$

DROP TRIGGER IF EXISTS after_review_update$$
CREATE TRIGGER after_review_update
AFTER UPDATE ON reviews
FOR EACH ROW
BEGIN
  -- Trigger for future audit logging if needed
END$$

DROP TRIGGER IF EXISTS after_appointment_complete$$
CREATE TRIGGER after_appointment_complete
AFTER UPDATE ON appointments
FOR EACH ROW
WHEN (NEW.appointment_status = 'completed' AND OLD.appointment_status != 'completed')
BEGIN
  -- Trigger for post-appointment workflows if needed
END$$

DROP TRIGGER IF EXISTS after_appointment_cancel$$
CREATE TRIGGER after_appointment_cancel
AFTER UPDATE ON appointments
FOR EACH ROW
WHEN (NEW.appointment_status = 'cancelled' AND OLD.appointment_status != 'cancelled')
BEGIN
  UPDATE availability SET is_booked = 0 
  WHERE doctor_id = NEW.doctor_id AND is_booked = 1 LIMIT 1;
END$$

DROP TRIGGER IF EXISTS prevent_duplicate_booking$$
CREATE TRIGGER prevent_duplicate_booking
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  DECLARE count_bookings INT;
  SELECT COUNT(*) INTO count_bookings
  FROM appointments
  WHERE patient_id = NEW.patient_id
  AND appointment_date = NEW.appointment_date
  AND appointment_time = NEW.appointment_time
  AND appointment_status != 'cancelled';
  
  IF count_bookings > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Duplicate appointment booking';
  END IF;
END$$

DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
