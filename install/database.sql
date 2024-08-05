-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2024 at 09:12 PM
-- Server version: 10.3.39-MariaDB-log-cll-lve
-- PHP Version: 8.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nemosofts_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_active_log`
--

CREATE TABLE `tbl_active_log` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `date_time` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `admin_type` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`, `email`, `image`, `status`, `admin_type`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'info.nemosofts@gmail.com', '57317_profile.png', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_banner`
--

CREATE TABLE `tbl_banner` (
  `bid` int(11) NOT NULL,
  `banner_title` varchar(255) NOT NULL,
  `banner_info` varchar(500) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `banner_post_id` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `cid` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favourite`
--

CREATE TABLE `tbl_favourite` (
  `id` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification`
--

CREATE TABLE `tbl_notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `notification_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `notification_on` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating`
--

CREATE TABLE `tbl_rating` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `device_id` varchar(40) NOT NULL,
  `rate` int(11) NOT NULL,
  `message` text NOT NULL,
  `dt_rate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reports`
--

CREATE TABLE `tbl_reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_msg` text NOT NULL,
  `report_on` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ringtone`
--

CREATE TABLE `tbl_ringtone` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ringtone_title` varchar(255) NOT NULL,
  `ringtone_url` text NOT NULL,
  `audio_type` varchar(255) NOT NULL,
  `rate_avg` int(11) NOT NULL DEFAULT 0,
  `total_rate` int(11) NOT NULL DEFAULT 0,
  `total_views` int(11) NOT NULL DEFAULT 0,
  `total_download` int(11) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ringtone_views`
--

CREATE TABLE `tbl_ringtone_views` (
  `view_id` bigint(20) NOT NULL,
  `ringtone_id` bigint(20) NOT NULL,
  `views` bigint(20) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `app_name` varchar(100) NOT NULL,
  `app_logo` varchar(200) NOT NULL,
  `app_email` varchar(100) NOT NULL,
  `app_author` varchar(100) NOT NULL,
  `app_contact` varchar(100) NOT NULL,
  `app_website` varchar(150) NOT NULL,
  `app_description` text NOT NULL,
  `app_developed_by` varchar(150) NOT NULL,
  `app_privacy_policy` text NOT NULL,
  `app_terms` text NOT NULL,
  `account_delete_intruction` text NOT NULL,
  `envato_buyer_name` varchar(100) NOT NULL,
  `envato_purchase_code` text NOT NULL,
  `envato_package_name` varchar(150) NOT NULL,
  `envato_api_key` text NOT NULL,
  `onesignal_app_id` text NOT NULL,
  `onesignal_rest_key` text NOT NULL,
  `home_limit` int(2) NOT NULL DEFAULT 10,
  `api_latest_limit` int(2) NOT NULL DEFAULT 10,
  `api_cat_order_by` varchar(150) NOT NULL DEFAULT 'category_name',
  `api_cat_post_order_by` varchar(150) NOT NULL DEFAULT 'DESC',
  `app_api_key` varchar(150) NOT NULL,
  `isRTL` varchar(10) NOT NULL DEFAULT 'false',
  `isMaintenance` varchar(10) NOT NULL DEFAULT 'false',
  `isScreenshot` varchar(10) NOT NULL DEFAULT 'false',
  `isGoogleLogin` varchar(10) NOT NULL DEFAULT 'true',
  `isLogin` varchar(10) NOT NULL DEFAULT 'true',
  `isAPK` varchar(10) NOT NULL DEFAULT 'false',
  `isVPN` varchar(10) NOT NULL DEFAULT 'false',
  `isDummy_1` varchar(10) NOT NULL DEFAULT 'false',
  `isDummy_2` varchar(10) NOT NULL DEFAULT 'false',
  `dummy_test_1` varchar(150) NOT NULL,
  `dummy_test_2` varchar(150) NOT NULL,
  `app_update_status` varchar(10) NOT NULL DEFAULT 'false',
  `app_new_version` double NOT NULL DEFAULT 1,
  `app_update_desc` text NOT NULL,
  `app_redirect_url` text NOT NULL,
  `more_apps_url` text NOT NULL,
  `ad_status` varchar(10) NOT NULL DEFAULT 'false',
  `ad_network` varchar(30) NOT NULL DEFAULT 'admob',
  `admob_publisher_id` text NOT NULL,
  `admob_banner_unit_id` text NOT NULL,
  `admob_interstitial_unit_id` text NOT NULL,
  `admob_native_unit_id` text NOT NULL,
  `admob_app_open_ad_unit_id` text NOT NULL,
  `startapp_app_id` text NOT NULL,
  `unity_game_id` text NOT NULL,
  `unity_banner_placement_id` text NOT NULL,
  `unity_interstitial_placement_id` text NOT NULL,
  `applovin_banner_ad_unit_id` text NOT NULL,
  `applovin_interstitial_ad_unit_id` text NOT NULL,
  `applovin_native_ad_manual_unit_id` text NOT NULL,
  `applovin_app_open_ad_unit_id` text NOT NULL,
  `ironsource_app_key` text NOT NULL,
  `mata_banner_ad_unit_id` text NOT NULL,
  `mata_interstitial_ad_unit_id` text NOT NULL,
  `mata_native_ad_manual_unit_id` text NOT NULL,
  `yandex_banner_ad_unit_id` text NOT NULL,
  `yandex_interstitial_ad_unit_id` text NOT NULL,
  `yandex_native_ad_manual_unit_id` text NOT NULL,
  `yandex_app_open_ad_unit_id` text NOT NULL,
  `wortise_app_id` text NOT NULL,
  `wortise_banner_unit_id` text NOT NULL,
  `wortise_interstitial_unit_id` text NOT NULL,
  `wortise_native_unit_id` text NOT NULL,
  `wortise_app_open_unit_id` text NOT NULL,
  `app_open_ad_on_start` varchar(10) NOT NULL DEFAULT 'false',
  `app_open_ad_on_Resume` varchar(10) NOT NULL DEFAULT 'false',
  `banner_home` varchar(10) NOT NULL DEFAULT 'false',
  `banner_post_details` varchar(10) NOT NULL DEFAULT 'false',
  `banner_category_details` varchar(10) NOT NULL DEFAULT 'false',
  `banner_search` varchar(10) NOT NULL DEFAULT 'false',
  `interstitial_post_list` varchar(10) NOT NULL DEFAULT 'false',
  `native_ad_post_list` varchar(10) NOT NULL DEFAULT 'false',
  `native_ad_category_list` varchar(10) NOT NULL DEFAULT 'false',
  `interstital_ad_click` int(10) NOT NULL DEFAULT 5,
  `native_position` int(10) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `app_name`, `app_logo`, `app_email`, `app_author`, `app_contact`, `app_website`, `app_description`, `app_developed_by`, `app_privacy_policy`, `app_terms`, `account_delete_intruction`, `envato_buyer_name`, `envato_purchase_code`, `envato_package_name`, `envato_api_key`, `onesignal_app_id`, `onesignal_rest_key`, `home_limit`, `api_latest_limit`, `api_cat_order_by`, `api_cat_post_order_by`, `app_api_key`, `isRTL`, `isMaintenance`, `isScreenshot`, `isGoogleLogin`, `isLogin`, `isAPK`, `isVPN`, `isDummy_1`, `isDummy_2`, `dummy_test_1`, `dummy_test_2`, `app_update_status`, `app_new_version`, `app_update_desc`, `app_redirect_url`, `more_apps_url`, `ad_status`, `ad_network`, `admob_publisher_id`, `admob_banner_unit_id`, `admob_interstitial_unit_id`, `admob_native_unit_id`, `admob_app_open_ad_unit_id`, `startapp_app_id`, `unity_game_id`, `unity_banner_placement_id`, `unity_interstitial_placement_id`, `applovin_banner_ad_unit_id`, `applovin_interstitial_ad_unit_id`, `applovin_native_ad_manual_unit_id`, `applovin_app_open_ad_unit_id`, `ironsource_app_key`, `mata_banner_ad_unit_id`, `mata_interstitial_ad_unit_id`, `mata_native_ad_manual_unit_id`, `yandex_banner_ad_unit_id`, `yandex_interstitial_ad_unit_id`, `yandex_native_ad_manual_unit_id`, `yandex_app_open_ad_unit_id`, `wortise_app_id`, `wortise_banner_unit_id`, `wortise_interstitial_unit_id`, `wortise_native_unit_id`, `wortise_app_open_unit_id`, `app_open_ad_on_start`, `app_open_ad_on_Resume`, `banner_home`, `banner_post_details`, `banner_category_details`, `banner_search`, `interstitial_post_list`, `native_ad_post_list`, `native_ad_category_list`, `interstital_ad_click`, `native_position`) VALUES
(1, 'Online Ringtone', '43133_logo.png', 'thiva.nemosofts@gmail.com', 'nemosofts', '0356525684', 'nemosofts.com', 'Love this app? Let us Know in the Google Play Store how we can make it even better', 'thivakaran', '', '', '', '', '', '', '', 'a78813a9-b792-42f4-bbd7-4fc4950f2f7e', 'ZTAyMzA3NzEtYTJmNC00N2VjLWJlODktYzk0Y2M3OGFiZjQx', 10, 50, 'category_name', 'DESC', '', 'false', 'false', 'false', 'true', 'true', 'false', 'false', 'false', 'false', '', '', 'false', 1, 'update note', 'https://play.google.com/store/apps/details', 'https://codecanyon.net/category/mobile/android?sort=date&term=nemosofts#content', 'true', 'admob', 'ca-app-pub-3940256099942544', 'ca-app-pub-3940256099942544/6300978111', 'ca-app-pub-3940256099942544/1033173712', 'ca-app-pub-3940256099942544/2247696110', 'ca-app-pub-3940256099942544/9257395921', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'banner', 'video', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', 'YOUR_PLACEMENT_ID', '00000000-0000-0000-0000-123456789abc', 'test-banner', 'test-interstitial', 'test-native', 'test-app-open', 'true', 'false', 'true', 'true', 'true', 'true', 'true', 'true', 'true', 10, 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp_settings`
--

CREATE TABLE `tbl_smtp_settings` (
  `id` int(5) NOT NULL,
  `smtp_type` varchar(20) NOT NULL DEFAULT 'server',
  `smtp_host` varchar(150) NOT NULL,
  `smtp_email` varchar(150) NOT NULL,
  `smtp_password` text NOT NULL,
  `smtp_secure` varchar(20) NOT NULL,
  `port_no` varchar(10) NOT NULL,
  `smtp_ghost` varchar(150) NOT NULL,
  `smtp_gemail` varchar(150) NOT NULL,
  `smtp_gpassword` text NOT NULL,
  `smtp_gsecure` varchar(20) NOT NULL,
  `gport_no` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_smtp_settings`
--

INSERT INTO `tbl_smtp_settings` (`id`, `smtp_type`, `smtp_host`, `smtp_email`, `smtp_password`, `smtp_secure`, `port_no`, `smtp_ghost`, `smtp_gemail`, `smtp_gpassword`, `smtp_gsecure`, `gport_no`) VALUES
(1, 'gmail', '', '', '', 'ssl', '465', '', '', '', 'tls', 587);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(10) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'Normal',
  `user_name` varchar(60) NOT NULL,
  `user_email` varchar(70) NOT NULL,
  `user_password` text NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `user_gender` varchar(255) NOT NULL,
  `profile_img` varchar(255) NOT NULL DEFAULT '0',
  `auth_id` varchar(255) NOT NULL DEFAULT '0',
  `registered_on` varchar(200) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_active_log`
--
ALTER TABLE `tbl_active_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `tbl_favourite`
--
ALTER TABLE `tbl_favourite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating`
--
ALTER TABLE `tbl_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reports`
--
ALTER TABLE `tbl_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_ringtone`
--
ALTER TABLE `tbl_ringtone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_ringtone_views`
--
ALTER TABLE `tbl_ringtone_views`
  ADD PRIMARY KEY (`view_id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_active_log`
--
ALTER TABLE `tbl_active_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_favourite`
--
ALTER TABLE `tbl_favourite`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rating`
--
ALTER TABLE `tbl_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_reports`
--
ALTER TABLE `tbl_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_ringtone`
--
ALTER TABLE `tbl_ringtone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_ringtone_views`
--
ALTER TABLE `tbl_ringtone_views`
  MODIFY `view_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;