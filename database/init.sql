-- MariaDB dump 10.19  Distrib 10.5.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: simplex
-- ------------------------------------------------------
-- Server version	10.5.18-MariaDB-0+deb11u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_pid` int(11) DEFAULT NULL,
  `priv_id` int(11) NOT NULL,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`menu_id`),
  KEY `FK_admin_menu_admin_menu_menu_id` (`menu_pid`),
  KEY `FK_admin_menu_user_priv_priv_id` (`priv_id`),
  CONSTRAINT `FK_admin_menu_admin_menu_menu_id` FOREIGN KEY (`menu_pid`) REFERENCES `admin_menu` (`menu_id`),
  CONSTRAINT `FK_admin_menu_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=910;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,NULL,1,1,'Администрирование','/admin/admin/','','home',0),(2,1,1,1,'Меню','/admin/admin/menu/','admin_menu','share',0),(3,1,2,3,'Роли','/admin/admin/role/','user_role','users',0),(4,1,1,6,'Привилегии','/admin/admin/priv/','user_priv','users',0),(5,1,2,2,'Пользователи','/admin/admin/user/','user','user',0),(6,1,2,7,'Права доступа','/admin/admin/rights/','user_role_priv','users',0),(7,NULL,2,2,'Сайт','/admin/site/','','briefcase',0),(9,7,1,1,'Меню','/admin/site/menu/','menu','share',0),(10,7,1,2,'Модули','/admin/site/module/','module_item','grid ',0),(11,1,1,3,'Компоненты','/admin/admin/component/','component','layers',0),(12,NULL,2,3,'Материалы','/admin/content/','content','docs',0),(14,7,2,8,'Настройки','/admin/site/settings/','settings','settings',0),(15,7,1,9,'SEO','/admin/site/seo/','seo','bar-chart',0),(22,1,1,10,'Структура','/admin/admin/structure/','struct_data','puzzle',0),(23,22,1,2,'Типы полей','/admin/admin/structure/fields/','struct_field','puzzle',0),(24,22,1,1,'Таблицы','/admin/admin/structure/tables/','struct_table','puzzle',0),(25,22,1,3,'Параметры','/admin/admin/structure/params/','struct_param','puzzle',0),(26,22,1,5,'Поля в таблицах','/admin/admin/structure/','struct_data','puzzle',0),(27,22,1,4,'Параметры полей','/admin/admin/structure/field_param/','struct_field_param','puzzle',0),(28,1,1,5,'Модули','/admin/admin/module/','module','grid',0),(29,1,1,5,'Параметры модулей','/admin/admin/module/param/','module_param','grid',0),(30,1,1,4,'Параметры компонентов','/admin/admin/component_param/','component_param','layers',0),(31,NULL,3,57,'Аккаунт','/admin/account/','','user',1),(34,1,1,58,'Установка дополнений','/admin/install/','','disc',0),(35,1,1,54,'Cron','/admin/cron/','cron','speedometer',0),(36,1,2,8,'Персональные права','/admin/user_priv_personal/','user_priv_personal','users',0),(37,1,1,50,'Шаблоны страниц','/admin/admin/content_template/','content_template',' fa fa-html5',0),(38,1,1,51,'Параметры шаблонов','/admin/admin/content_template_param/','content_template_param',' fa fa-html5',0);
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_widget`
--

DROP TABLE IF EXISTS `admin_widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_widget` (
  `widget_id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(11) NOT NULL DEFAULT 1,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `priv_id` int(11) DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`widget_id`),
  KEY `FK_admin_widget_user_priv_priv_id` (`priv_id`),
  CONSTRAINT `FK_admin_widget_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_widget`
--

LOCK TABLES `admin_widget` WRITE;
/*!40000 ALTER TABLE `admin_widget` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_widget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callback`
--

DROP TABLE IF EXISTS `callback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callback` (
  `callback_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `comments` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`callback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callback`
--

LOCK TABLES `callback` WRITE;
/*!40000 ALTER TABLE `callback` DISABLE KEYS */;
/*!40000 ALTER TABLE `callback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `component`
--

DROP TABLE IF EXISTS `component`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `component` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `params` longtext DEFAULT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=4096;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `component`
--

LOCK TABLES `component` WRITE;
/*!40000 ALTER TABLE `component` DISABLE KEYS */;
INSERT INTO `component` VALUES (1,'\\App\\Extensions\\Content\\Content','Материалы','a:2:{s:6:\"аег\";s:1:\"q\";s:6:\"groupp\";a:1:{s:4:\"jopa\";s:1:\"2\";}}'),(2,'\\App\\Extensions\\Auth\\Auth','Авторизация',NULL);
/*!40000 ALTER TABLE `component` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `component_param`
--

DROP TABLE IF EXISTS `component_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `component_param` (
  `cp_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` int(11) NOT NULL,
  `param_pid` int(11) DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `npp` int(11) NOT NULL DEFAULT 0,
  `help` varchar(255) NOT NULL DEFAULT '',
  `params` longtext DEFAULT NULL,
  PRIMARY KEY (`cp_id`),
  KEY `FK_component_param_component_component_id` (`component_id`),
  KEY `FK_component_param_component_param_mp_id` (`param_pid`),
  KEY `FK_component_param_struct_field_field_id` (`field_id`),
  CONSTRAINT `FK_component_param_component_component_id` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_component_param_component_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `component_param` (`cp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_component_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `component_param`
--

LOCK TABLES `component_param` WRITE;
/*!40000 ALTER TABLE `component_param` DISABLE KEYS */;
/*!40000 ALTER TABLE `component_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `short` text DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `params` longtext DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  KEY `content_content_template_null_fk` (`template_id`),
  CONSTRAINT `content_content_template_null_fk` FOREIGN KEY (`template_id`) REFERENCES `content_template` (`template_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1638;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,NULL,1,'2020-12-20','Главная','/','/','','','a:0:{}',NULL,NULL,1);
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_template`
--

DROP TABLE IF EXISTS `content_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `template_path` varchar(255) NOT NULL COMMENT 'relative path from Extensions/Content/tpl. for example "/mainPage.tpl" or "/news/item.tpl"',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_template`
--

LOCK TABLES `content_template` WRITE;
/*!40000 ALTER TABLE `content_template` DISABLE KEYS */;
INSERT INTO `content_template` VALUES (1,'Главная','index.tpl');
/*!40000 ALTER TABLE `content_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_template_param`
--

DROP TABLE IF EXISTS `content_template_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_template_param` (
  `ctp_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `param_pid` int(11) DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `npp` int(11) NOT NULL DEFAULT 0,
  `help` varchar(255) NOT NULL DEFAULT '',
  `params` longtext DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`ctp_id`),
  KEY `FK_content_template_param_template_template_id` (`template_id`),
  KEY `FK_content_template_param_content_template_param_mp_id` (`param_pid`),
  KEY `FK_content_template_param_struct_field_field_id` (`field_id`),
  CONSTRAINT `FK_content_template_param_content_template_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `content_template_param` (`ctp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_content_template_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_content_template_param_template_template_id` FOREIGN KEY (`template_id`) REFERENCES `content_template` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=826 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_template_param`
--

LOCK TABLES `content_template_param` WRITE;
/*!40000 ALTER TABLE `content_template_param` DISABLE KEYS */;
INSERT INTO `content_template_param` VALUES (1,1,NULL,'left',1,'seo_title_1','SEO заголовок 1',10000,'','a:0:{}',NULL,'SEO'),(2,1,NULL,'left',9,'seo_text_1','SEO текст 1',10000,'','a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}',NULL,'SEO'),(3,1,NULL,'left',1,'seo_title_2','SEO заголовок 2',10000,'','a:0:{}',NULL,'SEO'),(4,1,NULL,'left',9,'seo_text_2','SEO текст 2',10000,'','a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}',NULL,'SEO'),(5,1,NULL,'right',1,'meta_title','Заголовок',11000,'','a:0:{}',NULL,'Мета'),(6,1,NULL,'right',9,'meta_kw','Ключевые слова',11000,'','a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}',NULL,'Мета'),(7,1,NULL,'right',9,'meta_de','Описание',11000,'','a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}',NULL,'Мета');
/*!40000 ALTER TABLE `content_template_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cron`
--

DROP TABLE IF EXISTS `cron`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(11) NOT NULL DEFAULT 1,
  `timing` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ext_id` int(11) DEFAULT NULL COMMENT 'ID компонента',
  `module_id` int(11) DEFAULT NULL COMMENT 'ID модуля, НЕ экземпляра модуля',
  `plugin_name` varchar(50) DEFAULT NULL COMMENT 'Название плагина',
  `action` varchar(255) NOT NULL,
  `cparams` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=16384;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cron`
--

LOCK TABLES `cron` WRITE;
/*!40000 ALTER TABLE `cron` DISABLE KEYS */;
/*!40000 ALTER TABLE `cron` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cron_log`
--

DROP TABLE IF EXISTS `cron_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `result` varchar(2047) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=39;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cron_log`
--

LOCK TABLES `cron_log` WRITE;
/*!40000 ALTER TABLE `cron_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cron_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` enum('login_attempt','login_success') NOT NULL COMMENT 'Попытка входа в панель управления;;Успешный вход в панель управления',
  `ip` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=5461 COMMENT='Журнал входов в админку. По умолчанию отключен. Включать в AdminPlugLog';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_log`
--

DROP TABLE IF EXISTS `mail_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `success` int(11) NOT NULL COMMENT '1 - отправлено, 0 - не отправлено, ошибки при отправке',
  `mail_from` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fail_info` varchar(255) DEFAULT NULL,
  `reply_to` varchar(255) DEFAULT NULL,
  `transport` int(11) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_log`
--

LOCK TABLES `mail_log` WRITE;
/*!40000 ALTER TABLE `mail_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_pid` int(11) DEFAULT NULL,
  `component_id` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `hidden` int(11) NOT NULL DEFAULT 0,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `FK_menu_component_component_id` (`component_id`),
  KEY `FK_menu_menu_menu_id` (`menu_pid`),
  CONSTRAINT `FK_menu_component_component_id` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE SET NULL,
  CONSTRAINT `FK_menu_menu_menu_id` FOREIGN KEY (`menu_pid`) REFERENCES `menu` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1260;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,NULL,NULL,1,0,1,'Главная','/');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=2730;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('site','admin') NOT NULL DEFAULT 'site',
  `postexec` int(11) NOT NULL DEFAULT 0 COMMENT 'Выполнять после контента',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=2730;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'\\App\\Extensions\\Menu\\Menu','Меню','site',0),(2,'App\\Extensions\\Content\\ModuleContent','Список материалов','site',0),(3,'\\App\\Extensions\\Breadcrumbs\\Breadcrumbs','Хлебные крошки','site',1),(4,'\\App\\Extensions\\Block\\Block','Текстовый блок','site',0),(7,'Breadcrumbs','Admin. Хлебные крошки','admin',0),(8,'Menu','Admin. Меню','admin',0),(9,'\\App\\Extensions\\Slider\\ModuleSlider','Слайдер','site',0),(10,'\\App\\Extensions\\Callback\\ModuleCallback','Обратный звонок','site',0),(11,'Account','Admin. Аккаунт','admin',0),(12,'Install','Admin. Репозиторий','admin',0),(13,'\\App\\Extensions\\Code\\ModuleCode','Код','site',0),(14,'\\App\\Extensions\\Form\\ModuleForm','Форма','site',0);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_item`
--

DROP TABLE IF EXISTS `module_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `posname` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `params` longtext DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `FK_module_item_position_position_id` (`posname`),
  KEY `FK_module_item_module_module_id` (`module_id`),
  CONSTRAINT `FK_module_item_module_module_id` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1489;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_item`
--

LOCK TABLES `module_item` WRITE;
/*!40000 ALTER TABLE `module_item` DISABLE KEYS */;
INSERT INTO `module_item` VALUES (1,1,NULL,'mainmenu',1,0,'Меню главное','a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"0\";}'),(3,3,NULL,'content-outer-before',1,0,'Хлебные крошки','a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}'),(4,4,NULL,'footer',1,102,'Копирайт','a:2:{s:7:\"content\";s:120:\"<p>2012 &copy; ДСМ Урал<br /> г. Пермь, ул. Екатерининская 59, тел.: (342) 204-40-64</p>\";s:8:\"is_title\";s:1:\"0\";}'),(8,4,NULL,'footer',1,111,'Счетчики','a:2:{s:7:\"content\";s:131:\"<p><img src=\"../../../theme/default/img/liveinternet.png\" alt=\"\" /> <img src=\"../../../theme/default/img/metrika.png\" alt=\"\" /></p>\";s:8:\"is_title\";s:1:\"0\";}'),(9,4,NULL,'header-right',1,0,'Контакты вверху','a:4:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:7:\"content\";s:197:\"<div class=\"phone\"><span>(342)</span> 204-40-64</div>\r\n<div style=\"margin-top: 4px; font-size: 12px;\">г. Пермь, <a href=\"/contacts/\">ул. Екатерининская 59</a>, оф. 204</div>\";}'),(10,1,NULL,'footer',1,101,'Меню сайта внизу страницы','a:1:{s:8:\"is_title\";s:1:\"0\";}'),(11,4,NULL,'footer',1,103,'Ссылка на разработчика','a:2:{s:7:\"content\";s:126:\"<p><a href=\"http://internet-menu.ru\">Создание сайта</a> &mdash; веб-студия Интернет Меню</p>\";s:8:\"is_title\";s:1:\"0\";}'),(14,1,NULL,'aside',1,51,'Меню дочернее','a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"1\";}'),(15,7,NULL,'breadcrumbs',1,0,'Хлебные крошки',NULL),(16,8,NULL,'menu',1,0,'Меню','a:6:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:1:\"0\";s:8:\"is_child\";s:1:\"0\";}'),(17,11,31,'content-before',1,116,'Admin. Аккаунт','a:2:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"1\";}'),(18,12,34,'content-before',1,117,'Admin. Репозиторий','a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}');
/*!40000 ALTER TABLE `module_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_param`
--

DROP TABLE IF EXISTS `module_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_param` (
  `mp_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `param_pid` int(11) DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `npp` int(11) NOT NULL DEFAULT 0,
  `help` varchar(255) NOT NULL DEFAULT '',
  `params` longtext DEFAULT NULL,
  PRIMARY KEY (`mp_id`),
  KEY `FK_module_param_module_module_id` (`module_id`),
  KEY `FK_module_param_module_param_mp_id` (`param_pid`),
  KEY `FK_module_param_struct_field_field_id` (`field_id`),
  CONSTRAINT `FK_module_param_module_module_id` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_module_param_module_param_mp_id` FOREIGN KEY (`param_pid`) REFERENCES `module_param` (`mp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_module_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=819;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_param`
--

LOCK TABLES `module_param` WRITE;
/*!40000 ALTER TABLE `module_param` DISABLE KEYS */;
INSERT INTO `module_param` VALUES (1,2,NULL,'right',NULL,'content_data','Вывод данных',0,'',NULL),(2,2,1,'',2,'content_id','Раздел',0,'','a:1:{s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:7:\"content\";s:6:\"fk_key\";s:10:\"content_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(3,2,1,'',1,'tpl','Шаблон вывода',1,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:12:\"mod_list.tpl\";}}'),(4,2,1,'',2,'cnt_limit','Количество',2,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"3\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(5,2,NULL,'right',NULL,'module_param_view','Внешний вид',1,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(6,2,5,'',3,'date','Дата',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(7,2,5,'',3,'short','Анонс',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(8,2,5,'',3,'more','Кнопка далее',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(9,2,5,'',1,'more_text','Кнопка далее',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:23:\"Читать далее\";}}'),(10,4,NULL,'left',NULL,'module_param_content','Контент',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(11,4,10,'',9,'content','Текст',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),(12,8,NULL,'right',2,'max_level','Макс. ур. вложенности',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(13,8,NULL,'right',2,'start_id','Выводить подразделы',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(14,8,NULL,'right',3,'is_child','Дочернее меню',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(15,1,NULL,'',2,'start_id','Начальный пункт меню',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(16,1,NULL,'',2,'max_level','Максимальный уровень вложенности',0,'0 - без вложенных меню','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(17,1,NULL,'',3,'is_child','Выводить дочернее меню',0,'В текущем разделе выводить внутренние подразделы','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}}'),(18,10,NULL,'left',1,'subject','Заголовок письма',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:29:\"Обратный звонок\";}}'),(19,10,NULL,'left',1,'email','Email',1,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(20,10,NULL,'left',9,'message','Текст письма',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:120:\"Поступила заявка на обратный звонок с сайта: Имя: {name} Телефон: {phone}\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"1\";s:11:\"editor_full\";s:1:\"0\";}}'),(21,13,NULL,'left',NULL,'module_param_content','Контент',0,'','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}'),(22,13,21,'',9,'content','Текст',0,'','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(24,14,NULL,'right',1,'submit_button_text','Кнопка',1,'Текст на кнопке \"отправить\"','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:18:\"Отправить\";}}'),(25,14,NULL,'right',9,'fields','Поля формы',2,'формат: название:тип:{обязательно 1}, пример ФИО:string, Почта:email:1','a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(26,14,NULL,'right',1,'form_tpl','Шаблон формы',4,'например awesomeForm.tpl, складывать в Extensions/Form/tpl','a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}');
/*!40000 ALTER TABLE `module_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plug_cache`
--

DROP TABLE IF EXISTS `plug_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plug_cache` (
  `cache_key` char(255) NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `value` varchar(21000) DEFAULT NULL,
  `expires` int(11) DEFAULT 3600 COMMENT 'В секундах',
  PRIMARY KEY (`cache_key`) USING BTREE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=63776;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plug_cache`
--

LOCK TABLES `plug_cache` WRITE;
/*!40000 ALTER TABLE `plug_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `plug_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo`
--

DROP TABLE IF EXISTS `seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo` (
  `seo_id` int(11) NOT NULL AUTO_INCREMENT,
  `seo_pid` int(11) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `metatags` text DEFAULT NULL,
  PRIMARY KEY (`seo_id`),
  KEY `FK_seo_seo_seo_id` (`seo_pid`),
  CONSTRAINT `FK_seo_seo_seo_id` FOREIGN KEY (`seo_pid`) REFERENCES `seo` (`seo_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=16384;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo`
--

LOCK TABLES `seo` WRITE;
/*!40000 ALTER TABLE `seo` DISABLE KEYS */;
INSERT INTO `seo` VALUES (1,NULL,'/','','','',NULL);
/*!40000 ALTER TABLE `seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` enum('int','string','bool','enum') NOT NULL DEFAULT 'string',
  `enum_values` varchar(255) DEFAULT NULL COMMENT 'key::value;;key::value',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=3276;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,0,'Название сайта','site_name','Simflex','string',NULL),(6,0,'Email','email','info@site.ru','string',NULL),(7,0,'Телефон','phone','8 800 100-00-00','string',NULL),(9,11,'Часы работы','workhours','ПН-ВС — с 09:00 до 18:00','string',NULL),(18,0,'Email для получения заявок','form_email','','string',NULL),(23,0,'Адрес','address','','string',NULL);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slider`
--

DROP TABLE IF EXISTS `slider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slider` (
  `slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `npp` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `text` varchar(2047) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `href` varchar(1023) NOT NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=16384;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slider`
--

LOCK TABLES `slider` WRITE;
/*!40000 ALTER TABLE `slider` DISABLE KEYS */;
/*!40000 ALTER TABLE `slider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_data`
--

DROP TABLE IF EXISTS `struct_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `npp` int(11) NOT NULL DEFAULT 0,
  `table_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `help` varchar(255) NOT NULL DEFAULT '',
  `placeholder` varchar(255) NOT NULL DEFAULT '',
  `params` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_struct_data` (`table_id`,`name`),
  KEY `FK_struct_data_struct_field_field_id` (`field_id`),
  CONSTRAINT `FK_struct_data_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`),
  CONSTRAINT `FK_struct_data_struct_table_table_id` FOREIGN KEY (`table_id`) REFERENCES `struct_table` (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=336 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=805;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_data`
--

LOCK TABLES `struct_data` WRITE;
/*!40000 ALTER TABLE `struct_data` DISABLE KEYS */;
INSERT INTO `struct_data` VALUES (1,0,1,2,'table_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(2,0,1,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(3,0,2,2,'field_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(4,0,2,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(5,0,2,1,'class','Класс','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(6,0,3,2,'id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(7,1,3,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(8,2,3,2,'table_id','Таблица','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_table.table_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_table\";s:6:\"fk_key\";s:8:\"table_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(9,3,3,2,'field_id','Тип данных','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_field.field_id.name\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(10,4,3,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(11,5,3,1,'label','Ярлык','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(12,0,4,2,'param_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(13,2,4,2,'param_pid','Родитель','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_param.param_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_param\";s:6:\"fk_key\";s:8:\"param_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(14,1,4,2,'table_id','Таблица','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_table.table_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_table\";s:6:\"fk_key\";s:8:\"table_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(15,3,4,2,'field_id','Тип данных','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:26:\"struct_field.field_id.name\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(16,4,4,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(17,5,4,1,'label','Заголовок','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(18,8,3,1,'params','Параметры','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(22,1,5,2,'menu_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(23,2,5,2,'menu_pid','Родитель','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:23:\"admin_menu.menu_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:10:\"admin_menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(24,3,5,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(25,4,5,2,'priv_id','Привилегия','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(26,5,5,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(27,6,5,1,'link','Ссылка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(28,7,5,1,'model','Модель','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(29,1,6,2,'priv_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(30,2,6,3,'active','Активно','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(31,3,6,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(32,4,6,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(33,5,6,1,'comment','Комментарий','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(34,1,7,1,'role_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(35,5,7,2,'priv_id','Привилегия','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(36,3,7,3,'active','Активно','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(37,4,7,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(38,6,7,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(39,1,8,2,'user_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";}}'),(40,3,8,2,'role_id','Роль','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_role.role_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_role\";s:6:\"fk_key\";s:7:\"role_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(41,2,8,3,'active','Активно','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(42,4,8,1,'login','Логин','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(43,5,8,6,'password','Пароль','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(44,6,8,1,'hash','Хеш','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(45,1,9,2,'id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(46,2,9,2,'role_id','Роль','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_role.role_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_role\";s:6:\"fk_key\";s:7:\"role_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(47,3,9,2,'priv_id','Привилегия','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:22:\"user_priv.priv_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(48,1,10,2,'menu_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(49,4,10,2,'menu_pid','Родитель','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:17:\"menu.menu_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(50,5,10,2,'component_id','Компонент','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:27:\"component.component_id.name\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(51,1,10,3,'active','Активно','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(52,2,10,3,'hidden','Скрыть','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(53,3,10,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(54,7,10,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(55,8,10,1,'link','Ссылка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(56,1,11,2,'component_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(57,2,11,1,'class','Класс','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(58,3,11,1,'name','Название','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(59,1,12,2,'content_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(60,3,12,2,'pid','Родитель','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:7:\"content\";s:6:\"fk_key\";s:10:\"content_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(61,2,12,3,'active','Активно','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(62,4,12,7,'date','Дата','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(63,5,12,1,'title','Заголовок','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(64,6,12,4,'alias','Алиас','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:6:\"source\";s:5:\"title\";}}'),(65,7,12,5,'path','Путь','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"1\";s:10:\"style_cell\";s:0:\"\";}}'),(66,8,12,9,'short','Коротко','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(67,9,12,9,'text','Текст','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),(68,10,12,1,'params','Параметры','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(70,8,5,1,'icon','Иконка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(71,0,22,2,'setting_id','ID','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(72,0,22,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"90\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(73,0,22,1,'name','Наименование','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(74,0,22,1,'alias','Алиас','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(75,0,22,9,'value','Значение','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(76,6,4,1,'pos','Позиция','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(77,7,4,1,'default_value','Значение по умолчанию','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(78,0,23,2,'fp_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(79,1,23,2,'field_id','Тип поля','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(80,2,23,1,'name','Наименование','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(81,3,23,1,'label','Ярлык','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(82,4,23,2,'type_id','Тип параметра','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(84,0,24,2,'module_id','ID','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(85,1,24,1,'class','Класс','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(86,0,24,1,'name','Наименование','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(87,9,24,14,'type','Тип','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(88,9,23,1,'help','Подсказка','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(89,6,3,1,'help','Подсказка','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(90,7,3,1,'placeholder','Placeholder','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(91,2,1,1,'order_by','Сортировать по','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(92,3,1,3,'order_desc','По убыванию','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(93,-2,25,2,'item_id','ID','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"50\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(94,2,25,2,'module_id','Модуль','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:20:\"onChangeModule(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:6:\"module\";s:6:\"fk_key\";s:9:\"module_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(95,3,25,2,'menu_id','Меню','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(96,0,25,1,'posname','Позиция','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(97,-2,25,3,'active','Активно','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"90\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(98,-1,25,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"90\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(99,0,25,1,'name','Наименование','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(100,4,25,9,'params','Параметры','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(101,6,1,16,'loadstruct','Загрузить структуру','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:8:\"subquery\";s:0:\"\";s:4:\"text\";s:18:\"Загрузить\";s:4:\"href\";s:38:\"?action=loadstruct&table_id={table_id}\";}}'),(225,1,26,2,'seo_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(226,2,26,2,'seo_pid','PID','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:1;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";i:1;s:8:\"fk_table\";s:3:\"seo\";s:6:\"fk_key\";s:6:\"seo_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";b:1;}}'),(227,3,26,1,'link','Ссылка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(228,4,26,1,'title','Заголовок','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(229,5,26,9,'description','Описание','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:0;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(230,6,26,9,'keywords','Keywords','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:0;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(255,1,28,2,'mp_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(256,2,28,2,'module_id','Модуль','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:6:\"module\";s:6:\"fk_key\";s:9:\"module_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(257,3,28,2,'param_pid','PID','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"module_param\";s:6:\"fk_key\";s:5:\"mp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(258,4,28,2,'field_id','Тип параметра','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(259,5,28,1,'name','Название','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(260,6,28,1,'label','Ярлык','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:1;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(261,1,28,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(262,12,28,1,'params','Параметры','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(263,2,28,1,'position','Позиция','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"100\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(264,12,4,1,'params','Параметры','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(268,12,23,1,'default_value','Значение по умолчанию','','','a:1:{s:4:\"main\";a:9:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";s:8:\"onchange\";s:0:\"\";}}'),(269,7,8,1,'hash_admin','Admin. Хеш','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:2:\"fk\";s:0:\"\";}}'),(270,1,29,2,'cp_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(271,2,29,2,'component_id','Компонент','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(272,3,29,2,'param_pid','PID','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:1;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";i:1;s:8:\"fk_table\";s:15:\"component_param\";s:6:\"fk_key\";s:5:\"cp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";b:1;}}'),(273,4,29,1,'position','Позиция','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(274,5,29,2,'field_id','Тип параметра','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(275,6,29,1,'name','Название','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(276,7,29,1,'label','Ярлык','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:1;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(277,1,29,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(278,9,29,1,'help','Подсказка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(279,10,29,1,'params','Параметры','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(280,9,11,1,'params','Параметры','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(281,7,28,1,'help','Подсказка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(282,7,1,2,'priv_add','Привилегия добавления','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(283,7,1,2,'priv_delete','Привилегия удаления','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(284,7,1,2,'priv_edit','Привилегия изменения','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"user_priv\";s:6:\"fk_key\";s:7:\"priv_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(285,3,30,3,'active','Активно','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";}}'),(286,6,30,1,'href','Ссылка','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(287,1,30,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(288,5,30,11,'photo','Фото','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"110\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:4:\"path\";s:6:\"slider\";s:5:\"small\";s:0:\"\";s:6:\"medium\";s:0:\"\";s:5:\"large\";s:9:\"960x340x1\";}}'),(289,1,30,2,'slide_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(290,4,30,9,'text','Текст','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}'),(291,1,31,2,'callback_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(292,2,31,8,'datetime','Время','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(293,4,31,1,'name','ФИО','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(294,3,31,1,'phone','Телефон','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"200\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(295,3,32,14,'action','Действие','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";}}'),(296,5,32,1,'browser','Браузер','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(297,6,32,1,'data','Информация','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(298,2,32,8,'datetime','Время','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(299,4,32,1,'ip','IP адрес','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(300,1,32,2,'log_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(301,6,33,1,'action','Действие','Метод компонента или плагина','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(302,1,33,3,'active','Активно','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"85\";s:12:\"defaultValue\";s:1:\"1\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(303,7,33,9,'cparams','Параметры','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(304,4,33,2,'ext_id','Расширение','ID компонента','','a:1:{s:4:\"main\";a:15:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:9:\"component\";s:6:\"fk_key\";s:12:\"component_id\";s:8:\"fk_label\";s:5:\"class\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(305,1,33,2,'id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(306,3,33,1,'name','Название','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(307,5,33,1,'plugin_name','Плагин','Название класса плагина','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"180\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(308,2,33,1,'timing','Время','Как в *nix crontab. Например */10 * * * *','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"160\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(309,8,8,1,'email','Email','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"140\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(310,9,8,1,'name','ФИО','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"150\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(311,5,31,9,'comments','Комментарий','','','a:1:{s:4:\"main\";a:12:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(312,8,24,3,'postexec','Выполнять после контента','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";}}'),(313,11,12,11,'photo','Фото','','','a:1:{s:4:\"main\";a:14:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"130\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:4:\"path\";s:7:\"content\";s:5:\"small\";s:7:\"200x150\";s:6:\"medium\";s:0:\"\";s:5:\"large\";s:7:\"800x600\";}}'),(314,1,22,14,'type','Тип','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:6:\"string\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"1\";s:10:\"style_cell\";s:0:\"\";}}'),(315,2,22,1,'enum_values','Значения enum','','','a:1:{s:4:\"main\";a:10:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";}}'),(316,1,34,1,'id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(317,2,34,1,'user_id','Пользователь','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"250\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(318,3,34,1,'priv_id','Привилегия','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"1\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(319,1,35,1,'template_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(320,2,35,1,'template_name','Название','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(321,3,35,1,'template_path','Path','relative path from Extensions/Content/tpl. for example \"mainPage.tpl\" or \"news/item.tpl\"','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(322,0,12,2,'template_id','Шаблон','','','a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:3:\"120\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:16:\"content_template\";s:6:\"fk_key\";s:11:\"template_id\";s:8:\"fk_label\";s:13:\"template_name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(323,1,36,1,'ctp_id','ID','','','a:1:{s:4:\"main\";a:8:{s:2:\"pk\";s:1:\"1\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:2:\"60\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:5:\"is_fk\";s:0:\"\";}}'),(324,2,36,2,'template_id','Шаблон','','','a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"1\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:16:\"content_template\";s:6:\"fk_key\";s:11:\"template_id\";s:8:\"fk_label\";s:13:\"template_name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(325,3,36,2,'param_pid','PID','','','a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:22:\"content_template_param\";s:6:\"fk_key\";s:6:\"ctp_id\";s:8:\"fk_label\";s:5:\"label\";s:9:\"fk_is_pid\";s:1:\"1\";}}'),(326,4,36,1,'position','Позиция','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(327,5,36,2,'field_id','Тип','','','a:1:{s:4:\"main\";a:16:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:19:\"onChangeField(this)\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:12:\"struct_field\";s:6:\"fk_key\";s:8:\"field_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}'),(328,6,36,1,'name','Название','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(329,7,36,1,'label','Ярлык','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";i:1;s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(330,1,36,15,'npp','№ п/п','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:2:\"80\";s:12:\"defaultValue\";s:1:\"0\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(331,9,36,1,'help','Help','','','a:1:{s:4:\"main\";a:7:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";i:0;s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"1\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";}}'),(332,10,36,9,'params','Параметры','','','a:1:{s:4:\"main\";a:13:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"1\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}'),(333,99,1,1,'class','Класс драйвера','с namespace. например \\Simflex\\Extensions\\Content\\Admin\\AdminContent','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(334,15,36,1,'default_value','Значение по умолчанию','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"1\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}'),(335,0,36,1,'group_name','Группа','','','a:1:{s:4:\"main\";a:11:{s:2:\"pk\";s:1:\"0\";s:3:\"e2n\";s:1:\"0\";s:6:\"hidden\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:12:\"defaultValue\";s:0:\"\";s:8:\"required\";s:1:\"0\";s:6:\"filter\";s:1:\"0\";s:8:\"onchange\";s:0:\"\";s:8:\"readonly\";s:1:\"0\";s:10:\"style_cell\";s:0:\"\";s:12:\"screen_width\";s:1:\"0\";}}');
/*!40000 ALTER TABLE `struct_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_field`
--

DROP TABLE IF EXISTS `struct_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1489;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_field`
--

LOCK TABLES `struct_field` WRITE;
/*!40000 ALTER TABLE `struct_field` DISABLE KEYS */;
INSERT INTO `struct_field` VALUES (1,'Строка','\\Simflex\\Admin\\Fields\\FieldString'),(2,'Целое число','\\Simflex\\Admin\\Fields\\FieldInt'),(3,'Булевая переменная','\\Simflex\\Admin\\Fields\\FieldBool'),(4,'Алиас','\\Simflex\\Admin\\Fields\\FieldAlias'),(5,'Url-путь','\\Simflex\\Admin\\Fields\\FieldPath'),(6,'Пароль','\\Simflex\\Admin\\Fields\\FieldPassword'),(7,'Дата','\\Simflex\\Admin\\Fields\\FieldDate'),(8,'Дата и время','\\Simflex\\Admin\\Fields\\FieldDateTime'),(9,'Текст','\\Simflex\\Admin\\Fields\\FieldText'),(10,'Файл','\\Simflex\\Admin\\Fields\\FieldFile'),(11,'Изображение','\\Simflex\\Admin\\Fields\\FieldImage'),(14,'Варианты','\\Simflex\\Admin\\Fields\\FieldEnum'),(15,'№ п/п','\\Simflex\\Admin\\Fields\\FieldNPP'),(16,'Виртуальное поле','\\Simflex\\Admin\\Fields\\FieldVirtual'),(17,'Время','\\Simflex\\Admin\\Fields\\FieldTime'),(18,'Дробное число','\\Simflex\\Admin\\Fields\\FieldDouble'),(19,'Связь многие ко многим','\\Simflex\\Admin\\Fields\\FieldMultiKey'),(20,'Пароль видимый','\\Simflex\\Admin\\Fields\\FieldPasswordVisible'),(21,'Таблица','\\Simflex\\Admin\\Fields\\FieldTable');
/*!40000 ALTER TABLE `struct_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_field_param`
--

DROP TABLE IF EXISTS `struct_field_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_field_param` (
  `fp_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'Тип параметра FK struct_field',
  `help` varchar(255) NOT NULL DEFAULT '',
  `default_value` text DEFAULT NULL,
  PRIMARY KEY (`fp_id`),
  KEY `FK_struct_field_param_struct_field_field_id` (`field_id`),
  KEY `FK_struct_field_param_struct_field_field_id2` (`type_id`),
  CONSTRAINT `FK_struct_field_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_struct_field_param_struct_field_field_id2` FOREIGN KEY (`type_id`) REFERENCES `struct_field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=655;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_field_param`
--

LOCK TABLES `struct_field_param` WRITE;
/*!40000 ALTER TABLE `struct_field_param` DISABLE KEYS */;
INSERT INTO `struct_field_param` VALUES (1,2,'is_fk','Внешний ключ',3,'',NULL),(2,2,'fk_table','Внешний ключ. Таблица',1,'',NULL),(3,2,'fk_key','Внешний ключ. Ключ',1,'',NULL),(4,2,'fk_label','Внешний ключ. Ярлык',1,'',NULL),(6,2,'fk_is_pid','Внешний ключ. Поле PID',3,'',NULL),(9,9,'editor_mini','Минимальный редактор',3,'',NULL),(10,9,'editor_full','Полный редактор',3,'',NULL),(11,16,'subquery','Подзапрос',1,'',NULL),(12,16,'text','Текст',1,'',NULL),(13,16,'href','Ссылка',1,'',NULL),(14,10,'path','Адрес',1,'/uf/files/{указанный адрес}',NULL),(15,11,'path','Адрес',1,'/uf/images/{указанный адрес}',NULL),(16,11,'small','Малый размер',1,'Формат 200x150. Точный размер - 200x150x1',NULL),(17,11,'medium','Средний размер',1,'Формат 400x300. Точный размер - 400x300x1',NULL),(18,11,'large','Большой размер',1,'Формат 640x480. Точный размер - 640x480x1',NULL),(19,4,'source','Источник',1,'Из какого поля таблицы брать значение. Например: name','name'),(20,16,'in_modal','Открыть в модальном окне',3,'','0'),(21,18,'decimals','Число знаков после запятой',2,'0 - автоматически','0'),(22,18,'dec_point','Разделитель дробной части',1,'','.'),(23,18,'thousands_sep','Разделитель порядков',1,'',' '),(24,17,'use_seconds','С секундами',3,'Учитывать секунды','0'),(25,19,'table_values','Таблица сущностей',1,'',NULL),(26,19,'table_relations','Таблица связей',1,'',NULL),(27,19,'key','Ключ таблицы сущностей',1,'',NULL),(28,19,'key_alias','Поле-ярлык у сущности',1,'Например name у catalog_category',NULL),(29,21,'struct','Параметры',21,'','{\n    \"s\": [\n        {\n            \"n\": \"n\",\n            \"t\": \"text\",\n            \"l\": \"Имя\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"t\",\n            \"t\": \"combo\",\n            \"l\": \"Тип\",\n            \"v\": \"text\",\n            \"e\": \"text=Текст,,int=Число,,combo=Список,,editor=Редактор,,image=Изображение,,file=Файл\"\n        },\n        {\n            \"n\": \"l\",\n            \"t\": \"text\",\n            \"l\": \"Заголовок\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"v\",\n            \"t\": \"text\",\n            \"l\": \"Значение\",\n            \"v\": \"\",\n            \"e\": \"\"\n        },\n        {\n            \"n\": \"e\",\n            \"t\": \"text\",\n            \"l\": \"Дополнительно\",\n            \"v\": \"\",\n            \"e\": \"\"\n        }\n    ],\n    \"v\": []\n}');
/*!40000 ALTER TABLE `struct_field_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_param`
--

DROP TABLE IF EXISTS `struct_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `param_pid` int(11) DEFAULT NULL,
  `table_id` int(11) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `pos` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `default_value` text NOT NULL,
  `params` longtext DEFAULT NULL,
  `npp` int(11) DEFAULT 0,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`param_id`),
  KEY `FK_struct_param_struct_field_field_id` (`field_id`),
  KEY `FK_struct_param_struct_param_param_id` (`param_pid`),
  KEY `FK_struct_param_struct_table_table_id` (`table_id`),
  CONSTRAINT `FK_struct_param_struct_field_field_id` FOREIGN KEY (`field_id`) REFERENCES `struct_field` (`field_id`),
  CONSTRAINT `FK_struct_param_struct_param_param_id` FOREIGN KEY (`param_pid`) REFERENCES `struct_param` (`param_id`),
  CONSTRAINT `FK_struct_param_struct_table_table_id` FOREIGN KEY (`table_id`) REFERENCES `struct_table` (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1820;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_param`
--

LOCK TABLES `struct_param` WRITE;
/*!40000 ALTER TABLE `struct_param` DISABLE KEYS */;
INSERT INTO `struct_param` VALUES (1,NULL,3,NULL,'right','main','Параметры','',NULL,0,''),(2,1,3,3,'','pk','Первичный ключ','',NULL,0,''),(3,1,3,3,'','e2n','Преобразовать к NULL','',NULL,0,''),(4,1,3,3,'','hidden','Скрытый','',NULL,0,''),(5,1,3,2,'','width','Ширина столбца','',NULL,0,''),(6,1,3,1,'','defaultValue','Значение по умолчанию','',NULL,0,''),(7,1,3,3,'','required','Обязательно для заполнения','',NULL,0,''),(8,1,3,3,'','filter','Добавить в фильтр','',NULL,0,''),(10,NULL,12,NULL,'right','content_main','Вывод данных','',NULL,0,''),(11,10,12,1,'','tpl','Шаблон вывода','mod_list.tpl',NULL,0,''),(12,10,12,2,'','cnt_limit','Количество','3',NULL,0,''),(13,NULL,12,NULL,'right','content_view','Внешний вид','',NULL,0,''),(14,13,12,3,'','date','Дата','',NULL,0,''),(15,13,12,3,'','short','Анонс','0',NULL,0,''),(16,13,12,3,'','more','Кнопка далее','0',NULL,0,''),(17,13,12,1,'','more_text','Текст на кнопке далее','Читать далее',NULL,0,''),(18,1,3,1,'','onchange','on change','',NULL,0,''),(19,NULL,4,NULL,'right','main','Параметры','',NULL,0,''),(20,19,4,1,'','defaultValue','Значение по умолчанию','',NULL,0,''),(21,NULL,28,NULL,'right','module_param_main','Параметры','',NULL,0,''),(22,21,28,1,'','default_value','Значение по умолчанию','',NULL,0,''),(23,NULL,25,NULL,'right','module_item_main','Параметры','',NULL,0,''),(25,NULL,25,3,'right','is_title','Показывать заголовок','1',NULL,0,''),(26,NULL,25,3,'right','is_wrap','Выводить обертку','1',NULL,0,''),(27,10,12,3,'','hide_title','Не показывать заголовок','0','a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:1:\"0\";}}',0,''),(28,1,3,3,'','readonly','Только чтение','0','a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}',0,''),(29,1,3,1,'','style_cell','CSS стили ячейки','','a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}',0,''),(30,NULL,25,1,'right','cssclass','CSS Класс','','a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}',0,''),(31,1,3,2,'','screen_width','Показывать на мониторах до, px','0','a:1:{s:4:\"main\";a:6:{s:12:\"defaultValue\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}',0,'');
/*!40000 ALTER TABLE `struct_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_table`
--

DROP TABLE IF EXISTS `struct_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_table` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `order_by` varchar(255) DEFAULT NULL,
  `order_desc` int(11) DEFAULT NULL,
  `priv_add` int(11) DEFAULT NULL COMMENT 'Привелегия, которая позволяет добавлять строки в таблицу',
  `priv_edit` int(11) DEFAULT NULL COMMENT 'Привелегия, которая позволяет изменять строки в таблице',
  `priv_delete` int(11) DEFAULT NULL COMMENT 'Привелегия, которая позволяет удалять строки из таблицы',
  `class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`table_id`),
  UNIQUE KEY `UK_struct_table_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=1365;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_table`
--

LOCK TABLES `struct_table` WRITE;
/*!40000 ALTER TABLE `struct_table` DISABLE KEYS */;
INSERT INTO `struct_table` VALUES (1,'struct_table',NULL,NULL,NULL,NULL,NULL,NULL),(2,'struct_field',NULL,NULL,NULL,NULL,NULL,NULL),(3,'struct_data','npp',NULL,NULL,NULL,NULL,NULL),(4,'struct_param',NULL,NULL,NULL,NULL,NULL,NULL),(5,'admin_menu','npp',NULL,NULL,NULL,NULL,NULL),(6,'user_priv',NULL,NULL,NULL,NULL,NULL,NULL),(7,'user_role',NULL,NULL,NULL,NULL,NULL,NULL),(8,'user',NULL,NULL,NULL,NULL,NULL,NULL),(9,'user_role_priv',NULL,NULL,NULL,NULL,NULL,NULL),(10,'menu','npp',NULL,NULL,NULL,NULL,NULL),(11,'component',NULL,NULL,NULL,NULL,NULL,NULL),(12,'content',NULL,NULL,NULL,NULL,NULL,'\\Simflex\\Admin\\Extensions\\Content\\Admin\\AdminContent'),(22,'settings','npp',NULL,NULL,NULL,NULL,NULL),(23,'struct_field_param',NULL,NULL,NULL,NULL,NULL,NULL),(24,'module',NULL,NULL,NULL,NULL,NULL,NULL),(25,'module_item','npp',NULL,NULL,NULL,NULL,NULL),(26,'seo',NULL,NULL,NULL,NULL,NULL,NULL),(28,'module_param','npp',NULL,NULL,NULL,NULL,NULL),(29,'component_param','npp',NULL,NULL,NULL,NULL,NULL),(30,'slider','npp',NULL,NULL,NULL,NULL,NULL),(31,'callback','datetime',1,NULL,NULL,NULL,NULL),(32,'log','log_id',1,NULL,NULL,NULL,NULL),(33,'cron',NULL,NULL,1,1,1,NULL),(34,'user_priv_personal',NULL,NULL,1,1,1,NULL),(35,'content_template',NULL,NULL,1,1,1,NULL),(36,'content_template_param',NULL,NULL,1,1,1,NULL);
/*!40000 ALTER TABLE `struct_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struct_table_right`
--

DROP TABLE IF EXISTS `struct_table_right`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `struct_table_right` (
  `table_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `can_add` int(11) NOT NULL,
  `can_edit` int(11) NOT NULL,
  `can_delete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Права пользователей на таблицу';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struct_table_right`
--

LOCK TABLES `struct_table_right` WRITE;
/*!40000 ALTER TABLE `struct_table_right` DISABLE KEYS */;
/*!40000 ALTER TABLE `struct_table_right` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT 3,
  `active` int(11) NOT NULL DEFAULT 0,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `hash_admin` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UK_user_login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_user_user_role_role_id` (`role_id`),
  CONSTRAINT `FK_user_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=8192;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,1,'dev','827ccb0eea8a706c4c34a16891f84e7b','fb1a392b083e3121edd7c9046be62baa','22da1292a596ee685cb20faa435c3c8f','','',NULL),(2,2,1,'admin','827ccb0eea8a706c4c34a16891f84e7b','e1c3403a66d1269c4e3edbae11c11f03','',NULL,'',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` char(32) NOT NULL,
  `time_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_last_login` timestamp NULL DEFAULT NULL,
  `time_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `useragent` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`auth_id`),
  KEY `user_auth_user_user_id_fk` (`user_id`),
  KEY `user_auth_time_last_login_index` (`time_last_login`),
  KEY `user_auth_token_time_expires_index` (`token`,`time_expires`),
  CONSTRAINT `user_auth_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_auth`
--

LOCK TABLES `user_auth` WRITE;
/*!40000 ALTER TABLE `user_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_priv`
--

DROP TABLE IF EXISTS `user_priv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_priv` (
  `priv_id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(11) NOT NULL DEFAULT 0,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`priv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=4096;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_priv`
--

LOCK TABLES `user_priv` WRITE;
/*!40000 ALTER TABLE `user_priv` DISABLE KEYS */;
INSERT INTO `user_priv` VALUES (1,1,1,'dev','Привилегия разработчика'),(2,1,2,'admin','Привилегия администратора'),(3,1,2,'simplex_admin','Доступ к административному интерфейсу'),(4,1,3,'debug','Показывать отладчик');
/*!40000 ALTER TABLE `user_priv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_priv_personal`
--

DROP TABLE IF EXISTS `user_priv_personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_priv_personal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `priv_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_role_priv_user_priv_priv_id2` (`priv_id`),
  KEY `FK_user_role_priv_user_role_role_id2` (`user_id`),
  CONSTRAINT `FK_user_role_priv_user_priv_priv_id2` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`),
  CONSTRAINT `FK_user_role_priv_user_role_role_id2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_priv_personal`
--

LOCK TABLES `user_priv_personal` WRITE;
/*!40000 ALTER TABLE `user_priv_personal` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_priv_personal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `priv_id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `npp` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`role_id`),
  KEY `FK_user_role_user_priv_priv_id` (`priv_id`),
  CONSTRAINT `FK_user_role_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=5461;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1,1,1,'Разработчик'),(2,2,1,2,'Администратор'),(3,2,1,3,'Пользователь');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role_priv`
--

DROP TABLE IF EXISTS `user_role_priv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role_priv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `priv_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_role_priv_user_priv_priv_id` (`priv_id`),
  KEY `FK_user_role_priv_user_role_role_id` (`role_id`),
  CONSTRAINT `FK_user_role_priv_user_priv_priv_id` FOREIGN KEY (`priv_id`) REFERENCES `user_priv` (`priv_id`),
  CONSTRAINT `FK_user_role_priv_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AVG_ROW_LENGTH=2340;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role_priv`
--

LOCK TABLES `user_role_priv` WRITE;
/*!40000 ALTER TABLE `user_role_priv` DISABLE KEYS */;
INSERT INTO `user_role_priv` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,2,2),(6,NULL,4),(7,2,3);
/*!40000 ALTER TABLE `user_role_priv` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-20 19:20:05
