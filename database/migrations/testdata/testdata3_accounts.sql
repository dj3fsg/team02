-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2025 at 01:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


INSERT INTO `accounts` (`id`, `user_id`, `subcategory_id`, `type_id`, `status_id`, `date`, `title`, `amount`, `memo`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 0, 99, '2025-12-21', 'タイトル。～の入金', 0.00, 'メモ。入金ありました。', NULL, '2025-12-22 00:01:07');

-- 上記でインポートできないため、手動でデータ作成する