-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 11, 2023 at 04:32 PM
-- Server version: 8.0.26
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simflex_clean`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu`
--

CREATE TABLE `admin_menu` (
  `menu_id` int NOT NULL,
  `menu_pid` int DEFAULT NULL,
  `priv_id` int NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `hidden` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB AVG_ROW_LENGTH=910 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `admin_menu`
--

INSERT INTO `admin_menu` (`menu_id`, `menu_pid`, `priv_id`, `npp`, `name`, `link`, `model`, `icon`, `hidden`) VALUES
(1, NULL, 1, 1, 'Управление', '/admin/admin/', '', 'home', 0),
(2, 1, 1, 1, 'Меню', '/admin/admin/menu/', 'admin_menu', 'settings-mini', 0),
(3, 1, 2, 3, 'Роли', '/admin/admin/role/', 'user_role', 'users-mini', 0),
(4, 1, 1, 6, 'Привилегии', '/admin/admin/priv/', 'user_priv', 'fingerprint-mini', 0),
(5, 1, 2, 2, 'Пользователи', '/admin/admin/user/', 'user', 'user-mini', 0),
(6, 1, 2, 7, 'Права доступа', '/admin/admin/rights/', 'user_role_priv', 'file-shield-mini', 0),
(7, NULL, 2, 2, 'Настройки сайта', '/admin/site/', '', 'settings', 0),
(9, 7, 1, 1, 'Меню', '/admin/site/menu/', 'menu', 'user-mini', 0),
(10, 7, 1, 2, 'Модули', '/admin/site/module/', 'module_item', 'puzzle-mini', 0),
(11, 1, 1, 3, 'Компоненты', '/admin/admin/component/', 'component', 'container-mini', 0),
(12, NULL, 2, 3, 'Редактор страниц', '/admin/content/', 'content', 'edit', 0),
(14, 7, 2, 8, 'Настройки', '/admin/site/settings/', 'settings', 'settings-mini', 0),
(15, 7, 1, 9, 'SEO', '/admin/site/seo/', 'seo', 'chart-mini', 0),
(22, 1, 1, 2, 'Структура', '/admin/admin/structure/', 'struct_data', 'layout-mini', 0),
(23, 22, 1, 2, 'Типы полей', '/admin/admin/structure/fields/', 'struct_field', 'puzzle', 0),
(24, 22, 1, 1, 'Таблицы', '/admin/admin/structure/tables/', 'struct_table', 'puzzle', 0),
(25, 22, 1, 3, 'Параметры', '/admin/admin/structure/params/', 'struct_param', 'puzzle', 0),
(26, 22, 1, 5, 'Поля в таблицах', '/admin/admin/structure/', 'struct_data', 'puzzle', 0),
(27, 22, 1, 4, 'Параметры полей', '/admin/admin/structure/field_param/', 'struct_field_param', 'puzzle', 0),
(28, 1, 1, 3, 'Модули', '/admin/admin/module/', 'module', 'puzzle-mini', 0),
(29, 28, 1, 5, 'Параметры модулей', '/admin/admin/module/param/', 'module_param', '', 0),
(30, 11, 1, 4, 'Параметры компонентов', '/admin/admin/component_param/', 'component_param', '', 0),
(31, NULL, 3, 57, 'Аккаунт', '/admin/account/', '', 'user', 1),
(35, 1, 1, 54, 'Cron', '/admin/cron/', 'cron', 'calendar-mini', 0),
(36, 6, 2, 8, 'Персональные права', '/admin/user_priv_personal/', 'user_priv_personal', '', 0),
(37, 1, 1, 50, 'Шаблоны страниц', '/admin/admin/content_template/', 'content_template', 'layout-mini', 0),
(38, 37, 1, 51, 'Параметры шаблонов', '/admin/admin/content_template_param/', 'content_template_param', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin_widget`
--

CREATE TABLE `admin_widget` (
  `widget_id` int NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `priv_id` int DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `component`
--

CREATE TABLE `component` (
  `component_id` int NOT NULL,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `params` longtext
) ENGINE=InnoDB AVG_ROW_LENGTH=4096 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `component`
--

INSERT INTO `component` (`component_id`, `class`, `name`, `params`) VALUES
(1, '\\App\\Extensions\\Content\\Content', 'Материалы', 'a:2:{s:6:\"аег\";s:1:\"q\";s:6:\"groupp\";a:1:{s:4:\"jopa\";s:1:\"2\";}}'),
(2, '\\App\\Extensions\\Auth\\Auth', 'Авторизация', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `component_param`
--

CREATE TABLE `component_param` (
  `cp_id` int NOT NULL,
  `component_id` int NOT NULL,
  `param_pid` int DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `field_id` int DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `help` varchar(255) NOT NULL DEFAULT '',
  `params` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `content_id` int NOT NULL,
  `pid` int DEFAULT NULL,
  `active` int NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `short` text,
  `text` longtext,
  `params` longtext,
  `file` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `template_id` int DEFAULT NULL,
  `npp` int DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1638 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`content_id`, `pid`, `active`, `date`, `title`, `alias`, `path`, `short`, `text`, `params`, `file`, `photo`, `template_id`, `npp`) VALUES
(17, NULL, 1, '2023-09-01', 'Главная', '/', '/', '', '', NULL, NULL, '', 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `content_template`
--

CREATE TABLE `content_template` (
  `template_id` int NOT NULL,
  `template_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `template_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'relative path from Extensions/Content/tpl. for example "/mainPage.tpl" or "/news/item.tpl"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_template`
--

INSERT INTO `content_template` (`template_id`, `template_name`, `template_path`) VALUES
(1, 'Главная', 'index.tpl');

-- --------------------------------------------------------

--
-- Table structure for table `content_template_param`
--

CREATE TABLE `content_template_param` (
  `ctp_id` int NOT NULL,
  `template_id` int NOT NULL,
  `param_pid` int DEFAULT NULL,
  `position` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `field_id` int DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `help` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `default_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_template_param`
--

INSERT INTO `content_template_param` (`ctp_id`, `template_id`, `param_pid`, `position`, `field_id`, `name`, `label`, `npp`, `help`, `params`, `default_value`, `group_name`) VALUES
(1, 1, NULL, 'left', 1, 'seo_title_1', 'SEO заголовок 1', 10000, '', 'a:0:{}', NULL, 'SEO'),
(2, 1, NULL, 'left', 9, 'seo_text_1', 'SEO текст 1', 10000, '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}', NULL, 'SEO'),
(3, 1, NULL, 'left', 1, 'seo_title_2', 'SEO заголовок 2', 10000, '', 'a:0:{}', NULL, 'SEO'),
(4, 1, NULL, 'left', 9, 'seo_text_2', 'SEO текст 2', 10000, '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}', NULL, 'SEO'),
(5, 1, NULL, 'right', 1, 'meta_title', 'Заголовок', 11000, '', 'a:0:{}', NULL, 'Мета'),
(6, 1, NULL, 'right', 9, 'meta_kw', 'Ключевые слова', 11000, '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}', NULL, 'Мета'),
(7, 1, NULL, 'right', 9, 'meta_de', 'Описание', 11000, '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}', NULL, 'Мета');

-- --------------------------------------------------------

--
-- Table structure for table `cron`
--

CREATE TABLE `cron` (
  `id` int NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `timing` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ext_id` int DEFAULT NULL COMMENT 'ID компонента',
  `module_id` int DEFAULT NULL COMMENT 'ID модуля, НЕ экземпляра модуля',
  `plugin_name` varchar(50) DEFAULT NULL COMMENT 'Название плагина',
  `action` varchar(255) NOT NULL,
  `cparams` varchar(1023) DEFAULT NULL,
  `class_fqn` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `cron_log`
--

CREATE TABLE `cron_log` (
  `id` int NOT NULL,
  `cron_id` int NOT NULL,
  `datetime` datetime NOT NULL,
  `result` varchar(2047) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=39 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` int NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` enum('login_attempt','login_success') NOT NULL COMMENT 'Попытка входа в панель управления;;Успешный вход в панель управления',
  `ip` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `data` varchar(1000) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=5461 DEFAULT CHARSET=utf8mb3 COMMENT='Журнал входов в админку. По умолчанию отключен. Включать в AdminPlugLog';

-- --------------------------------------------------------

--
-- Table structure for table `mail_log`
--

CREATE TABLE `mail_log` (
  `log_id` int NOT NULL,
  `success` int NOT NULL COMMENT '1 - отправлено, 0 - не отправлено, ошибки при отправке',
  `mail_from` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fail_info` varchar(255) DEFAULT NULL,
  `reply_to` varchar(255) DEFAULT NULL,
  `transport` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int NOT NULL,
  `menu_pid` int DEFAULT NULL,
  `component_id` int DEFAULT NULL,
  `active` int NOT NULL DEFAULT '0',
  `hidden` int NOT NULL DEFAULT '0',
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1260 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `menu_pid`, `component_id`, `active`, `hidden`, `npp`, `name`, `link`) VALUES
(1, NULL, NULL, 1, 0, 1, 'Главная', '/');

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `id` int NOT NULL,
  `file` varchar(255) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=2730 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `module_id` int NOT NULL,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('site','admin') NOT NULL DEFAULT 'site',
  `postexec` int NOT NULL DEFAULT '0' COMMENT 'Выполнять после контента'
) ENGINE=InnoDB AVG_ROW_LENGTH=2730 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`module_id`, `class`, `name`, `type`, `postexec`) VALUES
(1, '\\App\\Extensions\\Menu\\Menu', 'Меню', 'site', 0),
(2, 'App\\Extensions\\Content\\ModuleContent', 'Список материалов', 'site', 0),
(3, '\\App\\Extensions\\Breadcrumbs\\Breadcrumbs', 'Хлебные крошки', 'site', 1),
(4, '\\App\\Extensions\\Block\\Block', 'Текстовый блок', 'site', 0),
(7, 'Breadcrumbs', 'Admin. Хлебные крошки', 'admin', 0),
(8, 'Menu', 'Admin. Меню', 'admin', 0),
(9, '\\App\\Extensions\\Slider\\ModuleSlider', 'Слайдер', 'site', 0),
(11, 'Account', 'Admin. Аккаунт', 'admin', 0),
(12, 'Install', 'Admin. Репозиторий', 'admin', 0),
(13, '\\App\\Extensions\\Code\\ModuleCode', 'Код', 'site', 0),
(14, '\\App\\Extensions\\Form\\ModuleForm', 'Форма', 'site', 0);

-- --------------------------------------------------------

--
-- Table structure for table `module_item`
--

CREATE TABLE `module_item` (
  `item_id` int NOT NULL,
  `module_id` int NOT NULL,
  `menu_id` int DEFAULT NULL,
  `posname` varchar(255) NOT NULL,
  `active` int NOT NULL DEFAULT '0',
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `params` longtext
) ENGINE=InnoDB AVG_ROW_LENGTH=1489 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `module_item`
--

INSERT INTO `module_item` (`item_id`, `module_id`, `menu_id`, `posname`, `active`, `npp`, `name`, `params`) VALUES
(1, 1, NULL, 'mainmenu', 1, 1, 'Меню главное', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"0\";}'),
(3, 3, NULL, 'content-outer-before', 1, 0, 'Хлебные крошки', 'a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}'),
(4, 4, NULL, 'footer', 1, 102, 'Копирайт', 'a:2:{s:7:\"content\";s:120:\"<p>2012 &copy; ДСМ Урал<br /> г. Пермь, ул. Екатерининская 59, тел.: (342) 204-40-64</p>\";s:8:\"is_title\";s:1:\"0\";}'),
(8, 4, NULL, 'footer', 1, 111, 'Счетчики', 'a:2:{s:7:\"content\";s:131:\"<p><img src=\"../../../theme/default/img/liveinternet.png\" alt=\"\" /> <img src=\"../../../theme/default/img/metrika.png\" alt=\"\" /></p>\";s:8:\"is_title\";s:1:\"0\";}'),
(9, 4, NULL, 'header-right', 1, 0, 'Контакты вверху', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:7:\"content\";s:197:\"<div class=\"phone\"><span>(342)</span> 204-40-64</div>\r\n<div style=\"margin-top: 4px; font-size: 12px;\">г. Пермь, <a href=\"/contacts/\">ул. Екатерининская 59</a>, оф. 204</div>\";}'),
(10, 1, NULL, 'footer', 1, 101, 'Меню сайта внизу страницы', 'a:1:{s:8:\"is_title\";s:1:\"0\";}'),
(11, 4, NULL, 'footer', 1, 103, 'Ссылка на разработчика', 'a:2:{s:7:\"content\";s:126:\"<p><a href=\"http://internet-menu.ru\">Создание сайта</a> &mdash; веб-студия Интернет Меню</p>\";s:8:\"is_title\";s:1:\"0\";}'),
(14, 1, NULL, 'aside', 1, 51, 'Меню дочернее', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"1\";}'),
(15, 7, NULL, 'breadcrumbs', 1, 0, 'Хлебные крошки', NULL),
(16, 8, NULL, 'menu', 1, 0, 'Меню', 'a:6:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:1:\"0\";s:8:\"is_child\";s:1:\"0\";}'),
(17, 11, 31, 'content-before', 1, 116, 'Admin. Аккаунт', 'a:2:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"1\";}'),
(18, 12, 34, 'content-before', 1, 117, 'Admin. Репозиторий', 'a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}');

-- --------------------------------------------------------

--
-- Table structure for table `module_param`
--

CREATE TABLE `module_param` (
  `mp_id` int NOT NULL,
  `module_id` int NOT NULL,
  `param_pid` int DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `field_id` int DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `help` varchar(255) NOT NULL DEFAULT '',
  `params` longtext
) ENGINE=InnoDB AVG_ROW_LENGTH=819 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `module_param`
--

INSERT INTO `module_param` (`mp_id`, `module_id`, `param_pid`, `position`, `field_id`, `name`, `label`, `npp`, `help`, `params`) VALUES
(1, 2, NULL, 'right', NULL, 'content_data', 'Вывод данных', 0, '', NULL),
(2, 2, 1, '', 2, 'content_id', 'Раздел', 0, '', 'a:1:{s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:7:\"content\";s:6:\"fk_key\";s:10:\"content_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(3, 2, 1, '', 1, 'tpl', 'Шаблон вывода', 1, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:12:\"mod_list.tpl\";}}'),
(4, 2, 1, '', 2, 'cnt_limit', 'Количество', 2, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"3\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(5, 2, NULL, 'right', NULL, 'module_param_view', 'Внешний вид', 1, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(6, 2, 5, '', 3, 'date', 'Дата', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(7, 2, 5, '', 3, 'short', 'Анонс', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(8, 2, 5, '', 3, 'more', 'Кнопка далее', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(9, 2, 5, '', 1, 'more_text', 'Кнопка далее', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:23:\"Читать далее\";}}'),
(10, 4, NULL, 'left', NULL, 'module_param_content', 'Контент', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(11, 4, 10, '', 9, 'content', 'Текст', 0, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),
(12, 8, NULL, 'right', 2, 'max_level', 'Макс. ур. вложенности', 0, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(13, 8, NULL, 'right', 2, 'start_id', 'Выводить подразделы', 0, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(14, 8, NULL, 'right', 3, 'is_child', 'Дочернее меню', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(15, 1, NULL, '', 2, 'start_id', 'Начальный пункт меню', 0, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(16, 1, NULL, '', 2, 'max_level', 'Максимальный уровень вложенности', 0, '0 - без вложенных меню', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(17, 1, NULL, '', 3, 'is_child', 'Выводить дочернее меню', 0, 'В текущем разделе выводить внутренние подразделы', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}}'),
(21, 13, NULL, 'left', NULL, 'module_param_content', 'Контент', 0, '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),
(22, 13, 21, '', 9, 'content', 'Текст', 0, '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(24, 14, NULL, 'right', 1, 'submit_button_text', 'Кнопка', 1, 'Текст на кнопке \"отправить\"', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:18:\"Отправить\";}}'),
(25, 14, NULL, 'right', 9, 'fields', 'Поля формы', 2, 'формат: название:тип:{обязательно 1}, пример ФИО:string, Почта:email:1', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(26, 14, NULL, 'right', 1, 'form_tpl', 'Шаблон формы', 4, 'например awesomeForm.tpl, складывать в Extensions/Form/tpl', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');

-- --------------------------------------------------------

--
-- Table structure for table `plug_cache`
--

CREATE TABLE `plug_cache` (
  `cache_key` char(255) NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `value` varchar(21000) DEFAULT NULL,
  `expires` int DEFAULT '3600' COMMENT 'В секундах'
) ENGINE=MEMORY AVG_ROW_LENGTH=63776 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `likes` int DEFAULT NULL,
  `dislikes` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `pros` text,
  `cons` text,
  `comment` text,
  `reply` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_file`
--

CREATE TABLE `review_file` (
  `rf_id` int NOT NULL,
  `review_id` int DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo`
--

CREATE TABLE `seo` (
  `seo_id` int NOT NULL,
  `seo_pid` int DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `metatags` text
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `seo`
--

INSERT INTO `seo` (`seo_id`, `seo_pid`, `link`, `title`, `description`, `keywords`, `metatags`) VALUES
(1, NULL, '/', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` enum('int','string','bool','enum') NOT NULL DEFAULT 'string',
  `enum_values` varchar(255) DEFAULT NULL COMMENT 'key::value;;key::value'
) ENGINE=InnoDB AVG_ROW_LENGTH=3276 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `npp`, `name`, `alias`, `value`, `type`, `enum_values`) VALUES
(1, 0, 'Название сайта', 'site_name', 'Simflex', 'string', NULL),
(6, 0, 'Email', 'email', '', 'string', NULL),
(7, 12, 'Телефон', 'phone', '', 'string', NULL),
(9, 11, 'Часы работы', 'workhours', 'ПН-ВС — с 09:00 до 18:00', 'string', NULL),
(18, 14, 'Email для получения заявок', 'form_email', '', 'string', NULL),
(23, 0, 'Адрес', 'address', '', 'string', NULL),
(24, 0, 'ВКонтакте', 'vk', '', 'string', NULL),
(25, 0, 'Телеграм', 'tg', '', 'string', NULL),
(26, 0, 'Инстаграм', 'inst', '', 'string', NULL),
(27, 13, 'WhatsApp', 'whats_app', '', 'string', NULL),
(28, 1, 'Организация', 'company', '', 'string', NULL),
(29, 1, 'Токен Telegram для получения заявок', 'form_tg_token', '', 'string', NULL),
(30, 1, 'Chat ID для получения заявок', 'form_tg_chat_id', '', 'string', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `slide_id` int NOT NULL,
  `npp` int NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `text` varchar(2047) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `href` varchar(1023) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `struct_data`
--

CREATE TABLE `struct_data` (
  `id` int NOT NULL,
  `npp` int NOT NULL DEFAULT '0',
  `table_id` int NOT NULL,
  `field_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `help` varchar(255) NOT NULL DEFAULT '',
  `placeholder` varchar(255) NOT NULL DEFAULT '',
  `params` longtext
) ENGINE=InnoDB AVG_ROW_LENGTH=805 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `struct_data`
--

INSERT INTO `struct_data` (`id`, `npp`, `table_id`, `field_id`, `name`, `label`, `help`, `placeholder`, `params`) VALUES
(1, 0, 1, 2, 'table_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(2, 0, 1, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(3, 0, 2, 2, 'field_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(4, 0, 2, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(5, 0, 2, 1, 'class', 'Класс', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(6, 0, 3, 2, 'id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(7, 1, 3, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(8, 2, 3, 2, 'table_id', 'Таблица', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"176\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_table\";s:6:\"fk_key\";s:8:\"table_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(9, 3, 3, 2, 'field_id', 'Тип данных', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"176\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(10, 4, 3, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(11, 5, 3, 1, 'label', 'Ярлык', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(12, 0, 4, 2, 'param_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(13, 2, 4, 2, 'param_pid', 'Родитель', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_param.param_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_param\";s:6:\"fk_key\";s:8:\"param_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(14, 1, 4, 2, 'table_id', 'Таблица', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_table.table_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_table\";s:6:\"fk_key\";s:8:\"table_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(15, 3, 4, 2, 'field_id', 'Тип данных', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_field.field_id.name\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(16, 4, 4, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(17, 5, 4, 1, 'label', 'Заголовок', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(18, 8, 3, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(22, 1, 5, 2, 'menu_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(23, 2, 5, 2, 'menu_pid', 'Родитель', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:23:\"admin_menu.menu_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:10:\"admin_menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(24, 3, 5, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(25, 4, 5, 2, 'priv_id', 'Привилегия', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(26, 5, 5, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"215\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(27, 6, 5, 1, 'link', 'Ссылка', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"400\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:3:\"576\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(28, 7, 5, 1, 'model', 'Модель', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(29, 1, 6, 2, 'priv_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(30, 2, 6, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(31, 3, 6, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(32, 4, 6, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(33, 5, 6, 1, 'comment', 'Комментарий', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(34, 1, 7, 1, 'role_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(35, 5, 7, 2, 'priv_id', 'Привилегия', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(36, 3, 7, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"104\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(37, 4, 7, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(38, 6, 7, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"215\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(39, 1, 8, 2, 'user_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"50\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(40, 3, 8, 2, 'role_id', 'Роль', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_role\";s:6:\"fk_key\";s:7:\"role_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(41, 2, 8, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(42, 4, 8, 1, 'login', 'Логин', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"190\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(43, 5, 8, 6, 'password', 'Пароль', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(44, 6, 8, 1, 'hash', 'Хеш', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(45, 1, 9, 2, 'id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(46, 2, 9, 2, 'role_id', 'Роль', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_role.role_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_role\";s:6:\"fk_key\";s:7:\"role_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(47, 3, 9, 2, 'priv_id', 'Привилегия', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(48, 1, 10, 2, 'menu_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(49, 4, 10, 2, 'menu_pid', 'Родитель', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:17:\"menu.menu_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(50, 5, 10, 2, 'component_id', 'Компонент', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(51, 1, 10, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"104\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(52, 2, 10, 3, 'hidden', 'Скрыть', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"104\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(53, 3, 10, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(54, 7, 10, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:3:\"991\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(55, 8, 10, 1, 'link', 'Ссылка', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"300\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(56, 1, 11, 2, 'component_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(57, 2, 11, 1, 'class', 'Класс', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(58, 3, 11, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(59, 1, 12, 2, 'content_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:17:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(60, 3, 12, 2, 'pid', 'Родитель', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:7:\"content\";s:6:\"fk_key\";s:10:\"content_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(61, 0, 12, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"104\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";}}'),
(62, 4, 12, 7, 'date', 'Дата', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(63, 5, 12, 1, 'title', 'Заголовок', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"1\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(64, 6, 12, 4, 'alias', 'Алиас', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:6:\"source\";s:5:\"title\";}}'),
(65, 7, 12, 5, 'path', 'Путь', '', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"1\";s:10:\"style_cell\";s:0:\"\";}}'),
(66, 8, 12, 9, 'short', 'Коротко', '', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(67, 9, 12, 9, 'text', 'Текст', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),
(68, 10, 12, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(70, 8, 5, 1, 'icon', 'Иконка', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(71, 0, 22, 2, 'setting_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(72, 0, 22, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(73, 0, 22, 1, 'name', 'Наименование', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(74, 0, 22, 1, 'alias', 'Алиас', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(75, 0, 22, 9, 'value', 'Значение', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(76, 6, 4, 1, 'pos', 'Позиция', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(77, 7, 4, 1, 'default_value', 'Значение по умолчанию', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(78, 0, 23, 2, 'fp_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(79, 1, 23, 2, 'field_id', 'Тип поля', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(80, 2, 23, 1, 'name', 'Наименование', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(81, 3, 23, 1, 'label', 'Ярлык', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(82, 4, 23, 2, 'type_id', 'Тип параметра', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(84, 0, 24, 2, 'module_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(85, 1, 24, 1, 'class', 'Класс', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(86, 0, 24, 1, 'name', 'Наименование', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"210\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(87, 9, 24, 14, 'type', 'Тип', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(88, 9, 23, 1, 'help', 'Подсказка', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(89, 6, 3, 1, 'help', 'Подсказка', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(90, 7, 3, 1, 'placeholder', 'Placeholder', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(91, 2, 1, 1, 'order_by', 'Сортировать по', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(92, 3, 1, 3, 'order_desc', 'По убыванию', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(93, -2, 25, 2, 'item_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(94, 2, 25, 2, 'module_id', 'Модуль', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:20:\"onChangeModule(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:6:\"module\";s:6:\"fk_key\";s:9:\"module_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(95, 3, 25, 2, 'menu_id', 'Меню', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(96, 0, 25, 1, 'posname', 'Позиция', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(97, -2, 25, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"104\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(98, -1, 25, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(99, 0, 25, 1, 'name', 'Наименование', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(100, 4, 25, 9, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(101, 6, 1, 16, 'loadstruct', 'Загрузить структуру', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:8:\"subquery\";s:0:\"\";s:4:\"text\";s:18:\"Загрузить\";s:4:\"href\";s:38:\"?action=loadstruct&table_id={table_id}\";}}'),
(225, 1, 26, 2, 'seo_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"60\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(226, 2, 26, 2, 'seo_pid', 'PID', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:1;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";i:1;s:8:\"fk_table\";s:3:\"seo\";s:6:\"fk_key\";s:6:\"seo_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";b:1;}}'),
(227, 3, 26, 1, 'link', 'Ссылка', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"300\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(228, 4, 26, 1, 'title', 'Заголовок', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"180\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(229, 5, 26, 9, 'description', 'Описание', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:0;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(230, 6, 26, 9, 'keywords', 'Keywords', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:0;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(255, 1, 28, 2, 'mp_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:19:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(256, 2, 28, 2, 'module_id', 'Модуль', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:6:\"module\";s:6:\"fk_key\";s:9:\"module_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(257, 3, 28, 2, 'param_pid', 'PID', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"module_param\";s:6:\"fk_key\";s:5:\"mp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(258, 4, 28, 2, 'field_id', 'Тип параметра', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(259, 5, 28, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"200\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(260, 6, 28, 1, 'label', 'Ярлык', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:1;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(261, 1, 28, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(262, 12, 28, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(263, 2, 28, 1, 'position', 'Позиция', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(264, 12, 4, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(268, 12, 23, 1, 'default_value', 'Значение по умолчанию', '', '', 'a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),
(269, 7, 8, 1, 'hash_admin', 'Admin. Хеш', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),
(270, 1, 29, 2, 'cp_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(271, 2, 29, 2, 'component_id', 'Компонент', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(272, 3, 29, 2, 'param_pid', 'PID', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:1;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";i:1;s:8:\"fk_table\";s:15:\"component_param\";s:6:\"fk_key\";s:5:\"cp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";b:1;}}'),
(273, 4, 29, 1, 'position', 'Позиция', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(274, 5, 29, 2, 'field_id', 'Тип параметра', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(275, 6, 29, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(276, 7, 29, 1, 'label', 'Ярлык', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:1;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(277, 1, 29, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(278, 9, 29, 1, 'help', 'Подсказка', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(279, 10, 29, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(280, 9, 11, 1, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(281, 7, 28, 1, 'help', 'Подсказка', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(282, 7, 1, 2, 'priv_add', 'Привилегия добавления', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(283, 7, 1, 2, 'priv_delete', 'Привилегия удаления', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}');
INSERT INTO `struct_data` (`id`, `npp`, `table_id`, `field_id`, `name`, `label`, `help`, `placeholder`, `params`) VALUES
(284, 7, 1, 2, 'priv_edit', 'Привилегия изменения', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(285, 3, 30, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";}}'),
(286, 6, 30, 1, 'href', 'Ссылка', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(287, 1, 30, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(288, 5, 30, 11, 'photo', 'Фото', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"110\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:4:\"path\";s:6:\"slider\";s:5:\"small\";s:0:\"\";s:6:\"medium\";s:0:\"\";s:5:\"large\";s:9:\"960x340x1\";}}'),
(289, 1, 30, 2, 'slide_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(290, 4, 30, 9, 'text', 'Текст', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),
(295, 3, 32, 14, 'action', 'Действие', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";}}'),
(296, 5, 32, 1, 'browser', 'Браузер', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(297, 6, 32, 1, 'data', 'Информация', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(298, 2, 32, 8, 'datetime', 'Время', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(299, 4, 32, 1, 'ip', 'IP адрес', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(300, 1, 32, 2, 'log_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(301, 6, 33, 1, 'action', 'Действие', 'Метод компонента или плагина', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),
(302, 1, 33, 3, 'active', 'Активно', '', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),
(303, 7, 33, 9, 'cparams', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(304, 4, 33, 2, 'ext_id', 'Расширение', 'ID компонента', '', 'a:1:{s:4:\"main\";a:15:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:5:\"class\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(305, 1, 33, 2, 'id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(306, 3, 33, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),
(307, 5, 33, 1, 'plugin_name', 'Плагин', 'Название класса плагина', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),
(308, 2, 33, 1, 'timing', 'Время', 'Как в *nix crontab. Например */10 * * * *', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"160\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),
(309, 8, 8, 1, 'email', 'Email', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(310, 10, 8, 1, 'name', 'Имя', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(312, 8, 24, 3, 'postexec', 'Выполнять после контента', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),
(313, 11, 12, 11, 'photo', 'Фото', '', '', 'a:1:{s:4:\"main\";a:18:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"88\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"50\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";s:4:\"path\";s:7:\"content\";s:5:\"small\";s:7:\"200x150\";s:6:\"medium\";s:0:\"\";s:5:\"large\";s:7:\"800x600\";}}'),
(314, 1, 22, 14, 'type', 'Тип', '', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:6:\"string\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"1\";s:10:\"style_cell\";s:0:\"\";}}'),
(315, 2, 22, 1, 'enum_values', 'Значения enum', '', '', 'a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),
(316, 1, 34, 1, 'id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(317, 2, 34, 1, 'user_id', 'Пользователь', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(318, 3, 34, 1, 'priv_id', 'Привилегия', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(319, 1, 35, 1, 'template_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),
(320, 2, 35, 1, 'template_name', 'Название', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(321, 3, 35, 1, 'template_path', 'Path', 'relative path from Extensions/Content/tpl. for example \"mainPage.tpl\" or \"news/item.tpl\"', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(322, 2, 12, 2, 'template_id', 'Шаблон', '', '', 'a:1:{s:4:\"main\";a:17:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"176\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:16:\"content_template\";s:6:\"fk_key\";s:11:\"template_id\";s:8:\"fk_label\";s:13:\"template_name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(323, 1, 36, 1, 'ctp_id', 'ID', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"54\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:2:\"40\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(324, 2, 36, 2, 'template_id', 'Шаблон', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:16:\"content_template\";s:6:\"fk_key\";s:11:\"template_id\";s:8:\"fk_label\";s:13:\"template_name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(325, 3, 36, 2, 'param_pid', 'PID', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:22:\"content_template_param\";s:6:\"fk_key\";s:6:\"ctp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),
(326, 4, 36, 1, 'position', 'Позиция', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(327, 5, 36, 2, 'field_id', 'Тип', '', '', 'a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),
(328, 6, 36, 1, 'name', 'Название', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"100\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(329, 7, 36, 1, 'label', 'Ярлык', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:3:\"100\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}'),
(330, 1, 36, 15, 'npp', '№ п/п', '', '', 'a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"107\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:3:\"991\";s:9:\"width_mob\";s:1:\"0\";}}'),
(331, 9, 36, 1, 'help', 'Help', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(332, 10, 36, 9, 'params', 'Параметры', '', '', 'a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),
(333, 99, 1, 1, 'class', 'Класс драйвера', 'с namespace. например \\Simflex\\Extensions\\Content\\Admin\\AdminContent', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(334, 15, 36, 1, 'default_value', 'Значение по умолчанию', '', '', 'a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),
(335, 0, 36, 1, 'group_name', 'Группа', '', '', 'a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:9:\"width_mob\";s:1:\"0\";s:3:\"pos\";s:0:\"\";s:9:\"pos_group\";s:0:\"\";}}');

-- --------------------------------------------------------

--
-- Table structure for table `struct_field`
--

CREATE TABLE `struct_field` (
  `field_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `class` varchar(255) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1489 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `struct_field`
--

INSERT INTO `struct_field` (`field_id`, `name`, `class`) VALUES
(1, 'Строка', '\\Simflex\\Admin\\Fields\\FieldString'),
(2, 'Целое число', '\\Simflex\\Admin\\Fields\\FieldInt'),
(3, 'Булевая переменная', '\\Simflex\\Admin\\Fields\\FieldBool'),
(4, 'Алиас', '\\Simflex\\Admin\\Fields\\FieldAlias'),
(5, 'Url-путь', '\\Simflex\\Admin\\Fields\\FieldPath'),
(6, 'Пароль', '\\Simflex\\Admin\\Fields\\FieldPassword'),
(7, 'Дата', '\\Simflex\\Admin\\Fields\\FieldDate'),
(8, 'Дата и время', '\\Simflex\\Admin\\Fields\\FieldDateTime'),
(9, 'Текст', '\\Simflex\\Admin\\Fields\\FieldText'),
(10, 'Файл', '\\Simflex\\Admin\\Fields\\FieldFile'),
(11, 'Изображение', '\\Simflex\\Admin\\Fields\\FieldImage'),
(14, 'Варианты', '\\Simflex\\Admin\\Fields\\FieldEnum'),
(15, '№ п/п', '\\Simflex\\Admin\\Fields\\FieldNPP'),
(16, 'Виртуальное поле', '\\Simflex\\Admin\\Fields\\FieldVirtual'),
(17, 'Время', '\\Simflex\\Admin\\Fields\\FieldTime'),
(18, 'Дробное число', '\\Simflex\\Admin\\Fields\\FieldDouble'),
(19, 'Связь многие ко многим', '\\Simflex\\Admin\\Fields\\FieldMultiKey'),
(20, 'Пароль видимый', '\\Simflex\\Admin\\Fields\\FieldPasswordVisible'),
(21, 'Таблица', '\\Simflex\\Admin\\Fields\\FieldTable'),
(22, 'Теги', '\\Simflex\\Admin\\Fields\\FieldTags'),
(23, 'Двусторонняя связь', '\\Simflex\\Admin\\Fields\\FieldRelation'),
(24, 'Виртуальная таблица', '\\Simflex\\Admin\\Fields\\FieldVirtualTable');

-- --------------------------------------------------------

--
-- Table structure for table `struct_field_param`
--

CREATE TABLE `struct_field_param` (
  `fp_id` int NOT NULL,
  `field_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type_id` int NOT NULL COMMENT 'Тип параметра FK struct_field',
  `help` varchar(255) NOT NULL DEFAULT '',
  `default_value` text
) ENGINE=InnoDB AVG_ROW_LENGTH=655 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `struct_field_param`
--

INSERT INTO `struct_field_param` (`fp_id`, `field_id`, `name`, `label`, `type_id`, `help`, `default_value`) VALUES
(1, 2, 'is_fk', 'Внешний ключ', 3, '', NULL),
(2, 2, 'fk_table', 'Внешний ключ. Таблица', 1, '', NULL),
(3, 2, 'fk_key', 'Внешний ключ. Ключ', 1, '', NULL),
(4, 2, 'fk_label', 'Внешний ключ. Ярлык', 1, '', NULL),
(6, 2, 'fk_is_pid', 'Внешний ключ. Поле PID', 3, '', NULL),
(9, 9, 'editor_mini', 'Минимальный редактор', 3, '', NULL),
(10, 9, 'editor_full', 'Полный редактор', 3, '', NULL),
(11, 16, 'subquery', 'Подзапрос', 1, '', NULL),
(12, 16, 'text', 'Текст', 1, '', NULL),
(13, 16, 'href', 'Ссылка', 1, '', NULL),
(14, 10, 'path', 'Адрес', 1, '/uf/files/{указанный адрес}', NULL),
(15, 11, 'path', 'Адрес', 1, '/uf/images/{указанный адрес}', NULL),
(16, 11, 'small', 'Малый размер', 1, 'Формат 200x150. Точный размер - 200x150x1', NULL),
(17, 11, 'medium', 'Средний размер', 1, 'Формат 400x300. Точный размер - 400x300x1', NULL),
(18, 11, 'large', 'Большой размер', 1, 'Формат 640x480. Точный размер - 640x480x1', NULL),
(19, 4, 'source', 'Источник', 1, 'Из какого поля таблицы брать значение. Например: name', 'name'),
(20, 16, 'in_modal', 'Открыть в модальном окне', 3, '', '0'),
(21, 18, 'decimals', 'Число знаков после запятой', 2, '0 - автоматически', '0'),
(22, 18, 'dec_point', 'Разделитель дробной части', 1, '', '.'),
(23, 18, 'thousands_sep', 'Разделитель порядков', 1, '', ' '),
(24, 17, 'use_seconds', 'С секундами', 3, 'Учитывать секунды', '0'),
(25, 19, 'table_values', 'Таблица сущностей', 1, '', NULL),
(26, 19, 'table_relations', 'Таблица связей', 1, '', NULL),
(27, 19, 'key', 'Ключ таблицы сущностей', 1, '', NULL),
(28, 19, 'key_alias', 'Поле-ярлык у сущности', 1, 'Например name у catalog_category', NULL),
(29, 21, 'struct', 'Параметры', 21, '', '{\n    \"s\": [\n        {\n            \"n\": \"n\",\n            \"t\": \"text\",\n            \"l\": \"Имя\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"t\",\n            \"t\": \"combo\",\n            \"l\": \"Тип\",\n            \"v\": \"text\",\n            \"e\": \"text=Текст,,int=Число,,combo=Список,,editor=Редактор,,image=Изображение,,file=Файл\"\n        },\n        {\n            \"n\": \"l\",\n            \"t\": \"text\",\n            \"l\": \"Заголовок\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"v\",\n            \"t\": \"text\",\n            \"l\": \"Значение\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"e\",\n            \"t\": \"text\",\n            \"l\": \"Дополнительно\",\n            \"v\": \"\",\n            \"e\": \"\"\n        }\n    ],\n    \"v\": []\n}'),
(30, 23, 'relation', 'Таблица связей', 1, '', NULL),
(31, 23, 'left', 'Левая колонка', 1, '', NULL),
(32, 23, 'right', 'Правая колонка', 1, '', NULL),
(33, 23, 'table', 'Таблица сущностей', 1, '', NULL),
(34, 23, 'name', 'Ярлык', 1, '', NULL),
(35, 14, 'enum', 'Варианты (key=value;;)', 1, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `struct_param`
--

CREATE TABLE `struct_param` (
  `param_id` int NOT NULL,
  `param_pid` int DEFAULT NULL,
  `table_id` int NOT NULL,
  `field_id` int DEFAULT NULL,
  `pos` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `default_value` text NOT NULL,
  `params` longtext,
  `npp` int DEFAULT '0',
  `group_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1820 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `struct_param`
--

INSERT INTO `struct_param` (`param_id`, `param_pid`, `table_id`, `field_id`, `pos`, `name`, `label`, `default_value`, `params`, `npp`, `group_name`) VALUES
(1, NULL, 3, NULL, 'right', 'main', 'Параметры', '', NULL, 0, ''),
(2, 1, 3, 3, '', 'pk', 'Первичный ключ', '', NULL, 0, ''),
(3, 1, 3, 3, '', 'e2n', 'Преобразовать к NULL', '', NULL, 0, ''),
(4, 1, 3, 3, '', 'hidden', 'Скрытый', '', NULL, 0, ''),
(5, 1, 3, 2, '', 'width', 'Ширина столбца', '', NULL, 0, ''),
(6, 1, 3, 1, '', 'defaultValue', 'Значение по умолчанию', '', NULL, 0, ''),
(7, 1, 3, 3, '', 'required', 'Обязательно для заполнения', '', NULL, 0, ''),
(8, 1, 3, 3, '', 'filter', 'Добавить в фильтр', '', NULL, 0, ''),
(10, NULL, 12, NULL, 'right', 'content_main', 'Вывод данных', '', NULL, 0, ''),
(11, 10, 12, 1, '', 'tpl', 'Шаблон вывода', 'mod_list.tpl', NULL, 0, ''),
(12, 10, 12, 2, '', 'cnt_limit', 'Количество', '3', NULL, 0, ''),
(13, NULL, 12, NULL, 'right', 'content_view', 'Внешний вид', '', NULL, 0, ''),
(14, 13, 12, 3, '', 'date', 'Дата', '', NULL, 0, ''),
(15, 13, 12, 3, '', 'short', 'Анонс', '0', NULL, 0, ''),
(16, 13, 12, 3, '', 'more', 'Кнопка далее', '0', NULL, 0, ''),
(17, 13, 12, 1, '', 'more_text', 'Текст на кнопке далее', 'Читать далее', NULL, 0, ''),
(18, 1, 3, 1, '', 'onchange', 'on change', '', NULL, 0, ''),
(19, NULL, 4, NULL, 'right', 'main', 'Параметры', '', NULL, 0, ''),
(20, 19, 4, 1, '', 'defaultValue', 'Значение по умолчанию', '', NULL, 0, ''),
(21, NULL, 28, NULL, 'right', 'module_param_main', 'Параметры', '', NULL, 0, ''),
(22, 21, 28, 1, '', 'default_value', 'Значение по умолчанию', '', NULL, 0, ''),
(23, NULL, 25, NULL, 'right', 'module_item_main', 'Параметры', '', NULL, 0, ''),
(25, NULL, 25, 3, 'right', 'is_title', 'Показывать заголовок', '1', NULL, 0, ''),
(26, NULL, 25, 3, 'right', 'is_wrap', 'Выводить обертку', '1', NULL, 0, ''),
(27, 10, 12, 3, '', 'hide_title', 'Не показывать заголовок', '0', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:1:\"0\";}}', 0, ''),
(28, 1, 3, 3, '', 'readonly', 'Только чтение', '0', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', 0, ''),
(29, 1, 3, 1, '', 'style_cell', 'CSS стили ячейки', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', 0, ''),
(30, NULL, 25, 1, 'right', 'cssclass', 'CSS Класс', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', 0, ''),
(31, 1, 3, 2, '', 'screen_width', 'Показывать на мониторах до, px', '0', 'a:1:{s:4:\"main\";a:6:{s:12:\"defaultValue\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}', 0, ''),
(32, 1, 3, 2, '', 'width_mob', 'Ширина столбца (мобильная)', '', 'a:1:{s:4:\"main\";a:6:{s:12:\"defaultValue\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}', 0, 'NULL'),
(33, 1, 3, 1, '', 'pos', 'Позиция', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', 0, NULL),
(34, 1, 3, 1, '', 'pos_group', 'Группа', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `struct_table`
--

CREATE TABLE `struct_table` (
  `table_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `order_by` varchar(255) DEFAULT NULL,
  `order_desc` int DEFAULT NULL,
  `priv_add` int DEFAULT NULL COMMENT 'Привелегия, которая позволяет добавлять строки в таблицу',
  `priv_edit` int DEFAULT NULL COMMENT 'Привелегия, которая позволяет изменять строки в таблице',
  `priv_delete` int DEFAULT NULL COMMENT 'Привелегия, которая позволяет удалять строки из таблицы',
  `class` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1365 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `struct_table`
--

INSERT INTO `struct_table` (`table_id`, `name`, `order_by`, `order_desc`, `priv_add`, `priv_edit`, `priv_delete`, `class`) VALUES
(1, 'struct_table', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'struct_field', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'struct_data', 'npp', NULL, NULL, NULL, NULL, NULL),
(4, 'struct_param', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'admin_menu', 'npp', NULL, NULL, NULL, NULL, NULL),
(6, 'user_priv', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'user_role', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'user', NULL, NULL, NULL, NULL, NULL, '\\App\\Extensions\\Catalog\\Admin\\User'),
(9, 'user_role_priv', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'menu', 'npp', NULL, NULL, NULL, NULL, NULL),
(11, 'component', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'content', NULL, NULL, NULL, NULL, NULL, '\\Simflex\\Extensions\\Content\\Admin\\AdminContent'),
(22, 'settings', 'npp', NULL, NULL, NULL, NULL, NULL),
(23, 'struct_field_param', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'module', NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'module_item', 'npp', NULL, NULL, NULL, NULL, NULL),
(26, 'seo', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'module_param', 'npp', NULL, NULL, NULL, NULL, NULL),
(29, 'component_param', 'npp', NULL, NULL, NULL, NULL, NULL),
(30, 'slider', 'npp', NULL, NULL, NULL, NULL, NULL),
(32, 'log', 'log_id', 1, NULL, NULL, NULL, NULL),
(33, 'cron', NULL, NULL, 1, 1, 1, NULL),
(34, 'user_priv_personal', NULL, NULL, 1, 1, 1, NULL),
(35, 'content_template', NULL, NULL, 1, 1, 1, NULL),
(36, 'content_template_param', NULL, NULL, 1, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `struct_table_right`
--

CREATE TABLE `struct_table_right` (
  `table_id` int NOT NULL,
  `role_id` int NOT NULL,
  `can_add` int NOT NULL,
  `can_edit` int NOT NULL,
  `can_delete` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Права пользователей на таблицу';

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL DEFAULT '3',
  `active` int NOT NULL DEFAULT '0',
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `hash_admin` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `patronym` varchar(255) DEFAULT NULL,
  `org_active` tinyint(1) DEFAULT NULL,
  `org_name` varchar(255) DEFAULT NULL,
  `org_inn` varchar(255) DEFAULT NULL,
  `other_active` varchar(255) DEFAULT NULL,
  `other_name` varchar(255) DEFAULT NULL,
  `other_last_name` varchar(255) DEFAULT NULL,
  `other_patronym` varchar(255) DEFAULT NULL,
  `vk_active` tinyint(1) DEFAULT NULL,
  `vk_id` varchar(255) DEFAULT NULL,
  `google_active` tinyint(1) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `yandex_active` tinyint(1) DEFAULT NULL,
  `yandex_id` varchar(255) DEFAULT NULL,
  `other_phone` varchar(255) DEFAULT NULL,
  `transcomp` varchar(255) DEFAULT NULL,
  `in_mailing` tinyint(1) DEFAULT '1',
  `discount` int DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=8192 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `role_id`, `active`, `login`, `password`, `hash`, `hash_admin`, `email`, `name`, `code`, `passport`, `city`, `address`, `phone`, `last_name`, `patronym`, `org_active`, `org_name`, `org_inn`, `other_active`, `other_name`, `other_last_name`, `other_patronym`, `vk_active`, `vk_id`, `google_active`, `google_id`, `yandex_active`, `yandex_id`, `other_phone`, `transcomp`, `in_mailing`, `discount`) VALUES
(1, 1, 1, 'dev', '827ccb0eea8a706c4c34a16891f84e7b', 'fb1a392b083e3121edd7c9046be62baa', '22da1292a596ee685cb20faa435c3c8f', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(2, 2, 1, 'neposeda', '827ccb0eea8a706c4c34a16891f84e7b', 'e1c3403a66d1269c4e3edbae11c11f03', '', NULL, '', NULL, NULL, NULL, NULL, NULL, '', '', 0, '', '', '0', '', '', '', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
  `auth_id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_last_login` timestamp NULL DEFAULT NULL,
  `time_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `useragent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_priv`
--

CREATE TABLE `user_priv` (
  `priv_id` int NOT NULL,
  `active` int NOT NULL DEFAULT '0',
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=4096 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_priv`
--

INSERT INTO `user_priv` (`priv_id`, `active`, `npp`, `name`, `comment`) VALUES
(1, 1, 1, 'dev', 'Привилегия разработчика'),
(2, 1, 2, 'admin', 'Привилегия администратора'),
(3, 1, 2, 'simplex_admin', 'Доступ к административному интерфейсу'),
(4, 1, 3, 'debug', 'Показывать отладчик');

-- --------------------------------------------------------

--
-- Table structure for table `user_priv_personal`
--

CREATE TABLE `user_priv_personal` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `priv_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `role_id` int NOT NULL,
  `priv_id` int NOT NULL,
  `active` int NOT NULL DEFAULT '0',
  `npp` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=5461 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`role_id`, `priv_id`, `active`, `npp`, `name`) VALUES
(1, 1, 1, 1, 'Разработчик'),
(2, 2, 1, 2, 'Администратор'),
(3, 2, 1, 3, 'Пользователь');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_priv`
--

CREATE TABLE `user_role_priv` (
  `id` int NOT NULL,
  `role_id` int DEFAULT NULL,
  `priv_id` int NOT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=2340 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_role_priv`
--

INSERT INTO `user_role_priv` (`id`, `role_id`, `priv_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 2),
(6, NULL, 4),
(7, 2, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `FK_admin_menu_admin_menu_menu_id` (`menu_pid`),
  ADD KEY `FK_admin_menu_user_priv_priv_id` (`priv_id`);

--
-- Indexes for table `admin_widget`
--
ALTER TABLE `admin_widget`
  ADD PRIMARY KEY (`widget_id`),
  ADD KEY `FK_admin_widget_user_priv_priv_id` (`priv_id`);

--
-- Indexes for table `component`
--
ALTER TABLE `component`
  ADD PRIMARY KEY (`component_id`);

--
-- Indexes for table `component_param`
--
ALTER TABLE `component_param`
  ADD PRIMARY KEY (`cp_id`),
  ADD KEY `FK_component_param_component_component_id` (`component_id`),
  ADD KEY `FK_component_param_component_param_mp_id` (`param_pid`),
  ADD KEY `FK_component_param_struct_field_field_id` (`field_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `content_content_template_null_fk` (`template_id`);

--
-- Indexes for table `content_template`
--
ALTER TABLE `content_template`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `content_template_param`
--
ALTER TABLE `content_template_param`
  ADD PRIMARY KEY (`ctp_id`),
  ADD KEY `FK_content_template_param_template_template_id` (`template_id`),
  ADD KEY `FK_content_template_param_content_template_param_mp_id` (`param_pid`),
  ADD KEY `FK_content_template_param_struct_field_field_id` (`field_id`);

--
-- Indexes for table `cron`
--
ALTER TABLE `cron`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_log`
--
ALTER TABLE `cron_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `mail_log`
--
ALTER TABLE `mail_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `FK_menu_component_component_id` (`component_id`),
  ADD KEY `FK_menu_menu_menu_id` (`menu_pid`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `module_item`
--
ALTER TABLE `module_item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `FK_module_item_position_position_id` (`posname`),
  ADD KEY `FK_module_item_module_module_id` (`module_id`);

--
-- Indexes for table `module_param`
--
ALTER TABLE `module_param`
  ADD PRIMARY KEY (`mp_id`),
  ADD KEY `FK_module_param_module_module_id` (`module_id`),
  ADD KEY `FK_module_param_module_param_mp_id` (`param_pid`),
  ADD KEY `FK_module_param_struct_field_field_id` (`field_id`);

--
-- Indexes for table `plug_cache`
--
ALTER TABLE `plug_cache`
  ADD PRIMARY KEY (`cache_key`) USING BTREE;

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `review_file`
--
ALTER TABLE `review_file`
  ADD PRIMARY KEY (`rf_id`);

--
-- Indexes for table `seo`
--
ALTER TABLE `seo`
  ADD PRIMARY KEY (`seo_id`),
  ADD KEY `FK_seo_seo_seo_id` (`seo_pid`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`slide_id`);

--
-- Indexes for table `struct_data`
--
ALTER TABLE `struct_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_struct_data` (`table_id`,`name`),
  ADD KEY `FK_struct_data_struct_field_field_id` (`field_id`);

--
-- Indexes for table `struct_field`
--
ALTER TABLE `struct_field`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `struct_field_param`
--
ALTER TABLE `struct_field_param`
  ADD PRIMARY KEY (`fp_id`),
  ADD KEY `FK_struct_field_param_struct_field_field_id` (`field_id`),
  ADD KEY `FK_struct_field_param_struct_field_field_id2` (`type_id`);

--
-- Indexes for table `struct_param`
--
ALTER TABLE `struct_param`
  ADD PRIMARY KEY (`param_id`),
  ADD KEY `FK_struct_param_struct_field_field_id` (`field_id`),
  ADD KEY `FK_struct_param_struct_param_param_id` (`param_pid`),
  ADD KEY `FK_struct_param_struct_table_table_id` (`table_id`);

--
-- Indexes for table `struct_table`
--
ALTER TABLE `struct_table`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `UK_struct_table_name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UK_user_login` (`login`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FK_user_user_role_role_id` (`role_id`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`auth_id`),
  ADD KEY `user_auth_user_user_id_fk` (`user_id`),
  ADD KEY `user_auth_time_last_login_index` (`time_last_login`),
  ADD KEY `user_auth_token_time_expires_index` (`token`,`time_expires`);

--
-- Indexes for table `user_priv`
--
ALTER TABLE `user_priv`
  ADD PRIMARY KEY (`priv_id`);

--
-- Indexes for table `user_priv_personal`
--
ALTER TABLE `user_priv_personal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_role_priv_user_priv_priv_id2` (`priv_id`),
  ADD KEY `FK_user_role_priv_user_role_role_id2` (`user_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `FK_user_role_user_priv_priv_id` (`priv_id`);

--
-- Indexes for table `user_role_priv`
--
ALTER TABLE `user_role_priv`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_role_priv_user_priv_priv_id` (`priv_id`),
  ADD KEY `FK_user_role_priv_user_role_role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `menu_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `admin_widget`
--
ALTER TABLE `admin_widget`
  MODIFY `widget_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `component`
--
ALTER TABLE `component`
  MODIFY `component_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `component_param`
--
ALTER TABLE `component_param`
  MODIFY `cp_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `content_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `content_template`
--
ALTER TABLE `content_template`
  MODIFY `template_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `content_template_param`
--
ALTER TABLE `content_template_param`
  MODIFY `ctp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=944;

--
-- AUTO_INCREMENT for table `cron`
--
ALTER TABLE `cron`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cron_log`
--
ALTER TABLE `cron_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- AUTO_INCREMENT for table `mail_log`
--
ALTER TABLE `mail_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migration`
--
ALTER TABLE `migration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `module_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `module_item`
--
ALTER TABLE `module_item`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `module_param`
--
ALTER TABLE `module_param`
  MODIFY `mp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_file`
--
ALTER TABLE `review_file`
  MODIFY `rf_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo`
--
ALTER TABLE `seo`
  MODIFY `seo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `slide_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `struct_data`
--
ALTER TABLE `struct_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT for table `struct_field`
--
ALTER TABLE `struct_field`
  MODIFY `field_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `struct_field_param`
--
ALTER TABLE `struct_field_param`
  MODIFY `fp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `struct_param`
--
ALTER TABLE `struct_param`
  MODIFY `param_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `struct_table`
--
ALTER TABLE `struct_table`
  MODIFY `table_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19408;

--
-- AUTO_INCREMENT for table `user_auth`
--
ALTER TABLE `user_auth`
  MODIFY `auth_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `user_priv`
--
ALTER TABLE `user_priv`
  MODIFY `priv_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_priv_personal`
--
ALTER TABLE `user_priv_personal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_role_priv`
--
ALTER TABLE `user_role_priv`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD CONSTRAINT `FK_admin_menu_admin_menu_menu_id` FOREIGN KEY (`menu_pid`) REFERENCES `admin_menu` (`menu_id`),
  ADD CONSTRAINT `FK_admin_menu_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`);

--
-- Constraints for table `admin_widget`
--
ALTER TABLE `admin_widget`
  ADD CONSTRAINT `FK_admin_widget_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `component_param`
--
ALTER TABLE `component_param`
  ADD CONSTRAINT `FK_component_param_component_component_id` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_component_param_component_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `component_param` (`cp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_component_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_content_template_null_fk` FOREIGN KEY (`template_id`) REFERENCES `content_template` (`template_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `content_template_param`
--
ALTER TABLE `content_template_param`
  ADD CONSTRAINT `FK_content_template_param_content_template_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `content_template_param` (`ctp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_content_template_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_content_template_param_template_template_id` FOREIGN KEY (`template_id`) REFERENCES `content_template` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `FK_menu_component_component_id` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_menu_menu_menu_id` FOREIGN KEY (`menu_pid`) REFERENCES `menu` (`menu_id`);

--
-- Constraints for table `module_item`
--
ALTER TABLE `module_item`
  ADD CONSTRAINT `FK_module_item_module_module_id` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`);

--
-- Constraints for table `module_param`
--
ALTER TABLE `module_param`
  ADD CONSTRAINT `FK_module_param_module_module_id` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_module_param_module_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `module_param` (`mp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_module_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seo`
--
ALTER TABLE `seo`
  ADD CONSTRAINT `FK_seo_seo_seo_id` FOREIGN KEY (`seo_pid`) REFERENCES `seo` (`seo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `struct_data`
--
ALTER TABLE `struct_data`
  ADD CONSTRAINT `FK_struct_data_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`),
  ADD CONSTRAINT `FK_struct_data_struct_table_table_id` FOREIGN KEY (`table_id`) REFERENCES `struct_table` (`table_id`);

--
-- Constraints for table `struct_field_param`
--
ALTER TABLE `struct_field_param`
  ADD CONSTRAINT `FK_struct_field_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_struct_field_param_struct_field_field_id2` FOREIGN KEY (`type_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `struct_param`
--
ALTER TABLE `struct_param`
  ADD CONSTRAINT `FK_struct_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`),
  ADD CONSTRAINT `FK_struct_param_struct_param_param_id` FOREIGN KEY (`param_pid`) REFERENCES `struct_param` (`param_id`),
  ADD CONSTRAINT `FK_struct_param_struct_table_table_id` FOREIGN KEY (`table_id`) REFERENCES `struct_table` (`table_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_user_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`);

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `user_auth_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_priv_personal`
--
ALTER TABLE `user_priv_personal`
  ADD CONSTRAINT `FK_user_role_priv_user_priv_priv_id2` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`),
  ADD CONSTRAINT `FK_user_role_priv_user_role_role_id2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_user_role_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`);

--
-- Constraints for table `user_role_priv`
--
ALTER TABLE `user_role_priv`
  ADD CONSTRAINT `FK_user_role_priv_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`),
  ADD CONSTRAINT `FK_user_role_priv_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
