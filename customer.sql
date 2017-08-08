-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-08-08 04:27:38
-- 服务器版本： 5.7.18
-- PHP Version: 7.0.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `customer`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `auth_key` varchar(64) DEFAULT NULL,
  `password` varchar(64) NOT NULL DEFAULT '',
  `account` text,
  `nickname` text,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `remark` text,
  `login_ip` varchar(64) NOT NULL DEFAULT '',
  `create_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_at` int(11) DEFAULT '0',
  `update_at` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `auth_key`, `password`, `account`, `nickname`, `role_id`, `status`, `remark`, `login_ip`, `create_id`, `update_id`, `create_at`, `update_at`) VALUES
(1, '7xRfIO2V7q2HT9KWQhGz_PwBM8hw5Nlf', '$2y$13$v3XDsSgnyzeo5xAU2fd4k.eixOVhf/jWBRHPNus3.8OSu.mITSMXu', 'Vt67B9CTlxyRq51s5y0N5TRhYjljYTNhZmMzOTU2NTk5NTIzNGYxOWU2ZjgwZTQ2MDQ4ZDY0NmYyOTYyNTEwMTdkNThkNzRhNzgxMDRjZTCEmHmH4RMkRT0Xk5Y0Rbv0KzaMaoJxRyL69JVLpskLTQ==', 'test1234', 1, 0, NULL, '', 5, 5, 1502097331, 1502097331),
(2, 'rDsmVpokHMaM8GIKvvPThQGAMnZs3SOa', '$2y$13$v3XDsSgnyzeo5xAU2fd4k.eixOVhf/jWBRHPNus3.8OSu.mITSMXu', 'pyruYL33F6x52+LUYdYGnDMzODc3YjU3MjMyOGNiNmQ1N2EwOThlYTE0NjkzNDIyMGFiZWI3M2ZkOWZhZDI2MmIyYTU2ZTQ2OTVmNGExZjWktOubGDcw3/W1NZKnKnpYX3H6DVS7bER3/xdpGgxsKQ==', 'sdf', 2, 0, '<p>sdf</p>', '127.0.0.1', 0, 5, 1502096312, 1502101469);

-- --------------------------------------------------------

--
-- 表的结构 `agency`
--

CREATE TABLE `agency` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` char(32) NOT NULL DEFAULT '' COMMENT '单位名称',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级机构的id',
  `time` int(11) NOT NULL COMMENT '创建时间',
  `code` char(32) NOT NULL DEFAULT '' COMMENT '编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('1', '1', 1502090251),
('1', '2', 1502090311),
('1', '3', 1502095726),
('1', '4', 1502095938),
('1', '6', 1502097332),
('1', '7', 1502099562),
('1', '8', 1502099695),
('2', '5', 1502096313);

-- --------------------------------------------------------

--
-- 表的结构 `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('1', 1, '角色编号-1', NULL, NULL, 1502089385, 1502089385),
('2', 1, '角色编号-2', NULL, NULL, 1502100641, 1502100641),
('admin/create', 2, 'permission:admin/create', NULL, NULL, NULL, NULL),
('admin/delete', 2, 'permission:admin/delete', NULL, NULL, NULL, NULL),
('admin/index', 2, 'permission: admin/index', NULL, NULL, NULL, NULL),
('admin/login-logs', 2, 'permission:admin/login-logs', NULL, NULL, NULL, NULL),
('admin/recover', 2, 'permission:admin/recover', NULL, NULL, NULL, NULL),
('admin/trash', 2, 'permission:admin/trash', NULL, NULL, NULL, NULL),
('admin/update', 2, 'permission:admin/update', NULL, NULL, NULL, NULL),
('default/index', 2, 'permission:default/index', NULL, NULL, NULL, NULL),
('default/password', 2, 'permission:default/password', NULL, NULL, NULL, NULL),
('role/auth', 2, 'permission:role/auth', NULL, NULL, NULL, NULL),
('role/create', 2, 'permission:role/create', NULL, NULL, NULL, NULL),
('role/delete', 2, 'permission:role/delete', NULL, NULL, NULL, NULL),
('role/index', 2, 'permission:role/index', NULL, NULL, NULL, NULL),
('role/recover', 2, 'permission:role/recover', NULL, NULL, NULL, NULL),
('role/trash', 2, 'permission:role/trash', NULL, NULL, NULL, NULL),
('role/update', 2, 'permission:role/update', NULL, NULL, NULL, NULL);
('customer/index', 2, 'permission:customer/index', NULL, NULL, NULL, NULL);
('customer/create', 2, 'permission:customer/create', NULL, NULL, NULL, NULL);
('customer/update', 2, 'permission:customer/update', NULL, NULL, NULL, NULL);
('customer/view', 2, 'permission:customer/view', NULL, NULL, NULL, NULL);
('customer/delete', 2, 'permission:customer/delete', NULL, NULL, NULL, NULL);




-- --------------------------------------------------------

--
-- 表的结构 `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('1', 'admin/create'),
('1', 'admin/delete'),
('1', 'admin/index'),
('2', 'admin/index'),
('1', 'admin/login-logs'),
('2', 'admin/login-logs'),
('1', 'admin/recover'),
('1', 'admin/trash'),
('1', 'admin/update'),
('1', 'default/index'),
('2', 'default/index'),
('1', 'default/password'),
('2', 'default/password'),
('1', 'role/auth'),
('2', 'role/auth'),
('1', 'role/create'),
('1', 'role/delete'),
('1', 'role/index'),
('2', 'role/index'),
('1', 'role/recover'),
('1', 'role/trash'),
('1', 'role/update');

-- --------------------------------------------------------

--
-- 表的结构 `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `customer`
--

CREATE TABLE `customer` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` char(32) NOT NULL DEFAULT '' COMMENT '客户编码',
  `name` char(32) NOT NULL DEFAULT '' COMMENT '客户主要名称',
  `number` char(32) NOT NULL DEFAULT '' COMMENT '客户代号',
  `aide_name` char(32) DEFAULT NULL COMMENT '辅助名称',
  `group_id` int(11) NOT NULL COMMENT '上级单位id',
  `level` int(11) NOT NULL COMMENT '级别',
  `type` int(11) NOT NULL COMMENT '客户类型',
  `company` char(32) DEFAULT NULL COMMENT '集团机构编号',
  `time` int(11) NOT NULL COMMENT '录入时间',
  `admin_id` int(11) NOT NULL COMMENT '录入管理员的id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `manager_login_logs`
--

CREATE TABLE `manager_login_logs` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `manager_login_logs`
--



-- --------------------------------------------------------

--
-- 表的结构 `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1502079348),
('m140506_102106_rbac_init', 1502079355);

-- --------------------------------------------------------

--
-- 表的结构 `role`
--

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` text,
  `remark` text,
  `create_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_at` int(11) DEFAULT '0',
  `update_at` int(11) DEFAULT '0',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `role`
--

INSERT INTO `role` (`id`, `name`, `remark`, `create_id`, `update_id`, `create_at`, `update_at`, `status`) VALUES
(1, 'admin', '管理员1', 0, 5, 1502089385, 1502098962, 0),
(2, '测试', '测试', 5, 5, 1502100641, 1502101964, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agency`
--
ALTER TABLE `agency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager_login_logs`
--
ALTER TABLE `manager_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `agency`
--
ALTER TABLE `agency`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `manager_login_logs`
--
ALTER TABLE `manager_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `role`
--
ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 限制导出的表
--

--
-- 限制表 `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 限制表 `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
