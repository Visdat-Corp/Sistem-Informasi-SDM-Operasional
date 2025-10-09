-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 01:44 PM
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
-- Database: `admin_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` bigint(20) UNSIGNED NOT NULL,
  `id_karyawan` bigint(20) UNSIGNED NOT NULL,
  `tanggal_absen` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `lokasi_absen_masuk` varchar(255) DEFAULT NULL,
  `lokasi_absen_keluar` varchar(255) DEFAULT NULL,
  `foto_masuk` varchar(255) DEFAULT NULL,
  `foto_keluar` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('hadir','terlambat','lembur') DEFAULT NULL,
  `menit_keterlambatan` int(11) DEFAULT NULL,
  `is_lembur` tinyint(1) NOT NULL DEFAULT 0,
  `id_jamKerja` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `id_karyawan`, `tanggal_absen`, `jam_masuk`, `jam_keluar`, `lokasi_absen_masuk`, `lokasi_absen_keluar`, `foto_masuk`, `foto_keluar`, `keterangan`, `status`, `menit_keterlambatan`, `is_lembur`, `id_jamKerja`, `created_at`, `updated_at`) VALUES
(31, 5, '2025-10-01', '12:41:57', NULL, '-5.158163333333333,119.47824', '-5.158163333333333,119.47824', 'attendance_photos/pIX3f4fuQ68jasd3S3awT6wfrEKJutTOqiGGZKMj.jpg', 'attendance_photos/YGX35YOPS5OnLmHjWluRqA1uVdpt9742bZPHHwRB.jpg', 'Overtime work', 'lembur', 342, 1, 1, '2025-10-01 04:41:57', '2025-10-01 04:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `nama_admin` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id_admin`, `nama_admin`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@visdat.com', '$2y$12$K./a.ATBYmvASkd5/6j//.3/jj0BQyweBd8csXFdo4dxW06KB6snm', '2025-09-26 00:11:09', '2025-09-26 00:11:09'),
(2, 'Admin HR', 'hr@visdat.com', '$2y$12$V7p59xHyPwd.aZ.r71OBU.MCVBsOE1D5kpCYHhIKgFw3.Wr8Vt1nm', '2025-09-26 00:11:09', '2025-09-26 00:11:09'),
(3, 'Admin IT', 'it@visdat.com', '$2y$12$3W90O.UhdUmjwRKAKuZtlO/W1/eYSH8M2Vw4KEcwS9QOqA.t5AlxS', '2025-09-26 00:11:09', '2025-09-26 00:11:09');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` bigint(20) UNSIGNED NOT NULL,
  `nama_departemen` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `nama_departemen`, `created_at`, `updated_at`) VALUES
(1, 'IT', '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(2, 'Jaringan', '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(3, 'sistem', '2025-10-01 01:39:54', '2025-10-01 01:39:54');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jam_kerja`
--

CREATE TABLE `jam_kerja` (
  `id_jamKerja` bigint(20) UNSIGNED NOT NULL,
  `jam_masuk_normal` time NOT NULL,
  `jam_keluar_normal` time NOT NULL,
  `toleransi_keterlambatan` int(11) NOT NULL DEFAULT 0,
  `jam_lembur` time DEFAULT NULL,
  `total_jam` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jam_kerja`
--

INSERT INTO `jam_kerja` (`id_jamKerja`, `jam_masuk_normal`, `jam_keluar_normal`, `toleransi_keterlambatan`, `jam_lembur`, `total_jam`, `created_at`, `updated_at`) VALUES
(1, '07:00:00', '10:00:00', 15, '11:00:00', NULL, '2025-09-30 02:45:46', '2025-09-30 05:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` bigint(20) UNSIGNED NOT NULL,
  `id_departemen` bigint(20) UNSIGNED NOT NULL,
  `id_posisi` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_karyawan` varchar(255) NOT NULL,
  `username_karyawan` varchar(255) NOT NULL,
  `email_karyawan` varchar(255) NOT NULL,
  `password_karyawan` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `id_departemen`, `id_posisi`, `nama_karyawan`, `username_karyawan`, `email_karyawan`, `password_karyawan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Test Employee 1', 'test1', 'test1@company.com', '$2y$12$3C5d2h/1g7tP38origcBXusUCEV7BpfnYr9GgAuvibScIBDSfpXNW', 'aktif', '2025-09-26 22:20:45', '2025-09-26 22:20:45'),
(5, 1, 1, 'Arjuna', 'aruma', 'arjuna@gmail.com', '$2y$12$kM6meYuvCNm7GJqNzkHxGOaFYOdULQDLl2THg/cD2BtlrCvZ40eC6', 'aktif', '2025-09-29 12:06:01', '2025-09-29 12:06:01'),
(6, 3, 10, 'lepe', 'tes', 'tes@gmail.com', '$2y$12$szFkzltVI5Wbkl8JqvDNZ.cLPdjxYA2Dq10MwZ/HjxdtcgjJfFPEi', 'aktif', '2025-10-01 01:44:25', '2025-10-01 01:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_kerja`
--

CREATE TABLE `lokasi_kerja` (
  `id_lokasi` bigint(20) UNSIGNED NOT NULL,
  `lokasi_kerja` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius` int(11) NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lokasi_kerja`
--

INSERT INTO `lokasi_kerja` (`id_lokasi`, `lokasi_kerja`, `created_at`, `updated_at`, `latitude`, `longitude`, `radius`) VALUES
(3, 'Ruko Abc', '2025-09-26 01:22:28', '2025-09-26 01:22:28', -5.15820155, 119.47831392, 100),
(7, 'tes', '2025-09-28 17:32:09', '2025-09-28 17:32:09', -5.16010354, 119.47642565, 100),
(8, 'y', '2025-09-30 02:56:29', '2025-09-30 02:56:29', -5.15984710, 119.47719812, 100);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_24_054028_create_admins_table', 1),
(5, '2025_09_24_054419_create_departemen_table', 1),
(6, '2025_09_24_054610_create_karyawan_table', 1),
(7, '2025_09_24_054807_create_lokasi_kerja_table', 1),
(8, '2025_09_24_055158_create_jam_kerja_table', 1),
(9, '2025_09_24_055329_create_absensi_table', 1),
(10, '2025_09_25_014614_add_email_to_karyawan_table', 1),
(11, '2025_09_25_030501_refactor_departemen_and_create_posisi_table', 1),
(12, '2025_09_25_031500_add_id_posisi_to_karyawan_table', 1),
(13, '2025_09_26_013808_add_geo_fence_to_lokasi_kerja_table', 1),
(14, '2025_09_26_060000_modify_mode_enum_in_absensi_table', 2),
(15, '2025_09_27_054507_create_personal_access_tokens_table', 3),
(16, '2025_09_28_121100_drop_mode_column_from_absensi_table', 4),
(17, '2025_09_30_095419_drop_mode_column_from_absensi_table', 5),
(18, '2025_09_30_100028_add_status_to_absensi_table', 6),
(19, '2025_09_30_115625_add_menit_keterlambatan_to_absensi_table', 7),
(20, '2025_10_01_115901_add_is_lembur_to_absensi_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Karyawan', 1, 'mobile-app', '6b5f4d0a6c2798dc1bbee1e0bc91a2fa3fdf077d2881a2dab8dc3dce2b0c989e', '[\"*\"]', NULL, NULL, '2025-09-26 21:48:13', '2025-09-26 21:48:13'),
(2, 'App\\Models\\Karyawan', 1, 'mobile-app', '09cc258afd8a15d001cd3024fbc04876f6efbaab01d0dc238eafadef93732460', '[\"*\"]', NULL, NULL, '2025-09-26 21:48:17', '2025-09-26 21:48:17'),
(3, 'App\\Models\\Karyawan', 1, 'mobile-app', 'aebff0bea9a628e3add12df561ddce6d71dcc9504c16ae57d73761dcd7d8db55', '[\"*\"]', NULL, NULL, '2025-09-26 21:49:05', '2025-09-26 21:49:05'),
(4, 'App\\Models\\Karyawan', 1, 'mobile-app', 'f1d3719f0ee03005de3bffce77ed59cfc65d5a69950125898f6e4bdee2a6d5e9', '[\"*\"]', NULL, NULL, '2025-09-26 21:50:52', '2025-09-26 21:50:52'),
(5, 'App\\Models\\Karyawan', 1, 'mobile-app', 'a6faedbc3c570c19fff055421ef0fc85629f5484bde66f8c60b4a2c48cb04488', '[\"*\"]', NULL, NULL, '2025-09-26 21:57:18', '2025-09-26 21:57:18'),
(6, 'App\\Models\\Karyawan', 5, 'mobile-app', 'ee71bc6cb5d48201e887f3bb44be90952b8f75ef8774b293e2048e362cbbe40e', '[\"*\"]', NULL, NULL, '2025-09-26 22:18:06', '2025-09-26 22:18:06'),
(7, 'App\\Models\\Karyawan', 1, 'mobile-app', 'f2097d43243159216ad8c54c94f0d75c79e47caaaced775d06d7d2b2270f46e2', '[\"*\"]', NULL, NULL, '2025-09-26 22:20:55', '2025-09-26 22:20:55'),
(9, 'App\\Models\\Karyawan', 1, 'mobile-app', 'e5460ca820bd34c27c5bc3a24f786bdab48c572703f811171ded80f0e2e795a1', '[\"*\"]', '2025-09-26 22:22:10', NULL, '2025-09-26 22:21:33', '2025-09-26 22:22:10'),
(10, 'App\\Models\\Karyawan', 1, 'mobile-app', 'c58e7413a61d04f499477a3549d6dab70d6ef91d90df76302953a4faa84c329a', '[\"*\"]', '2025-09-26 22:22:36', NULL, '2025-09-26 22:22:22', '2025-09-26 22:22:36'),
(11, 'App\\Models\\Karyawan', 1, 'mobile-app', '6cd526345c6db2ce8237129b10008146afdead15d5bb9fea77bbe01f84f11985', '[\"*\"]', '2025-09-26 22:23:30', NULL, '2025-09-26 22:23:13', '2025-09-26 22:23:30'),
(12, 'App\\Models\\Karyawan', 1, 'mobile-app', '7ab7f9906c117fbf26fe6a05a96f1ac8a401fb42ce5303ef071a21c43cc79ad7', '[\"*\"]', '2025-09-26 22:37:07', NULL, '2025-09-26 22:24:07', '2025-09-26 22:37:07'),
(13, 'App\\Models\\Karyawan', 1, 'mobile-app', 'b93cf420b02af47c425757d128ff8d2924ae0dce90fbe4ae61521834294b9e96', '[\"*\"]', NULL, NULL, '2025-09-26 22:30:54', '2025-09-26 22:30:54'),
(14, 'App\\Models\\Karyawan', 2, 'mobile-app', 'ff588edffd8dd702b278fbf5f2b40a3c9a00a5d823d2da3a6a6a4a657a63b667', '[\"*\"]', NULL, NULL, '2025-09-26 22:33:03', '2025-09-26 22:33:03'),
(15, 'App\\Models\\Karyawan', 2, 'mobile-app', '3c8d0cbe88c4b7bd9821d958dc41d632088af76acc2bf91ddfebf299018f0e8e', '[\"*\"]', NULL, NULL, '2025-09-26 22:34:47', '2025-09-26 22:34:47'),
(16, 'App\\Models\\Karyawan', 1, 'mobile-app', 'bf1a86d3f39f732e3af186070e03ef728ca735379e861109d563e25fddb21208', '[\"*\"]', NULL, NULL, '2025-09-26 22:39:11', '2025-09-26 22:39:11'),
(17, 'App\\Models\\Karyawan', 1, 'mobile-app', 'e0cfcb590a856dd907420c487324b1a8d231cec9ae1d20d815d7cd89ef22dc5e', '[\"*\"]', NULL, NULL, '2025-09-26 22:41:24', '2025-09-26 22:41:24'),
(18, 'App\\Models\\Karyawan', 1, 'mobile-app', '79f3cb5fb005593c166dafbef3fb926a15b021a258eddcbcd5174b6cb9d63196', '[\"*\"]', NULL, NULL, '2025-09-26 22:42:48', '2025-09-26 22:42:48'),
(19, 'App\\Models\\Karyawan', 1, 'mobile-app', 'b826247b029fe73e59594a3e1bfcc6daba21500c3d3cc6839a10aded9329de54', '[\"*\"]', NULL, NULL, '2025-09-26 22:47:15', '2025-09-26 22:47:15'),
(20, 'App\\Models\\Karyawan', 2, 'mobile-app', '9306ae4ec553697aabbb18e0c7de4cad1a6b5e662b638d8be00f2c07cdc55cbb', '[\"*\"]', NULL, NULL, '2025-09-26 22:48:12', '2025-09-26 22:48:12'),
(21, 'App\\Models\\Karyawan', 2, 'mobile-app', 'b4fb5af480673e4bb92e5e708266dd598eded8ab012b215eac2da902de8a787a', '[\"*\"]', NULL, NULL, '2025-09-26 22:51:01', '2025-09-26 22:51:01'),
(22, 'App\\Models\\Karyawan', 2, 'mobile-app', '0e64849c8d05421ad07a05510b650447199f5777e1940e7d07b5950ba168e988', '[\"*\"]', NULL, NULL, '2025-09-26 22:51:02', '2025-09-26 22:51:02'),
(23, 'App\\Models\\Karyawan', 2, 'mobile-app', 'edff8d3edc2181d782d1ba40cd31e24e3b55af8124ba66d16c1bb1a7e822b0e6', '[\"*\"]', NULL, NULL, '2025-09-26 22:51:45', '2025-09-26 22:51:45'),
(24, 'App\\Models\\Karyawan', 2, 'mobile-app', '22309aa866c6b3000d315157269c5afa96592dd78b796bd42a74b1d1e3028731', '[\"*\"]', NULL, NULL, '2025-09-26 22:52:11', '2025-09-26 22:52:11'),
(25, 'App\\Models\\Karyawan', 2, 'mobile-app', 'a72688670086f179ff742bcaee09299ff7ee036634abc44f780702af8fb4cad1', '[\"*\"]', NULL, NULL, '2025-09-26 22:52:13', '2025-09-26 22:52:13'),
(26, 'App\\Models\\Karyawan', 1, 'mobile-app', '55ae42fa801a6ac213cd4dcc0ca30a04a20db572beee6f1f09cd269c6519190a', '[\"*\"]', NULL, NULL, '2025-09-26 22:52:30', '2025-09-26 22:52:30'),
(27, 'App\\Models\\Karyawan', 1, 'mobile-app', '001ac7198167df476ca4de2d9078b82e4b15d84747f2378b3960bafd9ed92688', '[\"*\"]', NULL, NULL, '2025-09-26 22:52:33', '2025-09-26 22:52:33'),
(28, 'App\\Models\\Karyawan', 1, 'mobile-app', '2cf6be805e58ac79b607a9fb4d9b7e9a3b6a8cef6474fc59f75bae01a0ca01c1', '[\"*\"]', NULL, NULL, '2025-09-26 22:53:33', '2025-09-26 22:53:33'),
(29, 'App\\Models\\Karyawan', 1, 'mobile-app', '87575dae7053b1bf738b94250ebdb8fc0c108035dfd4514f1cb80b08d05f9925', '[\"*\"]', NULL, NULL, '2025-09-26 22:56:19', '2025-09-26 22:56:19'),
(30, 'App\\Models\\Karyawan', 3, 'mobile-app', '5578c747b14765a71aee070196f055f583aebd63a97ae4820404ef47c808e2bb', '[\"*\"]', NULL, NULL, '2025-09-26 22:59:37', '2025-09-26 22:59:37'),
(31, 'App\\Models\\Karyawan', 3, 'mobile-app', '202fb4256710f54b5aa291c86458f2bd84c8180e0767d1bb5a467d136be1efab', '[\"*\"]', NULL, NULL, '2025-09-26 22:59:47', '2025-09-26 22:59:47'),
(32, 'App\\Models\\Karyawan', 3, 'mobile-app', '48f215310a848264c0120f8799dd3a039a81da7fa21648dacaa4dbd36b386274', '[\"*\"]', NULL, NULL, '2025-09-26 23:01:11', '2025-09-26 23:01:11'),
(33, 'App\\Models\\Karyawan', 3, 'mobile-app', '21a3d22b4de218d53b213c4744609241a55780bd8d365bdc652aa32a07798732', '[\"*\"]', NULL, NULL, '2025-09-26 23:01:19', '2025-09-26 23:01:19'),
(34, 'App\\Models\\Karyawan', 3, 'mobile-app', '5748c05a237571668c79651e655d132b8408095ca05fa2b9ca00c26aa5e01877', '[\"*\"]', NULL, NULL, '2025-09-26 23:02:34', '2025-09-26 23:02:34'),
(35, 'App\\Models\\Karyawan', 3, 'mobile-app', '9c32b0354b94eb7552e001ac33da5980ea149d10ca3d28e6b88be6f5e1e7e90d', '[\"*\"]', NULL, NULL, '2025-09-26 23:05:40', '2025-09-26 23:05:40'),
(36, 'App\\Models\\Karyawan', 3, 'mobile-app', '0899415969f9ab66fb8005a49a4e4eaf1e0844639f01c8943c9c3a8e6c98bec7', '[\"*\"]', NULL, NULL, '2025-09-26 23:05:40', '2025-09-26 23:05:40'),
(37, 'App\\Models\\Karyawan', 1, 'mobile-app', '15c3503a367ed8c381c9ee628ef352ae190427331c5e778be42653a5e4c15fd6', '[\"*\"]', NULL, NULL, '2025-09-26 23:06:18', '2025-09-26 23:06:18'),
(38, 'App\\Models\\Karyawan', 1, 'mobile-app', 'b26f40084baacb69007cc22e5316af40b79e046365ef4cef60231553abdc46eb', '[\"*\"]', NULL, NULL, '2025-09-26 23:06:55', '2025-09-26 23:06:55'),
(39, 'App\\Models\\Karyawan', 1, 'mobile-app', '9976f5db57210d1e5c90f9e79f8aae5f3dd4f881e0d540bf5ca713ebf705f3d0', '[\"*\"]', NULL, NULL, '2025-09-28 04:44:01', '2025-09-28 04:44:01'),
(40, 'App\\Models\\Karyawan', 1, 'mobile-app', '6ec39e3a2530b1ca547d7884de256d824a1648b46cbddce99c3dc63c3cb73278', '[\"*\"]', '2025-09-28 04:47:24', NULL, '2025-09-28 04:44:13', '2025-09-28 04:47:24'),
(41, 'App\\Models\\Karyawan', 1, 'mobile-app', '1bf079c50fb94e7cbf71e9aeedec3b70346b4df1916dea68a00158cc1a54207c', '[\"*\"]', NULL, NULL, '2025-09-28 04:46:45', '2025-09-28 04:46:45'),
(42, 'App\\Models\\Karyawan', 1, 'mobile-app', 'dbee00d475d403c856fff204cdc3618b715083b91162da25cee9b8efd4850a0f', '[\"*\"]', NULL, NULL, '2025-09-28 04:51:29', '2025-09-28 04:51:29'),
(43, 'App\\Models\\Karyawan', 1, 'mobile-app', '680f91d6d482c0aaf78b48d6a3b925d595d38d8375edc62bd49b6c734aedbc4d', '[\"*\"]', '2025-09-28 04:58:36', NULL, '2025-09-28 04:57:23', '2025-09-28 04:58:36'),
(44, 'App\\Models\\Karyawan', 1, 'mobile-app', '9b43f148d9e3497719791ac4676c6070cd495ae5a52d66fe8982627b19febd10', '[\"*\"]', '2025-09-28 04:59:04', NULL, '2025-09-28 04:58:54', '2025-09-28 04:59:04'),
(47, 'App\\Models\\Karyawan', 1, 'mobile-app', 'aabdfe6c535f4eff354d9cbf8a0cd5dcd8ade387b71dd2610a1393a7c929f2aa', '[\"*\"]', '2025-09-28 05:05:55', NULL, '2025-09-28 05:05:54', '2025-09-28 05:05:55'),
(50, 'App\\Models\\Karyawan', 2, 'mobile-app', 'd9ed54cd7ec7432ff526e4827b4388ea00cf9a826e5f0c4b556132552590270d', '[\"*\"]', '2025-09-28 05:08:33', NULL, '2025-09-28 05:08:20', '2025-09-28 05:08:33'),
(51, 'App\\Models\\Karyawan', 1, 'mobile-app', 'beb01b54b4e7685b4aecad890dca61adce09c8a6715053c1da91f4e41aae42cb', '[\"*\"]', '2025-09-28 05:21:26', NULL, '2025-09-28 05:17:36', '2025-09-28 05:21:26'),
(52, 'App\\Models\\Karyawan', 1, 'mobile-app', '866cc233fcb1fb620bdc8bc5e9348c59945b5bcca09f249a0f433f266bb2cd28', '[\"*\"]', '2025-09-28 05:32:34', NULL, '2025-09-28 05:32:18', '2025-09-28 05:32:34'),
(53, 'App\\Models\\Karyawan', 2, 'mobile-app', '723c6e9864c35922d6ce3e444e383492df9b812c75aad2a685937ca90ff92c20', '[\"*\"]', '2025-09-28 05:34:05', NULL, '2025-09-28 05:33:04', '2025-09-28 05:34:05'),
(54, 'App\\Models\\Karyawan', 1, 'mobile-app', 'c77f7e973bd24ac82cb06d85e768ce4c00765db76ee62345c6977f19652e3071', '[\"*\"]', '2025-09-28 17:34:17', NULL, '2025-09-28 17:30:13', '2025-09-28 17:34:17'),
(55, 'App\\Models\\Karyawan', 1, 'mobile-app', 'd733c5efb62d7184a7ead94782c4f062fa4aeec535bd0a2a22bf1a4d124cc6cb', '[\"*\"]', '2025-09-28 17:54:02', NULL, '2025-09-28 17:53:46', '2025-09-28 17:54:02'),
(56, 'App\\Models\\Karyawan', 1, 'mobile-app', '57a027695e3d2f90e2b8764e49f2d208c08d65233769ba7dcab1bb81579bb441', '[\"*\"]', '2025-09-28 18:00:13', NULL, '2025-09-28 18:00:04', '2025-09-28 18:00:13'),
(57, 'App\\Models\\Karyawan', 1, 'mobile-app', '1181d42b28e110b94c979286001a5382b368cc15fadb4021a4fce72a8a62417e', '[\"*\"]', '2025-09-28 18:06:10', NULL, '2025-09-28 18:05:34', '2025-09-28 18:06:10'),
(59, 'App\\Models\\Karyawan', 1, 'mobile-app', '420dc545c8419863ca3af753fe9d655fce02ea5a5de44883d60805eed2da7cf6', '[\"*\"]', '2025-09-28 18:12:01', NULL, '2025-09-28 18:12:01', '2025-09-28 18:12:01'),
(60, 'App\\Models\\Karyawan', 1, 'mobile-app', '3a31d996a5e9fc60606a4fb0a62a977342a3589bf4df84a1468232e77c1deb80', '[\"*\"]', NULL, NULL, '2025-09-28 18:44:09', '2025-09-28 18:44:09'),
(61, 'App\\Models\\Karyawan', 1, 'mobile-app', 'be65546785cb8704d206d12243ff691295d317df74d3554670241c77fe625fef', '[\"*\"]', NULL, NULL, '2025-09-28 18:45:04', '2025-09-28 18:45:04'),
(62, 'App\\Models\\Karyawan', 1, 'mobile-app', 'b350acff1a4e2047b37cf14bdab2744136e673fde9fa3acf0e5e4cad026c6795', '[\"*\"]', NULL, NULL, '2025-09-28 18:59:22', '2025-09-28 18:59:22'),
(63, 'App\\Models\\Karyawan', 1, 'mobile-app', '3c8bf6ff17c2a44bd17f96a088b1d8131dc6facdbebf28a048a0a5ae5fef1fbe', '[\"*\"]', NULL, NULL, '2025-09-28 18:59:55', '2025-09-28 18:59:55'),
(64, 'App\\Models\\Karyawan', 1, 'mobile-app', '263d4dca0f230afb187f674690e36077a8c2cc617be93e9fe452f0ccf3f9701e', '[\"*\"]', '2025-09-28 19:06:52', NULL, '2025-09-28 19:05:06', '2025-09-28 19:06:52'),
(65, 'App\\Models\\Karyawan', 2, 'mobile-app', '0eb356506cf6367b453d4abe166507894498df079f760420b07772cd364fb93a', '[\"*\"]', '2025-09-29 03:14:42', NULL, '2025-09-29 03:14:30', '2025-09-29 03:14:42'),
(66, 'App\\Models\\Karyawan', 1, 'mobile-app', 'db8f06b743dd6152ff301201b2796076f18e8b66834fbb07938fb3ee2085cf6f', '[\"*\"]', '2025-09-29 03:30:43', NULL, '2025-09-29 03:20:45', '2025-09-29 03:30:43'),
(67, 'App\\Models\\Karyawan', 1, 'mobile-app', 'd9fd03130715b52cb014dfcd5554e5fbe315112f94d0d3cb3ff11767283c9da3', '[\"*\"]', NULL, NULL, '2025-09-30 01:44:04', '2025-09-30 01:44:04'),
(68, 'App\\Models\\Karyawan', 5, 'mobile-app', 'f6cadc9e2fe0c42c6ade771aaddf97ec22a93577c573f6ddfe3caa783fdec446', '[\"*\"]', NULL, NULL, '2025-09-30 01:46:00', '2025-09-30 01:46:00'),
(69, 'App\\Models\\Karyawan', 5, 'mobile-app', '33ff7e5f82c3146e2b038acd276458eb9b22dded3ab703270c74b756ca3a8648', '[\"*\"]', '2025-09-30 01:47:34', NULL, '2025-09-30 01:46:24', '2025-09-30 01:47:34'),
(71, 'App\\Models\\Karyawan', 1, 'mobile-app', '98232ee07db93fe6e0bba2f5ac31731753ba8d43d31d16f3e9afee10b3749306', '[\"*\"]', '2025-09-30 02:08:42', NULL, '2025-09-30 02:08:42', '2025-09-30 02:08:42'),
(72, 'App\\Models\\Karyawan', 1, 'mobile-app', 'a72217c706fdfc8f3c7b7ada7545a0a05f6dd919337691c9a2ea04eee009f17d', '[\"*\"]', NULL, NULL, '2025-09-30 02:18:15', '2025-09-30 02:18:15'),
(74, 'App\\Models\\Karyawan', 1, 'mobile-app', '5b79cbcb2de2af92eed7a91cbc578f44998f424771e5c0c2967d58ad8ae8ea82', '[\"*\"]', '2025-09-30 02:23:52', NULL, '2025-09-30 02:19:27', '2025-09-30 02:23:52'),
(75, 'App\\Models\\Karyawan', 5, 'mobile-app', '1e31bccd8686c54e9d73fe48bbf16d4d65080e96e7a655e90ee4bf7b816ad227', '[\"*\"]', '2025-09-30 03:02:33', NULL, '2025-09-30 02:55:20', '2025-09-30 03:02:33'),
(76, 'App\\Models\\Karyawan', 5, 'mobile-app', 'b0fa58088ab3afbfa5416d1b4c8b0ccc39094ed733e46e430745d7a96809475c', '[\"*\"]', '2025-09-30 03:07:55', NULL, '2025-09-30 03:07:44', '2025-09-30 03:07:55'),
(77, 'App\\Models\\Karyawan', 5, 'mobile-app', '14dbf993e7dfcc889554045b1c6d48a4e9f19c8094cc08577d2b7869f9704d6b', '[\"*\"]', '2025-09-30 03:18:37', NULL, '2025-09-30 03:18:22', '2025-09-30 03:18:37'),
(78, 'App\\Models\\Karyawan', 5, 'mobile-app', '79563031d9dfec573323166d977003d20816a7e351773c573bf3879f6a0738a5', '[\"*\"]', '2025-09-30 03:27:31', NULL, '2025-09-30 03:23:11', '2025-09-30 03:27:31'),
(79, 'App\\Models\\Karyawan', 5, 'mobile-app', '7fe2c63770038477ae7a82e013f8468ab9e1a314390961e6903e5d3d8861e8b2', '[\"*\"]', '2025-09-30 03:30:55', NULL, '2025-09-30 03:30:45', '2025-09-30 03:30:55'),
(80, 'App\\Models\\Karyawan', 5, 'mobile-app', '63ae85f576434ccd443f0e4f8218ef7a8c98f8c321750d161186480633ff374f', '[\"*\"]', '2025-09-30 03:37:44', NULL, '2025-09-30 03:37:33', '2025-09-30 03:37:44'),
(81, 'App\\Models\\Karyawan', 1, 'mobile-app', 'dde7af0d9d59aadafb811478e6528358b7a4e019f6b477e9be0ecaa9ddcf6036', '[\"*\"]', '2025-09-30 04:54:53', NULL, '2025-09-30 03:51:34', '2025-09-30 04:54:53'),
(82, 'App\\Models\\Karyawan', 5, 'mobile-app', '66fb7ecef4c7fe6cfcbcebf4bc22a72a1f9663f3a6d5606542244c6f8182e473', '[\"*\"]', '2025-09-30 05:02:09', NULL, '2025-09-30 05:01:44', '2025-09-30 05:02:09'),
(83, 'App\\Models\\Karyawan', 1, 'mobile-app', 'e5b7921b7dc61a475c080085c68a416abd3b058b7b1677033d956bcc505eaadf', '[\"*\"]', NULL, NULL, '2025-09-30 05:22:04', '2025-09-30 05:22:04'),
(84, 'App\\Models\\Karyawan', 1, 'mobile-app', '750ac0113985134f97148abffda7ab2fc3e8b736aba44712ecb22246a36303a0', '[\"*\"]', '2025-09-30 05:37:16', NULL, '2025-09-30 05:37:03', '2025-09-30 05:37:16'),
(85, 'App\\Models\\Karyawan', 1, 'mobile-app', 'bedc61fc598d9811da352bd4b2bcf0fdfc555f28a3a2062209db20511ec35e13', '[\"*\"]', '2025-09-30 05:51:10', NULL, '2025-09-30 05:51:03', '2025-09-30 05:51:10'),
(86, 'App\\Models\\Karyawan', 1, 'mobile-app', '37fd446f09516da949414a251e2fe176dca59ec348ac5420ead5745f963ad53e', '[\"*\"]', '2025-09-30 05:51:45', NULL, '2025-09-30 05:51:32', '2025-09-30 05:51:45'),
(87, 'App\\Models\\Karyawan', 5, 'mobile-app', 'aa0d75dcb16a3f729ca6df22e4c4eea0b64c28d2af72eb30eb05e4c5cb8038b9', '[\"*\"]', NULL, NULL, '2025-09-30 11:20:14', '2025-09-30 11:20:14'),
(88, 'App\\Models\\Karyawan', 5, 'mobile-app', 'c25118d7502b93199ef3cb84930d5d3e1d68a579c35c50b9306d14134d04e25f', '[\"*\"]', NULL, NULL, '2025-09-30 11:20:16', '2025-09-30 11:20:16'),
(89, 'App\\Models\\Karyawan', 5, 'mobile-app', '43c135dbd8daced28573ad082efc25edefeba1729e5b380b1e941f97952338d4', '[\"*\"]', '2025-09-30 11:21:20', NULL, '2025-09-30 11:20:23', '2025-09-30 11:21:20'),
(90, 'App\\Models\\Karyawan', 5, 'mobile-app', 'fdf032c9b3286435e1e6a5e3ea49c3bad97d7940bfebe952ade02b279e988098', '[\"*\"]', '2025-09-30 11:24:17', NULL, '2025-09-30 11:21:43', '2025-09-30 11:24:17'),
(91, 'App\\Models\\Karyawan', 1, 'mobile-app', '28dbc393f696532f58dbf8c0a07aff0555451e32e7060ce676ac62f7666f1ace', '[\"*\"]', '2025-10-01 02:07:21', NULL, '2025-09-30 11:24:32', '2025-10-01 02:07:21'),
(92, 'App\\Models\\Karyawan', 5, 'mobile-app', '31f02d350c32f525dee3486e7d659dab0a9bc61741b6b9891aeb97f079044d7b', '[\"*\"]', '2025-10-01 02:53:04', NULL, '2025-10-01 02:17:03', '2025-10-01 02:53:04'),
(93, 'App\\Models\\Karyawan', 5, 'mobile-app', '6ba0a6199f305ee65914795447a4b7013c6006136245640cd69a3e3de832e32c', '[\"*\"]', '2025-10-01 10:37:20', NULL, '2025-10-01 03:23:10', '2025-10-01 10:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `posisi`
--

CREATE TABLE `posisi` (
  `id_posisi` bigint(20) UNSIGNED NOT NULL,
  `nama_posisi` varchar(255) NOT NULL,
  `id_departemen` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posisi`
--

INSERT INTO `posisi` (`id_posisi`, `nama_posisi`, `id_departemen`, `created_at`, `updated_at`) VALUES
(1, 'Programmer', 1, '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(2, 'System Analyst', 1, '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(3, 'Network Administrator', 2, '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(4, 'Network Engineer', 2, '2025-09-26 00:01:57', '2025-09-26 00:01:57'),
(5, 'Frontend', 3, '2025-10-01 01:40:13', '2025-10-01 01:40:13'),
(6, 'tes', 1, '2025-10-01 01:40:57', '2025-10-01 01:40:57'),
(7, 'tes1', 1, '2025-10-01 01:41:12', '2025-10-01 01:41:12'),
(8, 'tes', 2, '2025-10-01 01:41:18', '2025-10-01 01:41:18'),
(9, 'tes1', 2, '2025-10-01 01:41:30', '2025-10-01 01:41:30'),
(10, 'tes1', 3, '2025-10-01 01:41:38', '2025-10-01 01:41:38'),
(11, 'tess', 3, '2025-10-01 01:41:50', '2025-10-01 01:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('uzZwx5Wppq4rIXYZDDceSv29atj0ZLN2K4GnNERn', NULL, '192.168.136.144', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieVhjU1lubXppZExqemtQSXEyZHNMTE5wYnJPdDJYRUNyNnlaUTRYYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xOTIuMTY4LjEzNi4xNDQ6ODAwMC9sb2dpbiI7fX0=', 1759300731);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '2025-09-26 00:01:57', '$2y$12$Y2Y1ehBKWWi0X/58f5Z1YuiU0OGLvxLXjCdsLtmL8B5ITuR2MzuVu', 'lEIH6wu4vD', '2025-09-26 00:01:57', '2025-09-26 00:01:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `absensi_id_karyawan_foreign` (`id_karyawan`),
  ADD KEY `absensi_id_jamkerja_foreign` (`id_jamKerja`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jam_kerja`
--
ALTER TABLE `jam_kerja`
  ADD PRIMARY KEY (`id_jamKerja`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `karyawan_username_karyawan_unique` (`username_karyawan`),
  ADD UNIQUE KEY `karyawan_email_karyawan_unique` (`email_karyawan`),
  ADD KEY `karyawan_id_departemen_foreign` (`id_departemen`),
  ADD KEY `karyawan_id_posisi_foreign` (`id_posisi`);

--
-- Indexes for table `lokasi_kerja`
--
ALTER TABLE `lokasi_kerja`
  ADD PRIMARY KEY (`id_lokasi`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `posisi`
--
ALTER TABLE `posisi`
  ADD PRIMARY KEY (`id_posisi`),
  ADD KEY `posisi_id_departemen_foreign` (`id_departemen`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id_departemen` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jam_kerja`
--
ALTER TABLE `jam_kerja`
  MODIFY `id_jamKerja` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lokasi_kerja`
--
ALTER TABLE `lokasi_kerja`
  MODIFY `id_lokasi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `posisi`
--
ALTER TABLE `posisi`
  MODIFY `id_posisi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_id_jamkerja_foreign` FOREIGN KEY (`id_jamKerja`) REFERENCES `jam_kerja` (`id_jamKerja`) ON DELETE SET NULL,
  ADD CONSTRAINT `absensi_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE CASCADE,
  ADD CONSTRAINT `karyawan_id_posisi_foreign` FOREIGN KEY (`id_posisi`) REFERENCES `posisi` (`id_posisi`) ON DELETE SET NULL;

--
-- Constraints for table `posisi`
--
ALTER TABLE `posisi`
  ADD CONSTRAINT `posisi_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
