--
-- Add version, category, hits, metadata, language to #__media_files
--

ALTER TABLE `#__media_files` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT 1 AFTER `publish_down`;
ALTER TABLE `#__media_files` ADD COLUMN `catid` int(10) unsigned NOT NULL DEFAULT 0  AFTER `version`;
ALTER TABLE `#__media_files` ADD COLUMN `hits` INT(10) unsigned NOT NULL DEFAULT 0 AFTER `catid`;
ALTER TABLE `#__media_files` ADD COLUMN `metadata` text NOT NULL AFTER `hits`;
ALTER TABLE `#__media_files` ADD COLUMN `language` char(7) NOT NULL COMMENT 'The language code for the media file.' AFTER `metadata`;

ALTER TABLE `#__media_files` ADD KEY `idx_catid` (`catid`);
ALTER TABLE `#__media_files` ADD KEY `idx_language` (`language`);

--
-- Change access field from tinyint(3) to INT(10)
--

ALTER TABLE `#__media_files` CHANGE `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';

--
-- Table structure for table `#__media_files``
--
CREATE TABLE IF NOT EXISTS `#__media_atributes` (
  `id` int(11) NOT NULL,
  `mediatype` varchar(250) NOT NULL,
  `key` varchar(250) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`context`,`id`),
  KEY `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
