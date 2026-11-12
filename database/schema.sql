-- ====================================================================
-- Database Relasional 15 Tabel - TKJ Service Business Web App
-- Database Engine: MariaDB / MySQL (XAMPP Compatible)
-- Architecture: Normalized 3NF with Foreign Keys, Indexes & Constraints
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `db_tkj_service` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_tkj_service`;

-- Non-strict foreign key checks during creation
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables to allow clean re-imports
DROP TABLE IF EXISTS `ai_conversations`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `work_logs`;
DROP TABLE IF EXISTS `work_schedules`;
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `service_orders`;
DROP TABLE IF EXISTS `spareparts`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `technicians`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------------------
-- 1. Table: `roles`
-- Defines user permissions & access levels
-- --------------------------------------------------------------------
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. Table: `users`
-- Main user authentication table linked to `roles`
-- --------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT 'default.png',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_users_role_id` (`role_id`),
    INDEX `idx_users_username` (`username`),
    INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. Table: `customers`
-- Extended profile for Customer users
-- --------------------------------------------------------------------
CREATE TABLE `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `customer_type` ENUM('umum', 'sekolah', 'instansi', 'siswa') NOT NULL DEFAULT 'umum',
    `address` TEXT DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT 'Kota',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_customers_user_id` (`user_id`),
    INDEX `idx_customers_type` (`customer_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. Table: `technicians`
-- Extended profile for Student/Instructor Technicians
-- --------------------------------------------------------------------
CREATE TABLE `technicians` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `nis_nip` VARCHAR(50) DEFAULT NULL,
    `class_grade` VARCHAR(50) DEFAULT 'XII TKJ',
    `specialization` VARCHAR(255) DEFAULT NULL,
    `skill_level` ENUM('junior', 'senior', 'lead') NOT NULL DEFAULT 'junior',
    `status` ENUM('available', 'on_duty', 'off') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_technicians_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_technicians_user_id` (`user_id`),
    INDEX `idx_technicians_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. Table: `services`
-- Master catalog of TKJ technical services
-- --------------------------------------------------------------------
CREATE TABLE `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `base_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `estimated_duration_hours` INT NOT NULL DEFAULT 2,
    `icon` VARCHAR(50) DEFAULT 'bi-wrench',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_services_code` (`code`),
    INDEX `idx_services_category` (`category`),
    INDEX `idx_services_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. Table: `service_orders`
-- Transactions and service orders placed by customers
-- --------------------------------------------------------------------
CREATE TABLE `service_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(30) NOT NULL UNIQUE,
    `customer_id` INT NOT NULL,
    `technician_id` INT DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'assigned', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `service_address` TEXT NOT NULL,
    `problem_description` TEXT NOT NULL,
    `scheduled_date` DATE NOT NULL,
    `scheduled_time` TIME DEFAULT '09:00:00',
    `completed_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_orders_code` (`order_code`),
    INDEX `idx_orders_customer_id` (`customer_id`),
    INDEX `idx_orders_technician_id` (`technician_id`),
    INDEX `idx_orders_status` (`status`),
    INDEX `idx_orders_scheduled_date` (`scheduled_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. Table: `order_details`
-- Line items for each service order
-- --------------------------------------------------------------------
CREATE TABLE `order_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `service_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(12,2) NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ord_details_order` FOREIGN KEY (`order_id`) REFERENCES `service_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ord_details_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_order_details_order_id` (`order_id`),
    INDEX `idx_order_details_service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 8. Table: `work_schedules`
-- Technician duty scheduling
-- --------------------------------------------------------------------
CREATE TABLE `work_schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `technician_id` INT NOT NULL,
    `order_id` INT DEFAULT NULL,
    `work_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `status` ENUM('scheduled', 'ongoing', 'done', 'rescheduled') NOT NULL DEFAULT 'scheduled',
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_schedules_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_schedules_order` FOREIGN KEY (`order_id`) REFERENCES `service_orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_schedules_technician_id` (`technician_id`),
    INDEX `idx_schedules_order_id` (`order_id`),
    INDEX `idx_schedules_work_date` (`work_date`),
    INDEX `idx_schedules_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 9. Table: `work_logs`
-- Field execution audit log recorded by technicians
-- --------------------------------------------------------------------
CREATE TABLE `work_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `technician_id` INT NOT NULL,
    `log_type` ENUM('checkin', 'diagnosis', 'repair', 'part_replacement', 'testing', 'checkout') NOT NULL,
    `description` TEXT NOT NULL,
    `photo_evidence` VARCHAR(255) DEFAULT NULL,
    `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_work_logs_order` FOREIGN KEY (`order_id`) REFERENCES `service_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_work_logs_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_work_logs_order_id` (`order_id`),
    INDEX `idx_work_logs_technician_id` (`technician_id`),
    INDEX `idx_work_logs_log_type` (`log_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 10. Table: `spareparts`
-- Inventory of replacement hardware parts and cables
-- --------------------------------------------------------------------
CREATE TABLE `spareparts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `part_code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `stock_qty` INT NOT NULL DEFAULT 0,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'pcs',
    `unit_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `selling_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `min_stock` INT NOT NULL DEFAULT 5,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_spareparts_code` (`part_code`),
    INDEX `idx_spareparts_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 11. Table: `transactions`
-- Billing invoice summarizing service + sparepart costs
-- --------------------------------------------------------------------
CREATE TABLE `transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transaction_code` VARCHAR(30) NOT NULL UNIQUE,
    `order_id` INT NOT NULL UNIQUE,
    `total_service_fee` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_sparepart_fee` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(12,2) NOT NULL,
    `payment_status` ENUM('unpaid', 'partially_paid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_transactions_order` FOREIGN KEY (`order_id`) REFERENCES `service_orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_trx_code` (`transaction_code`),
    INDEX `idx_trx_order_id` (`order_id`),
    INDEX `idx_trx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 12. Table: `payments`
-- Customer payment records and verification
-- --------------------------------------------------------------------
CREATE TABLE `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payment_code` VARCHAR(30) NOT NULL UNIQUE,
    `transaction_id` INT NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `payment_method` ENUM('cash', 'transfer_bank', 'qris', 'e_wallet') NOT NULL,
    `proof_image` VARCHAR(255) DEFAULT NULL,
    `payment_date` DATETIME NOT NULL,
    `status` ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    `verified_by_user_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payments_verifier` FOREIGN KEY (`verified_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_payments_trx_id` (`transaction_id`),
    INDEX `idx_payments_method` (`payment_method`),
    INDEX `idx_payments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 13. Table: `ratings`
-- Customer feedback, star ratings, and testimonials
-- --------------------------------------------------------------------
CREATE TABLE `ratings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL UNIQUE,
    `customer_id` INT NOT NULL,
    `technician_id` INT DEFAULT NULL,
    `stars` TINYINT NOT NULL CHECK (`stars` BETWEEN 1 AND 5),
    `comment` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ratings_order` FOREIGN KEY (`order_id`) REFERENCES `service_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ratings_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ratings_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_ratings_order_id` (`order_id`),
    INDEX `idx_ratings_customer_id` (`customer_id`),
    INDEX `idx_ratings_technician_id` (`technician_id`),
    INDEX `idx_ratings_stars` (`stars`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 14. Table: `notifications`
-- User notifications for status updates and assignments
-- --------------------------------------------------------------------
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('order_status', 'assignment', 'payment', 'system') NOT NULL DEFAULT 'system',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `link_url` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_notifications_user_id` (`user_id`),
    INDEX `idx_notifications_is_read` (`is_read`),
    INDEX `idx_notifications_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 15. Table: `ai_conversations`
-- AI assistant consultation history log
-- --------------------------------------------------------------------
CREATE TABLE `ai_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `session_code` VARCHAR(50) NOT NULL,
    `sender` ENUM('user', 'ai_assistant') NOT NULL,
    `message` TEXT NOT NULL,
    `intent_topic` VARCHAR(50) DEFAULT 'general_inquiry',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ai_conv_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_ai_conv_user_id` (`user_id`),
    INDEX `idx_ai_conv_session` (`session_code`),
    INDEX `idx_ai_conv_sender` (`sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- SEED DATA TESTING
-- Requirement: 1 Kepsek, 1 Operator, 3 Teknisi, 3 Customer, min 8 Jasa
-- Password for all seed users: 'password123'
-- Hash: $2y$10$4.a5dJvWkZ1F7fC0.1H9r.nL3vC7M4Z4.Kz.Xm1B2C3D4E5F6G7H8
-- ====================================================================

-- 1. Roles Seed
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'kepala_sekolah', 'Kepala Sekolah', 'Pimpinan pengawas kinerja & laporan kualitatif TEFA'),
(2, 'operator', 'Operator TKJ', 'Pengelola pemesanan, verifikasi, & penjadwalan teknisi'),
(3, 'teknisi', 'Teknisi Siswa TKJ', 'Pelaksana teknis pengerjaan lapangan & perawatan IT'),
(4, 'customer', 'Pelanggan / Customer', 'Pemesan layanan jasa teknis IT & jaringan');

-- 2. Users Seed (8 Users Total)
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `full_name`, `phone`) VALUES
-- 1 Kepala Sekolah (Pass: kepalasekolah101)
(1, 1, 'kepala_sekolah', 'kepsek@sch.id', '$2y$10$K.vuNGTKWu.sRXJbMPeeGOqwBcX3Q5rAa4ARVNYj1v4zX7pR9UjLC', 'Drs. H. Hendra M.Pd', '081100001111'),
-- 1 Operator (Pass: operator456)
(2, 2, 'operator', 'operator@tkj.sch.id', '$2y$10$J6wtAl21qtRm4183NQjLp.qruHZ8hyM8nnmeS/Kroh2x1gaSQov7m', 'Siti Rahmawati, S.Kom', '081298765432'),
-- 3 Teknisi (Pass: teknisi789)
(3, 3, 'teknisi', 'budi.teknisi@tkj.sch.id', '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW', 'Budi Santoso', '085711223344'),
(4, 3, 'teknisi2', 'rizky.teknisi@tkj.sch.id', '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW', 'Rizky Pratama', '085722334455'),
(5, 3, 'teknisi3', 'fajar.teknisi@tkj.sch.id', '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW', 'Fajar Kurniawan', '085733445566'),
-- 3 Customers (Pass: customer123)
(6, 4, 'customer', 'ahmad.dahlan@gmail.com', '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86', 'Ahmad Dahlan', '081234567890'),
(7, 4, 'customer2', 'dewi.lestari@gmail.com', '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86', 'Dewi Lestari', '081245678901'),
(8, 4, 'customer3', 'sdn1.budiutomo@sch.id', '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86', 'Budi Utomo (SD Negeri 1)', '081256789012');

-- 3. Customers Extended Profile Seed (3 Customers)
INSERT INTO `customers` (`id`, `user_id`, `customer_type`, `address`, `city`) VALUES
(1, 6, 'umum', 'Jl. Merdeka No. 12, Kel. Kebon Manis', 'Kota Bandung'),
(2, 7, 'umum', 'Jl. Pemuda No. 45, Ruko B3', 'Kota Bandung'),
(3, 8, 'sekolah', 'Jl. Pendidikan No. 1, SDN 1 Bandung', 'Kota Bandung');

-- 4. Technicians Extended Profile Seed (3 Technicians)
INSERT INTO `technicians` (`id`, `user_id`, `nis_nip`, `class_grade`, `specialization`, `skill_level`, `status`) VALUES
(1, 3, '22231001', 'XII TKJ 1', 'Instalasi CCTV & Fiber Optic', 'senior', 'available'),
(2, 4, '22231015', 'XII TKJ 1', 'Konfigurasi Router MikroTik & Firewall', 'senior', 'on_duty'),
(3, 5, '22231028', 'XII TKJ 2', 'Hardware PC/Laptop & Troubleshooting OS', 'junior', 'available');

-- 5. Services Seed (9 Services - Minimum 8 required)
INSERT INTO `services` (`id`, `code`, `name`, `category`, `description`, `base_price`, `estimated_duration_hours`, `icon`) VALUES
(1, 'SVC-CCTV-01', 'Instalasi CCTV Analog & DVR 4 Channel', 'Keamanan Jaringan', 'Pemasangan 4 unit kamera CCTV Analog, penarikan kabel coaxial, setting DVR, dan konfigurasi monitoring smartphone.', 200000.00, 4, 'bi-camera-video-fill'),
(2, 'SVC-CCTV-02', 'Instalasi IP Camera & NVR 8 Channel', 'Keamanan Jaringan', 'Pemasangan IP Camera High Definition dengan Switch PoE, penarikan kabel UTP Cat6, NVR, & cloud setup.', 350000.00, 6, 'bi-camera-reels-fill'),
(3, 'SVC-NET-01', 'Instalasi & Cabling Jaringan LAN UTP/STP', 'Infrastruktur Network', 'Pemasangan kabel UTP Cat6, crimping RJ45, penataan kabel trunking, faceplate wall, & pengujian cable tester.', 100000.00, 3, 'bi-diagram-3-fill'),
(4, 'SVC-NET-02', 'Pemasangan & Setting Access Point Wi-Fi', 'Infrastruktur Network', 'Instalasi Access Point ceiling/wall, konfigurasi SSID dual-band, roaming setup, & keamanan WPA3.', 120000.00, 2, 'bi-wifi'),
(5, 'SVC-HW-01', 'Service Laptop Mati Total & Overheat', 'Maintenance Hardware', 'Pembongkaran laptop, pembersihan fan heatsink, ganti pasta thermal high-conductive, & repair motherboard ringan.', 150000.00, 3, 'bi-laptop-fill'),
(6, 'SVC-HW-02', 'Perbaikan PC Desktop & Upgrade SSD/RAM', 'Maintenance Hardware', 'Perbaikan PC mati/bluescreen, instalasi SSD NVMe/SATA, cloning sistem operasi, & upgrade RAM.', 80000.00, 2, 'bi-pc-display'),
(7, 'SVC-RTR-01', 'Konfigurasi Router MikroTik & Bandwidth Queue', 'Network Management', 'Setting dasar MikroTik (WAN/LAN), Firewall Filter, NAT, Bandwidth Simple Queue, & DNS Server.', 150000.00, 2, 'bi-router-fill'),
(8, 'SVC-RTR-02', 'Setting Hotspot Voucher & Radius Server MikroTik', 'Network Management', 'Konfigurasi portal Wi-Fi Hotspot voucher, template login custom, user manager radius, & limiting paket jam/quota.', 250000.00, 3, 'bi-ticket-detailed-fill'),
(9, 'SVC-SW-01', 'Instalasi Software Office, OS Windows/Linux & Antivirus', 'Software & OS', 'Install ulang OS Windows 11 / Linux Ubuntu LTS, driver resmi, MS Office, PDF Reader, & antivirus terupdate.', 60000.00, 2, 'bi-cpu-fill');

-- 6. Service Orders Seed
INSERT INTO `service_orders` (`id`, `order_code`, `customer_id`, `technician_id`, `status`, `priority`, `service_address`, `problem_description`, `scheduled_date`, `scheduled_time`) VALUES
(1, 'ORD-20260829-001', 1, 1, 'in_progress', 'high', 'Jl. Merdeka No. 12, Kel. Kebon Manis', 'Ingin pasang 4 kamera CCTV di garasi dan halaman toko swalayan.', '2026-08-30', '09:00:00'),
(2, 'ORD-20260829-002', 2, 2, 'assigned', 'medium', 'Jl. Pemuda No. 45, Ruko B3', 'Setting router MikroTik RB750Gr3 untuk pembagian bandwidth 50Mbps 10 komputer.', '2026-08-31', '10:00:00'),
(3, 'ORD-20260829-003', 3, 3, 'completed', 'high', 'Jl. Pendidikan No. 1, SDN 1 Bandung', 'Perbaikan 2 unit PC Lab Sekolah lemot dan instalasi ulang Windows 10.', '2026-08-28', '13:00:00');

-- 7. Order Details Seed
INSERT INTO `order_details` (`id`, `order_id`, `service_id`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(1, 1, 1, 1, 200000.00, 200000.00, 'Paket CCTV Analog 4 Channel Complete Setup'),
(2, 2, 7, 1, 150000.00, 150000.00, 'Setting Router MikroTik Ruko Pemuda'),
(3, 3, 6, 2, 80000.00, 160000.00, 'Perbaikan & Upgrade 2 PC Lab SDN 1'),
(4, 3, 9, 2, 60000.00, 120000.00, 'Install ulang OS & Office 2 PC');

-- 8. Work Schedules Seed
INSERT INTO `work_schedules` (`id`, `technician_id`, `order_id`, `work_date`, `start_time`, `end_time`, `status`, `notes`) VALUES
(1, 1, 1, '2026-08-30', '09:00:00', '13:00:00', 'scheduled', 'Bawa bor listrik, kabel coaxial 50m, & BNC connector'),
(2, 2, 2, '2026-08-31', '10:00:00', '12:00:00', 'scheduled', 'Bawa kabel LAN patchcord & Winbox'),
(3, 3, 3, '2026-08-28', '13:00:00', '17:00:00', 'done', 'Pengerjaan 2 PC Lab SDN 1 selesai dengan lancar');

-- 9. Work Logs Seed
INSERT INTO `work_logs` (`id`, `order_id`, `technician_id`, `log_type`, `description`, `photo_evidence`) VALUES
(1, 1, 1, 'checkin', 'Teknisi tiba di lokasi Toko Swalayan Jl. Merdeka No. 12', 'checkin_001.jpg'),
(2, 1, 1, 'diagnosis', 'Pemeriksaan titik elevasi dinding kamera dan posisi stopkontak DVR', 'diag_001.jpg'),
(3, 3, 3, 'repair', 'Pembersihan heatsink, ganti pasta processor, & instalasi OS Windows 10 Pro', 'repair_003.jpg'),
(4, 3, 3, 'checkout', 'Serah terima 2 PC Lab SDN 1 kepada Pak Budi Utomo dalam kondisi sehat', 'checkout_003.jpg');

-- 10. Spareparts Seed
INSERT INTO `spareparts` (`id`, `part_code`, `name`, `category`, `stock_qty`, `unit`, `unit_cost`, `selling_price`, `min_stock`) VALUES
(1, 'PRT-CBL-CAT6', 'Kabel UTP Belden Cat6 High Grade', 'Kabel & Connector', 300, 'meter', 4500.00, 7000.00, 50),
(2, 'PRT-RJ45-CAT6', 'Konektor RJ45 Cat6 Pass Through', 'Kabel & Connector', 200, 'pcs', 1500.00, 3000.00, 20),
(3, 'PRT-SSD-256G', 'SSD NVMe V-Gen M.2 256GB', 'Storage SSD', 10, 'unit', 260000.00, 320000.00, 3),
(4, 'PRT-CCTV-CAM', 'Kamera CCTV Hikvision 2MP Outdoor', 'Kamera CCTV', 8, 'unit', 180000.00, 240000.00, 2),
(5, 'PRT-RTR-GR3', 'MikroTik RouterBOARD RB750Gr3 hEX', 'Router', 4, 'unit', 650000.00, 780000.00, 1);

-- 11. Transactions Seed
INSERT INTO `transactions` (`id`, `transaction_code`, `order_id`, `total_service_fee`, `total_sparepart_fee`, `discount`, `tax_amount`, `grand_total`, `payment_status`) VALUES
(1, 'TRX-20260829-001', 1, 200000.00, 480000.00, 20000.00, 0.00, 660000.00, 'unpaid'),
(2, 'TRX-20260829-002', 2, 150000.00, 0.00, 0.00, 0.00, 150000.00, 'unpaid'),
(3, 'TRX-20260828-003', 3, 280000.00, 0.00, 30000.00, 0.00, 250000.00, 'paid');

-- 12. Payments Seed
INSERT INTO `payments` (`id`, `payment_code`, `transaction_id`, `amount`, `payment_method`, `proof_image`, `payment_date`, `status`, `verified_by_user_id`) VALUES
(1, 'PAY-20260828-001', 3, 250000.00, 'transfer_bank', 'proof_pay_003.jpg', '2026-08-28 16:30:00', 'verified', 2);

-- 13. Ratings Seed
INSERT INTO `ratings` (`id`, `order_id`, `customer_id`, `technician_id`, `stars`, `comment`) VALUES
(1, 3, 3, 3, 5, 'Pelayanan teknisi siswa TKJ sangat ramah, rapi, dan PC lab sekolah sekarang kembali cepat!');

-- 14. Notifications Seed
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link_url`) VALUES
(1, 6, 'Pesanan Berhasil Dibuat', 'Pesanan ORD-20260829-001 sedang diproses oleh Operator.', 'order_status', 0, 'index.php?url=dashboard'),
(2, 3, 'Tugas Lapangan Baru', 'Anda ditugaskan menangani pesanan ORD-20260829-001 di Toko Swalayan.', 'assignment', 0, 'index.php?url=dashboard'),
(3, 2, 'Pembayaran Diverifikasi', 'Pembayaran transaksi TRX-20260828-003 sebesar Rp 250.000 telah diverifikasi.', 'payment', 1, 'index.php?url=dashboard');

-- 15. AI Conversations Seed
INSERT INTO `ai_conversations` (`id`, `user_id`, `session_code`, `sender`, `message`, `intent_topic`) VALUES
(1, 6, 'SESS-AI-001', 'user', 'Berapa estimasi biaya pemasangan 4 kamera CCTV di ruko?', 'cctv_consultation'),
(2, 6, 'SESS-AI-001', 'ai_assistant', 'Estimasi biaya instalasi CCTV Analog 4 Channel mulai dari Rp 200.000 (jasa teknis). Anda juga dapat memilih paket lengkap termasuk kabel dan DVR.', 'cctv_consultation');

-- --------------------------------------------------------------------
-- 16. Table: `otp_codes`
-- Stores One-Time Passwords securely with hash, expiration, and rate limiting
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `otp_codes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `identifier` VARCHAR(100) NOT NULL,
    `otp_hash` VARCHAR(255) NOT NULL,
    `channel` ENUM('sms', 'whatsapp', 'email', 'api') DEFAULT 'whatsapp',
    `attempts` INT DEFAULT 0,
    `max_attempts` INT DEFAULT 3,
    `is_used` TINYINT(1) DEFAULT 0,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_otp_identifier` (`identifier`),
    INDEX `idx_otp_user_id` (`user_id`),
    INDEX `idx_otp_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

