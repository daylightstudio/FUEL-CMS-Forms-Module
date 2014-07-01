# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.9)
# Database: daylight_project_pterodactyl
# Generation Time: 2014-06-25 22:46:59 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table form_entries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `form_entries`;

CREATE TABLE `form_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remote_ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `post` text COLLATE utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table forms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms`;

CREATE TABLE `forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `save_entries` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `form_action` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `submit_button_text` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `reset_button_text` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `form_display` enum('auto','block','html') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'auto',
  `block_view` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `block_view_module` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `form_html` text COLLATE utf8_unicode_ci NOT NULL,
  `inputs` text COLLATE utf8_unicode_ci NOT NULL,
  `javascript_submit` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `javascript_validate` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `javascript_waiting_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_recipients` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_message` text COLLATE utf8_unicode_ci NOT NULL,
  `after_submit_text` text COLLATE utf8_unicode_ci NOT NULL,
  `return_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
