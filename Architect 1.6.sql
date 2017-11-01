-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Sep 17, 2014 at 11:29 AM
-- Server version: 5.1.73
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_export`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_additional_fields`
--

CREATE TABLE IF NOT EXISTS `tbl_additional_fields` (
  `additional_field_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each additional field',
  `additional_field_additional_field_group_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_additional_field_groups indicating which group this field is part of',
  `additional_field_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the field',
  `additional_field_is_required` tinyint(1) NOT NULL COMMENT 'boolean indicating whether or not this field is a required field',
  PRIMARY KEY (`additional_field_ID`),
  UNIQUE KEY `additional_field_name` (`additional_field_name`),
  KEY `additional_field_additional_field_group_FK` (`additional_field_additional_field_group_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold additional fields' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_additional_field_groups`
--

CREATE TABLE IF NOT EXISTS `tbl_additional_field_groups` (
  `additional_field_group_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each field group',
  `additional_field_group_permissions_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_permissions indicating who has permission to modify the field group',
  `additional_field_group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of each additional field group',
  PRIMARY KEY (`additional_field_group_ID`),
  KEY `additional_field_group_permissions_FK` (`additional_field_group_permissions_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold additional field groups' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_additional_field_values`
--

CREATE TABLE IF NOT EXISTS `tbl_additional_field_values` (
  `additional_field_value_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each additional field value',
  `additional_field_value_additional_field_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_additional_fields indicating what type of value this is',
  `additional_field_value_document_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_documents indicating which document this value is for',
  `additional_field_value_en` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'english value',
  PRIMARY KEY (`additional_field_value_ID`),
  KEY `additional_field_value_additional_field_FK` (`additional_field_value_additional_field_FK`),
  KEY `additional_field_value_document_FK` (`additional_field_value_document_FK`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold additional field values' AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blocks`
--

CREATE TABLE IF NOT EXISTS `tbl_blocks` (
  `block_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each block',
  `block_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique name of the block',
  `block_code_location` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'file location of the block''s code',
  PRIMARY KEY (`block_ID`),
  UNIQUE KEY `block_name` (`block_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold code blocks' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_documents`
--

CREATE TABLE IF NOT EXISTS `tbl_documents` (
  `document_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each document',
  `document_group_FK` int(255) unsigned DEFAULT NULL COMMENT 'Relation to tbl_document_groups indicating which group this document is part of',
  `document_template_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_templates indicating which template should be used to display this document (optional)',
  `document_status_FK` int(255) unsigned DEFAULT '2' COMMENT 'relation to tbl_document_statuses indicating what the current status of the document is',
  `document_privacy_list` text NOT NULL COMMENT 'comma delimited list indicating which user groups have access to this private document',
  `document_created` datetime NOT NULL COMMENT 'DATETIME the document was created',
  `document_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'TIMESTAMP when the document was last updated',
  `document_is_home_page` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether or not this page is to be displayed as the home page',
  `document_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the document',
  `document_title_en` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'title of the document',
  `document_content_en` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'actual content of the document',
  PRIMARY KEY (`document_ID`),
  KEY `document_template_FK` (`document_template_FK`),
  KEY `document_group_FK` (`document_group_FK`),
  KEY `document_status_FK` (`document_status_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table to hold general site documents' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_document_groups`
--

CREATE TABLE IF NOT EXISTS `tbl_document_groups` (
  `document_group_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each document group',
  `document_group_template_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_templates indicating which template should be used to render this document',
  `document_group_single_item_template_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_templates indicating which template to use in order to display a single item',
  `document_group_additional_field_group_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_additional_fields_groups indicating which additional fields are used by this document group',
  `document_group_permissions_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_permissions indicating who can modify this document group',
  `document_group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique name of each document group',
  PRIMARY KEY (`document_group_ID`),
  UNIQUE KEY `document_group_name` (`document_group_name`),
  KEY `document_group_template_FK` (`document_group_template_FK`),
  KEY `document_group_additional_fields_FK` (`document_group_additional_field_group_FK`),
  KEY `document_group_permissions_FK` (`document_group_permissions_FK`),
  KEY `document_group_single_item_template_FK` (`document_group_single_item_template_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold document groups' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_document_statuses`
--

CREATE TABLE IF NOT EXISTS `tbl_document_statuses` (
  `document_status_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each document status',
  `document_status_en` varchar(255) NOT NULL COMMENT 'english description of the status',
  PRIMARY KEY (`document_status_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='table to hold document statuses' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_document_statuses`
--

INSERT INTO `tbl_document_statuses` (`document_status_ID`, `document_status_en`) VALUES
(1, 'OFFLINE'),
(2, 'ONLINE');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_forms`
--

CREATE TABLE IF NOT EXISTS `tbl_forms` (
  `form_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each form',
  `form_name` varchar(255) NOT NULL COMMENT 'Unique Name for each form',
  `form_total_pages` int(50) unsigned NOT NULL DEFAULT '1' COMMENT 'total number of pages on the form',
  `form_destination` varchar(255) NOT NULL COMMENT 'Email address at which to deliver the form',
  `form_captcha_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether or not CAPTCHA is enabled on the form',
  PRIMARY KEY (`form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table to hold forms' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_elements`
--

CREATE TABLE IF NOT EXISTS `tbl_form_elements` (
  `form_element_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each form element',
  `form_element_form_FK` int(255) unsigned NOT NULL COMMENT 'Relation to tbl_forms indicating which form this element is part of',
  `form_element_form_page` int(20) unsigned NOT NULL COMMENT 'Int determining which page of the form this element is on',
  `form_element_type_FK` int(255) unsigned NOT NULL COMMENT 'Relation to tbl_form_element_types indicating which type of element this is',
  `form_element_name` varchar(255) DEFAULT NULL COMMENT 'Name of the element',
  `form_element_content` text COMMENT 'Content of the element. Placeholder value for inputs, content for html',
  `form_element_label` text COMMENT 'Element label',
  `form_element_pattern` text COMMENT 'RegEx Pattern used to validate input for this element',
  `form_element_is_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether or not this element is required to be completed',
  `form_element_order` int(255) unsigned NOT NULL COMMENT 'Order of the element',
  PRIMARY KEY (`form_element_ID`),
  KEY `form_element_form_page` (`form_element_form_page`),
  KEY `form_element_form_FK` (`form_element_form_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table to hold form elements' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_element_types`
--

CREATE TABLE IF NOT EXISTS `tbl_form_element_types` (
  `form_element_type_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each element type',
  `form_element_type` varchar(100) NOT NULL COMMENT 'the actual element type',
  PRIMARY KEY (`form_element_type_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table to hold form element types' AUTO_INCREMENT=1 ;

INSERT INTO `tbl_form_element_types` (`form_element_type_ID`, `form_element_type`) VALUES
(1, 'text'),
(2, 'textarea'),
(3, 'radio'),
(4, 'checkbox'),
(5, 'select'),
(6, 'email'),
(7, 'url'),
(8, 'number'),
(9, 'tel'),
(10, 'date'),
(11, 'html');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_scripts`
--

CREATE TABLE IF NOT EXISTS `tbl_form_scripts` (
  `form_script_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each form script',
  `form_script_form_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_forms indicating which form this script is for',
  `form_script_location` text NOT NULL COMMENT 'path to the script',
  PRIMARY KEY (`form_script_ID`),
  KEY `form_script_form_FK` (`form_script_form_FK`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to hold form scripts' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_values`
--

CREATE TABLE IF NOT EXISTS `tbl_form_values` (
  `form_value_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each form value',
  `form_value_form_element_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_form_elements indicating which element this value is for',
  `form_value_display_value` varchar(255) NOT NULL COMMENT 'display value of the value',
  `form_value` varchar(255) NOT NULL COMMENT 'actual value of the value',
  `form_value_order` int(20) unsigned DEFAULT NULL COMMENT 'integer controlling the order of the elements',
  PRIMARY KEY (`form_value_ID`),
  KEY `form_value_form_element_FK` (`form_value_form_element_FK`),
  KEY `form_value_form_element_FK_2` (`form_value_form_element_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table to hold form values' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_navigation_menus`
--

CREATE TABLE IF NOT EXISTS `tbl_navigation_menus` (
  `navigation_menu_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each navigation menu',
  `navigation_menu_permissions_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_permissions indicating who can modify this menu',
  `navigation_menu_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique name of each menu',
  PRIMARY KEY (`navigation_menu_ID`),
  UNIQUE KEY `navigation_menu_name` (`navigation_menu_name`),
  KEY `navigation_menu_permissions_FK` (`navigation_menu_permissions_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold navigation menus' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_navigation_menu_items`
--

CREATE TABLE IF NOT EXISTS `tbl_navigation_menu_items` (
  `navigation_menu_item_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique ID for each navigation menu item',
  `navigation_menu_item_navigation_menu_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_navigation_menus indicating which menu this item is attached to',
  `navigation_menu_item_parent_menu_item_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_navigation_menu_items indicating which menu item is this item''s parent',
  `navigation_menu_item_position` int(50) NOT NULL DEFAULT '0' COMMENT 'position of the item in the menu',
  `navigation_menu_item_link_target` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'link target of the menu item',
  `navigation_menu_item_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'custom class added to the menu item',
  `navigation_menu_item_name_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'english text of the menu item',
  `navigation_menu_item_title_en` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'english text of the menu title attribute',
  PRIMARY KEY (`navigation_menu_item_ID`),
  KEY `navigation_menu_item_navigation_menu_FK` (`navigation_menu_item_navigation_menu_FK`),
  KEY `navigation_menu_item_parent_menu_item_FK` (`navigation_menu_item_parent_menu_item_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold navigation menu items' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_permissions`
--

CREATE TABLE IF NOT EXISTS `tbl_site_permissions` (
  `site_permission_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique id for each permission',
  `site_permission_type_FK` int(255) unsigned NOT NULL COMMENT 'Relation to tbl_site_permission_types indicating what type of permission this is',
  `site_permission_entity_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_site_permission_entities indicating which specific item this permission partains to',
  `site_permission_value` varchar(100) NOT NULL COMMENT 'The actual value of the permisssion. Ususally in the form of an id number',
  PRIMARY KEY (`site_permission_ID`),
  KEY `site_permission_type_FK` (`site_permission_type_FK`),
  KEY `site_permission_entity_FK` (`site_permission_entity_FK`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to hold permissions for Architect' AUTO_INCREMENT=24 ;

--
-- Dumping data for table `tbl_site_permissions`
--

INSERT INTO `tbl_site_permissions` (`site_permission_ID`, `site_permission_type_FK`, `site_permission_entity_FK`, `site_permission_value`) VALUES
(1, 1, 5, '2'),
(5, 1, 1, '2'),
(10, 1, 9, '3'),
(11, 1, 1, '3'),
(12, 1, 7, '3'),
(14, 1, 5, '3'),
(15, 1, 7, '2'),
(16, 1, 6, '2'),
(17, 1, 6, '3'),
(18, 1, 14, '3'),
(19, 1, 13, '3'),
(20, 1, 12, '3'),
(21, 1, 11, '3'),
(22, 1, 10, '3'),
(23, 1, 16, '3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_permission_entities`
--

CREATE TABLE IF NOT EXISTS `tbl_site_permission_entities` (
  `site_permission_entity_id` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each permission entitiy',
  `site_permission_entity` varchar(255) NOT NULL COMMENT 'name of the entity',
  PRIMARY KEY (`site_permission_entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='table to hold permission entities' AUTO_INCREMENT=17 ;

--
-- Dumping data for table `tbl_site_permission_entities`
--

INSERT INTO `tbl_site_permission_entities` (`site_permission_entity_id`, `site_permission_entity`) VALUES
(1, 'management dashboard'),
(5, 'publishing dashboard'),
(6, 'publishing - media management'),
(7, 'publishing - documents'),
(9, 'development dashboard'),
(10, 'development - templates'),
(11, 'development - navigation'),
(12, 'development - document groups'),
(13, 'development - blocks'),
(14, 'development - additional fields'),
(15, 'site administration'),
(16, 'development - forms');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_permission_types`
--

CREATE TABLE IF NOT EXISTS `tbl_site_permission_types` (
  `permission_type_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique id for each permission type',
  `permission_type` varchar(255) NOT NULL COMMENT 'the type of the permission',
  PRIMARY KEY (`permission_type_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='table to hold site permission types' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_site_permission_types`
--

INSERT INTO `tbl_site_permission_types` (`permission_type_ID`, `permission_type`) VALUES
(1, 'user group'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_settings`
--

CREATE TABLE IF NOT EXISTS `tbl_site_settings` (
  `site_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each site',
  `site_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the actual site',
  `site_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'version or architect running on the site',
  `site_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'string describing the current state of the site: examples include: ONLINE, OFFLINE, MAINTENANCE, etc.',
  `site_development_alternate_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'path to redirect to when the site is under development',
  `site_development_override` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'true' COMMENT 'string required to override development redirection',
  `site_languages` text COLLATE utf8_unicode_ci NOT NULL COMMENT '| delimited string indicating which languages are installed on the site',
  `site_timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'timezone of the site',
  `site_robots` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'content for site''s robots.txt file',
  `site_map` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'content for site''s sitemap file',
  `site_custom_login_css_override` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Override for customized login page CSS file',
  `site_custom_login_preform_override` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Override for customized login page content before form',
  `site_custom_login_postform_override` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Override for customized login page content after form',
  PRIMARY KEY (`site_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold general site settings' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_site_settings`
--

INSERT INTO `tbl_site_settings` (`site_ID`, `site_name`, `site_version`, `site_status`, `site_development_alternate_path`, `site_development_override`, `site_languages`, `site_timezone`, `site_robots`, `site_map`, `site_custom_login_css_override`, `site_custom_login_preform_override`, `site_custom_login_postform_override`) VALUES
(1, '', '1.6', 'ONLINE', '', '', 'en', 'America/Phoenix', 'User-agent: *\r\nDisallow:', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_log`
--

CREATE TABLE IF NOT EXISTS `tbl_system_log` (
  `system_log_entry_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each log entry',
  `system_log_entry` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'the actual text of the log',
  `system_log_timestamp` datetime NOT NULL COMMENT 'the DATETIME this entry was logged',
  PRIMARY KEY (`system_log_entry_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table to hold site logs' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_templates`
--

CREATE TABLE IF NOT EXISTS `tbl_templates` (
  `template_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each template',
  `template_permission_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_permissions indicating which users may use this template',
  `template_additional_field_group_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_additional_field_groups indicating which additional fields are added to this template',
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'internal name of the template',
  `template_location` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'file location of the template',
  `template_custom_styles` text COLLATE utf8_unicode_ci COMMENT '| and || delimited list of custom styles that can be used in documents based on this template',
  PRIMARY KEY (`template_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold site templates' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each user',
  `user_user_status_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_user_statuses indicating the status of this user',
  `user_user_group_FK` int(255) unsigned DEFAULT NULL COMMENT 'relation to tbl_user_groups indicating what type of user this is',
  `user_username` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique username used to log in',
  `user_salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'salt that is hashed with the password to log in',
  `user_password_hash_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'hashed value of password.salt',
  `user_password_hash_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'hashed value of salt.password',
  `user_confirmation_string` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'random string used to confirm various aspects of this account',
  `user_email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email address for this user',
  `user_font_family` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT' COMMENT 'font family override for back end',
  `user_font_size` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT' COMMENT 'font size override for back end',
  `user_session_timeout` int(11) NOT NULL DEFAULT '15' COMMENT 'length of time in minutes before the user''s session will end',
  `user_tutorials_enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Boolean controlling whether or not the tutorial sections will be displayed',
  PRIMARY KEY (`user_ID`),
  UNIQUE KEY `user_username` (`user_username`),
  UNIQUE KEY `user_email_address` (`user_email_address`),
  KEY `user_user_group_FK` (`user_user_group_FK`),
  KEY `user_user_status_FK` (`user_user_status_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_data`
--

CREATE TABLE IF NOT EXISTS `tbl_user_data` (
  `user_data_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each set of user data',
  `user_data_user_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_users indicating which user account this data is about',
  `user_data_first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s first name',
  `user_data_middle_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s middle name',
  `user_data_last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s last name',
  PRIMARY KEY (`user_data_ID`),
  KEY `user_data_user_FK` (`user_data_user_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold data about the users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_groups`
--

CREATE TABLE IF NOT EXISTS `tbl_user_groups` (
  `user_group_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each user group',
  `user_group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique name of the user group',
  `user_group_description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'description of the user group',
  PRIMARY KEY (`user_group_ID`),
  UNIQUE KEY `user_group_name` (`user_group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold user groups' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_user_groups`
--

INSERT INTO `tbl_user_groups` (`user_group_ID`, `user_group_name`, `user_group_description`) VALUES
(1, 'Site Administrator', 'A user with access to all functions of the site.'),
(2, 'Content Editor', 'A user with access to the publishing functions of the site.'),
(3, 'Site Developer', 'A user with access to the development functions of the site.'),
(4, 'Site User', 'A user with no special access to the site.');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_sessions`
--

CREATE TABLE IF NOT EXISTS `tbl_user_sessions` (
  `user_session_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'unique id for each user session',
  `user_session_user_FK` int(255) unsigned NOT NULL COMMENT 'relation to tbl_users indicating who this session is for',
  `user_session_expire` datetime NOT NULL COMMENT 'DATETIME this session will expire',
  PRIMARY KEY (`user_session_ID`),
  KEY `user_session_user_FK` (`user_session_user_FK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold user sessions' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_statuses`
--

CREATE TABLE IF NOT EXISTS `tbl_user_statuses` (
  `user_status_ID` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each user status',
  `user_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'status of the user',
  `user_status_description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'description of the user status',
  PRIMARY KEY (`user_status_ID`),
  UNIQUE KEY `user_status` (`user_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table to hold user statuses' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_user_statuses`
--

INSERT INTO `tbl_user_statuses` (`user_status_ID`, `user_status`, `user_status_description`) VALUES
(1, 'unconfirmed', 'This user has not confirmed the email account associated with their user account.'),
(2, 'confirmed', 'This user has confirmed the email account associated with their user account.'),
(3, 'banned', 'This user has been banned from all non-public pages');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_additional_fields`
--
ALTER TABLE `tbl_additional_fields`
  ADD CONSTRAINT `tbl_additional_fields_ibfk_1` FOREIGN KEY (`additional_field_additional_field_group_FK`) REFERENCES `tbl_additional_field_groups` (`additional_field_group_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_additional_field_values`
--
ALTER TABLE `tbl_additional_field_values`
  ADD CONSTRAINT `tbl_additional_field_values_ibfk_1` FOREIGN KEY (`additional_field_value_additional_field_FK`) REFERENCES `tbl_additional_fields` (`additional_field_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_additional_field_values_ibfk_2` FOREIGN KEY (`additional_field_value_document_FK`) REFERENCES `tbl_documents` (`document_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_documents`
--
ALTER TABLE `tbl_documents`
  ADD CONSTRAINT `tbl_documents_ibfk_1` FOREIGN KEY (`document_template_FK`) REFERENCES `tbl_templates` (`template_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_documents_ibfk_2` FOREIGN KEY (`document_group_FK`) REFERENCES `tbl_document_groups` (`document_group_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_documents_ibfk_3` FOREIGN KEY (`document_status_FK`) REFERENCES `tbl_document_statuses` (`document_status_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_document_groups`
--
ALTER TABLE `tbl_document_groups`
  ADD CONSTRAINT `tbl_document_groups_ibfk_1` FOREIGN KEY (`document_group_template_FK`) REFERENCES `tbl_templates` (`template_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_document_groups_ibfk_4` FOREIGN KEY (`document_group_additional_field_group_FK`) REFERENCES `tbl_additional_field_groups` (`additional_field_group_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_document_groups_ibfk_5` FOREIGN KEY (`document_group_single_item_template_FK`) REFERENCES `tbl_templates` (`template_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_form_elements`
--
ALTER TABLE `tbl_form_elements`
  ADD CONSTRAINT `tbl_form_elements_ibfk_1` FOREIGN KEY (`form_element_form_FK`) REFERENCES `tbl_forms` (`form_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_form_scripts`
--
ALTER TABLE `tbl_form_scripts`
  ADD CONSTRAINT `tbl_form_scripts_ibfk_1` FOREIGN KEY (`form_script_form_FK`) REFERENCES `tbl_forms` (`form_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_form_values`
--
ALTER TABLE `tbl_form_values`
  ADD CONSTRAINT `tbl_form_values_ibfk_1` FOREIGN KEY (`form_value_form_element_FK`) REFERENCES `tbl_form_elements` (`form_element_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_navigation_menu_items`
--
ALTER TABLE `tbl_navigation_menu_items`
  ADD CONSTRAINT `tbl_navigation_menu_items_ibfk_1` FOREIGN KEY (`navigation_menu_item_parent_menu_item_FK`) REFERENCES `tbl_navigation_menu_items` (`navigation_menu_item_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_navigation_menu_items_ibfk_2` FOREIGN KEY (`navigation_menu_item_navigation_menu_FK`) REFERENCES `tbl_navigation_menus` (`navigation_menu_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_site_permissions`
--
ALTER TABLE `tbl_site_permissions`
  ADD CONSTRAINT `tbl_site_permissions_ibfk_1` FOREIGN KEY (`site_permission_type_FK`) REFERENCES `tbl_site_permission_types` (`permission_type_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_site_permissions_ibfk_2` FOREIGN KEY (`site_permission_entity_FK`) REFERENCES `tbl_site_permission_entities` (`site_permission_entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`user_user_group_FK`) REFERENCES `tbl_user_groups` (`user_group_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_users_ibfk_2` FOREIGN KEY (`user_user_status_FK`) REFERENCES `tbl_user_statuses` (`user_status_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_data`
--
ALTER TABLE `tbl_user_data`
  ADD CONSTRAINT `tbl_user_data_ibfk_1` FOREIGN KEY (`user_data_user_FK`) REFERENCES `tbl_users` (`user_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_sessions`
--
ALTER TABLE `tbl_user_sessions`
  ADD CONSTRAINT `tbl_user_sessions_ibfk_1` FOREIGN KEY (`user_session_user_FK`) REFERENCES `tbl_users` (`user_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
