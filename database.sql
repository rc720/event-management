-- MySQL Database Schema for Creative Event & Portfolio Management Website
-- Created: 2026-05-19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database Selection (Uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS `creative_events_db`;
-- USE `creative_events_db`;

-- --------------------------------------------------------
-- Table structure for table `admin_users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin user: username 'admin', password 'admin123'
-- Hash is generated via password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `admin_users` (`id`, `username`, `password`, `fullname`, `email`) 
VALUES (1, 'admin', '$2y$10$cx5KfIlESNXdZkJjLygX7.r0RkIpXb5TkbMnNmpKdvn5WIYNVo/RC', 'System Administrator', 'admin@creativeevents.com')
ON DUPLICATE KEY UPDATE `username`='admin';

-- --------------------------------------------------------
-- Table structure for table `blogs`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `featured_image` VARCHAR(255) NOT NULL,
  `short_description` TEXT NOT NULL,
  `full_description` LONGTEXT NOT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`slug`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `events`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `featured_image` VARCHAR(255) NOT NULL,
  `short_description` TEXT NOT NULL,
  `full_description` LONGTEXT NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`slug`),
  INDEX (`status`),
  INDEX (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `gallery`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `featured_image` VARCHAR(255) NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `team_members`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `team_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `designation` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `featured_image` VARCHAR(255) NOT NULL,
  `facebook` VARCHAR(255) DEFAULT NULL,
  `twitter` VARCHAR(255) DEFAULT NULL,
  `instagram` VARCHAR(255) DEFAULT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `enquiries`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `enquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`is_read`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `site_title` VARCHAR(255) NOT NULL DEFAULT 'Creative Events & Portfolio',
  `logo` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `facebook_link` VARCHAR(255) DEFAULT NULL,
  `facebook_enable` TINYINT(1) NOT NULL DEFAULT 1,
  `twitter_link` VARCHAR(255) DEFAULT NULL,
  `twitter_enable` TINYINT(1) NOT NULL DEFAULT 1,
  `instagram_link` VARCHAR(255) DEFAULT NULL,
  `instagram_enable` TINYINT(1) NOT NULL DEFAULT 1,
  `linkedin_link` VARCHAR(255) DEFAULT NULL,
  `linkedin_enable` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default settings
INSERT INTO `settings` (`id`, `site_title`, `phone`, `email`, `address`, `facebook_link`, `facebook_enable`, `twitter_link`, `twitter_enable`, `instagram_link`, `instagram_enable`, `linkedin_link`, `linkedin_enable`)
VALUES (1, 'AuraEvents - Luxury Event Planner', '+1 (555) 123-4567', 'info@auraevents.com', '123 Creative Studio, Suite 500, New York, NY 10001', 'https://facebook.com/auraevents', 1, 'https://twitter.com/auraevents', 1, 'https://instagram.com/auraevents', 1, 'https://linkedin.com/company/auraevents', 1)
ON DUPLICATE KEY UPDATE `site_title`='AuraEvents - Luxury Event Planner';

COMMIT;
