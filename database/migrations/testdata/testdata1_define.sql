
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

INSERT INTO `roles` (`id`, `name`, `desc`) VALUES
(0, '一般', '一般のユーザーIDロール'),
(99, '管理者', '管理者ロール');

INSERT INTO `categories` (`id`, `category`, `desc`) VALUES
(0, 'none', ''),
(1, 'schedule', 'スケジュール'),
(2, 'account', '家計簿');

INSERT INTO `subcategories` (`id`, `category_id`, `subcategory`, `desc`) VALUES
(0, 0, 'none', ''),
(1, 1, '予定', '予定'),
(2, 1, 'タスク', 'タスク'),
(3, 2, '入金', '入金'),
(4, 2, '出金', '出金'),
(5, 1, 'メモ', 'メモ');

INSERT INTO `types` (`id`, `type`, `desc`) VALUES
(0, 'none', '日付設定なし'),
(1, 'time', 'ある日の時間帯'),
(2, 'term', 'ある日付の期間'),
(3, 'date', 'ある日'),
(4, 'deadline', '所定日の期限'),
(5, 'repeat_daily', '毎日同時刻'),
(6, 'repeat_weekly', '毎週同曜日'),
(7, 'repeat_monthly', '毎月同日'),
(8, 'repeat_yearly', '毎年同月同日');

INSERT INTO `statuses` (`id`, `status`, `desc`) VALUES
(0, 'enable', '有効'),
(1, 'disable', '無効'),
(2, 'done', '完了'),
(99, 'removed', '削除');

INSERT INTO `account_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '食費', NULL, NULL),
(2, '日用品', NULL, NULL),
(3, '交通費', NULL, NULL),
(4, '家賃', NULL, NULL),
(5, '娯楽', NULL, NULL),
(6, '給料', NULL, NULL),
(9, 'その他', NULL, NULL);

-- 1st user (email:webapp002@tech/pass:12345678)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_id`, `created_at`, `updated_at`) VALUES
('1', 'webapp002', 'webapp002@tech', NULL, '$2y$12$OTS60mFTbu51gBEJL.wymuJjHmZV3mR3pQAd58T01vxh2PyMKKNiy', NULL, '99', NULL, NULL);
