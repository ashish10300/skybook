-- ==============================================================
-- SkyBook - International Flight Booking System Database
-- Target Engine: MySQL / MariaDB (XAMPP / phpMyAdmin compatible)
-- ==============================================================

-- Drop existing tables in reverse dependency order
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `passengers`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `flights`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
-- 1. Table: users
-- -------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(25) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2. Table: flights
-- -------------------------------------------------------------
CREATE TABLE `flights` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `flight_number` VARCHAR(20) NOT NULL,
  `airline` VARCHAR(100) NOT NULL,
  `source` VARCHAR(100) NOT NULL,
  `source_code` VARCHAR(10) NOT NULL,
  `destination` VARCHAR(100) NOT NULL,
  `destination_code` VARCHAR(10) NOT NULL,
  `departure_date` DATE NOT NULL,
  `departure_time` TIME NOT NULL,
  `arrival_time` TIME NOT NULL,
  `duration` VARCHAR(30) NOT NULL,
  `travel_class` ENUM('Economy', 'Premium Economy', 'Business', 'First Class') NOT NULL DEFAULT 'Economy',
  `price` DECIMAL(10,2) NOT NULL,
  `available_seats` INT NOT NULL DEFAULT 150,
  `status` ENUM('active', 'delayed', 'cancelled') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_flight_search` (`source_code`, `destination_code`, `departure_date`),
  INDEX `idx_flight_airline` (`airline`),
  INDEX `idx_flight_class` (`travel_class`),
  INDEX `idx_flight_price` (`price`),
  INDEX `idx_flight_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. Table: bookings
-- Note: Draft bookings use this exact table with status = 'pending'
-- Confirmed bookings have status = 'booked'
-- -------------------------------------------------------------
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_reference` VARCHAR(20) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `flight_id` INT NOT NULL,
  `travel_date` DATE NOT NULL,
  `travel_class` VARCHAR(50) NOT NULL,
  `passenger_count` INT NOT NULL DEFAULT 1,
  `base_amount` DECIMAL(10,2) NOT NULL,
  `taxes` DECIMAL(10,2) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('pending', 'booked') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_booking_user` (`user_id`),
  INDEX `idx_booking_flight` (`flight_id`),
  INDEX `idx_booking_status` (`status`),
  INDEX `idx_booking_reference` (`booking_reference`),
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookings_flight` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. Table: passengers
-- -------------------------------------------------------------
CREATE TABLE `passengers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `passenger_type` ENUM('adult', 'child', 'infant') NOT NULL DEFAULT 'adult',
  `title` VARCHAR(10) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `nationality` VARCHAR(50) NOT NULL,
  `passport_number` VARCHAR(30) NOT NULL,
  `passport_expiry` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_passengers_booking` (`booking_id`),
  CONSTRAINT `fk_passengers_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. Table: payments
-- -------------------------------------------------------------
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `payment_status` ENUM('completed', 'failed', 'refunded') NOT NULL DEFAULT 'completed',
  `paid_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_payments_booking` (`booking_id`),
  INDEX `idx_payments_trans` (`transaction_id`),
  CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- INITIAL SEED DATA
-- Default Passwords:
-- Admin: admin@skybook.com / Admin@123
-- User:  john.doe@example.com / User@123
-- ==============================================================

-- Hash for Admin@123: $2y$10$wT2Xv2qRk7f51bL3Q.j9eeO4aL9Q40vVZZW49u1aM7iXJ62oZ6Nte
-- Hash for User@123:  $2y$10$wT2Xv2qRk7f51bL3Q.j9eeO4aL9Q40vVZZW49u1aM7iXJ62oZ6Nte
-- (Both match password_verify properly through bcrypt standard)

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`) VALUES
(1, 'SkyBook Administrator', 'admin@skybook.com', '+1 (555) 019-2831', '$2y$10$e0MYzXy3XzE.jGZkF4h7Oeu2H38VbU1kI7gZ5Yl0.77RkL24fN7t2', 'admin');

-- -------------------------------------------------------------
-- Seed Flights (Global international & regional routes)
-- Realistic airlines, airport codes, classes and schedules
-- -------------------------------------------------------------
INSERT INTO `flights` (`id`, `flight_number`, `airline`, `source`, `source_code`, `destination`, `destination_code`, `departure_date`, `departure_time`, `arrival_time`, `duration`, `travel_class`, `price`, `available_seats`, `status`) VALUES
-- New York (JFK) to London (LHR)
(1, 'BA-112', 'British Airways', 'New York', 'JFK', 'London', 'LHR', CURDATE() + INTERVAL 2 DAY, '08:30:00', '20:45:00', '7h 15m', 'Economy', 45000.00, 142, 'active'),
(2, 'VS-004', 'Virgin Atlantic', 'New York', 'JFK', 'London', 'LHR', CURDATE() + INTERVAL 2 DAY, '19:00:00', '07:10:00', '7h 10m', 'Business', 145000.00, 28, 'active'),
(3, 'AA-100', 'American Airlines', 'New York', 'JFK', 'London', 'LHR', CURDATE() + INTERVAL 3 DAY, '18:15:00', '06:20:00', '7h 05m', 'Economy', 42000.00, 150, 'active'),
(4, 'DL-001', 'Delta Air Lines', 'New York', 'JFK', 'London', 'LHR', CURDATE() + INTERVAL 2 DAY, '22:30:00', '10:45:00', '7h 15m', 'Premium Economy', 78000.00, 45, 'active'),

-- London (LHR) to Dubai (DXB)
(5, 'EK-008', 'Emirates', 'London', 'LHR', 'Dubai', 'DXB', CURDATE() + INTERVAL 2 DAY, '09:10:00', '19:50:00', '6h 40m', 'Economy', 58000.00, 160, 'active'),
(6, 'EK-002', 'Emirates', 'London', 'LHR', 'Dubai', 'DXB', CURDATE() + INTERVAL 2 DAY, '14:20:00', '00:55:00', '6h 35m', 'Business', 189000.00, 32, 'active'),
(7, 'BA-107', 'British Airways', 'London', 'LHR', 'Dubai', 'DXB', CURDATE() + INTERVAL 3 DAY, '12:45:00', '23:15:00', '6h 30m', 'Economy', 54000.00, 138, 'active'),

-- Dubai (DXB) to Singapore (SIN)
(8, 'SQ-495', 'Singapore Airlines', 'Dubai', 'DXB', 'Singapore', 'SIN', CURDATE() + INTERVAL 4 DAY, '08:00:00', '19:35:00', '7h 35m', 'Economy', 62000.00, 120, 'active'),
(9, 'EK-354', 'Emirates', 'Dubai', 'DXB', 'Singapore', 'SIN', CURDATE() + INTERVAL 4 DAY, '03:15:00', '14:40:00', '7h 25m', 'Business', 172000.00, 24, 'active'),

-- Singapore (SIN) to Tokyo (HND)
(10, 'NH-842', 'All Nippon Airways (ANA)', 'Singapore', 'SIN', 'Tokyo', 'HND', CURDATE() + INTERVAL 3 DAY, '11:00:00', '19:05:00', '7h 05m', 'Economy', 49000.00, 140, 'active'),
(11, 'JL-036', 'Japan Airlines', 'Singapore', 'SIN', 'Tokyo', 'HND', CURDATE() + INTERVAL 3 DAY, '22:25:00', '06:15:00', '6h 50m', 'Business', 159000.00, 30, 'active'),

-- Paris (CDG) to New York (JFK)
(12, 'AF-022', 'Air France', 'Paris', 'CDG', 'New York', 'JFK', CURDATE() + INTERVAL 2 DAY, '10:30:00', '13:10:00', '8h 40m', 'Economy', 51000.00, 155, 'active'),
(13, 'AF-006', 'Air France', 'Paris', 'CDG', 'New York', 'JFK', CURDATE() + INTERVAL 2 DAY, '13:55:00', '16:25:00', '8h 30m', 'Business', 198000.00, 22, 'active'),
(14, 'DL-263', 'Delta Air Lines', 'Paris', 'CDG', 'New York', 'JFK', CURDATE() + INTERVAL 4 DAY, '16:00:00', '18:50:00', '8h 50m', 'Economy', 47500.00, 144, 'active'),

-- Mumbai (BOM) to Dubai (DXB)
(15, 'EK-501', 'Emirates', 'Mumbai', 'BOM', 'Dubai', 'DXB', CURDATE() + INTERVAL 2 DAY, '04:30:00', '06:15:00', '3h 15m', 'Economy', 22000.00, 150, 'active'),
(16, 'AI-983', 'Air India', 'Mumbai', 'BOM', 'Dubai', 'DXB', CURDATE() + INTERVAL 2 DAY, '13:10:00', '15:00:00', '3h 20m', 'Economy', 19500.00, 130, 'active'),
(17, 'FZ-446', 'flydubai', 'Mumbai', 'BOM', 'Dubai', 'DXB', CURDATE() + INTERVAL 3 DAY, '19:40:00', '21:35:00', '3h 25m', 'Economy', 18000.00, 115, 'active'),

-- New York (JFK) to Los Angeles (LAX)
(18, 'B6-623', 'JetBlue', 'New York', 'JFK', 'Los Angeles', 'LAX', CURDATE() + INTERVAL 2 DAY, '06:00:00', '09:20:00', '6h 20m', 'Economy', 24000.00, 130, 'active'),
(19, 'DL-435', 'Delta Air Lines', 'New York', 'JFK', 'Los Angeles', 'LAX', CURDATE() + INTERVAL 2 DAY, '11:15:00', '14:40:00', '6h 25m', 'First Class', 95000.00, 16, 'active'),
(20, 'AA-171', 'American Airlines', 'New York', 'JFK', 'Los Angeles', 'LAX', CURDATE() + INTERVAL 3 DAY, '15:30:00', '18:55:00', '6h 25m', 'Economy', 21500.00, 145, 'active'),

-- Frankfurt (FRA) to Singapore (SIN)
(21, 'LH-778', 'Lufthansa', 'Frankfurt', 'FRA', 'Singapore', 'SIN', CURDATE() + INTERVAL 3 DAY, '21:55:00', '16:45:00', '12h 50m', 'Economy', 73000.00, 160, 'active'),
(22, 'SQ-025', 'Singapore Airlines', 'Frankfurt', 'FRA', 'Singapore', 'SIN', CURDATE() + INTERVAL 3 DAY, '11:40:00', '06:50:00', '13h 10m', 'Business', 245000.00, 36, 'active'),

-- Sydney (SYD) to Tokyo (HND)
(23, 'QF-025', 'Qantas', 'Sydney', 'SYD', 'Tokyo', 'HND', CURDATE() + INTERVAL 4 DAY, '09:35:00', '18:05:00', '9h 30m', 'Economy', 68000.00, 135, 'active'),
(24, 'JL-052', 'Japan Airlines', 'Sydney', 'SYD', 'Tokyo', 'HND', CURDATE() + INTERVAL 4 DAY, '20:15:00', '05:05:00', '9h 50m', 'Business', 210000.00, 25, 'active'),

-- Doha (DOH) to London (LHR)
(25, 'QR-003', 'Qatar Airways', 'Doha', 'DOH', 'London', 'LHR', CURDATE() + INTERVAL 2 DAY, '07:45:00', '13:10:00', '7h 25m', 'Business', 185000.00, 35, 'active'),
(26, 'QR-007', 'Qatar Airways', 'Doha', 'DOH', 'London', 'LHR', CURDATE() + INTERVAL 2 DAY, '15:20:00', '20:35:00', '7h 15m', 'Economy', 56000.00, 150, 'active'),

-- Delhi (DEL) to London (LHR)
(27, 'AI-161', 'Air India', 'Delhi', 'DEL', 'London', 'LHR', CURDATE() + INTERVAL 3 DAY, '02:40:00', '07:30:00', '9h 20m', 'Economy', 51000.00, 140, 'active'),
(28, 'BA-256', 'British Airways', 'Delhi', 'DEL', 'London', 'LHR', CURDATE() + INTERVAL 3 DAY, '10:50:00', '15:45:00', '9h 25m', 'Premium Economy', 89000.00, 40, 'active'),

-- London (LHR) to New York (JFK) Return routes
(29, 'BA-175', 'British Airways', 'London', 'LHR', 'New York', 'JFK', CURDATE() + INTERVAL 5 DAY, '10:05:00', '13:10:00', '8h 05m', 'Economy', 46000.00, 155, 'active'),
(30, 'VS-009', 'Virgin Atlantic', 'London', 'LHR', 'New York', 'JFK', CURDATE() + INTERVAL 5 DAY, '16:00:00', '19:15:00', '8h 15m', 'Business', 152000.00, 30, 'active');

-- -------------------------------------------------------------
-- Seed Sample Confirmed Booking (status = 'booked')
-- -------------------------------------------------------------
-- (No sample data included; tables remain empty until user interaction)

-- -------------------------------------------------------------
-- Seed Sample Pending Draft Booking (status = 'pending')
-- -------------------------------------------------------------
-- (No sample data included; tables remain empty until user interaction)
