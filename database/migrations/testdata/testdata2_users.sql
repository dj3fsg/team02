-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2025 at 01:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'yamane', 'yamane@tech', NULL, '$2y$12$TAtJypNJeU.2dC9mDFDA0.iC/8TK5vIWvNGDNeBn8Ezb/EVDrsqcW', NULL, 0, '2025-12-22 02:59:25', '2025-12-22 02:59:25'),
(2, 'takumi', 'takumi@tach', NULL, '$2y$12$anzqqfDhmQYXgH/PLdbI7uBNxNOyA1v2jwO9HQCLfvXWQg8SR3He.', NULL, 0, '2025-12-22 03:00:11', '2025-12-22 03:00:11'),
(3, 'tech', 'tech@tech', NULL, '$2y$12$5QGg2jpFuA7u82nanPI5/uzHqLjCDZxJMUbxxeBR7ZSLS4C5u5uK2', NULL, 0, '2025-12-22 03:00:29', '2025-12-22 03:00:29');

-- 上記でインポートできないため、手動でデータ作成する