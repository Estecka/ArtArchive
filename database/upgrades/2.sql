ALTER TABLE `artworks` CHANGE `title` `title` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `artworks` ADD `links` TEXT NULL DEFAULT NULL AFTER `description`;
UPDATE `settings` SET `value` = '2' WHERE `settings`.`name` = 'dbVersion'
