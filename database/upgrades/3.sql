ALTER TABLE `artworks` ADD `thumbUrl` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `description`; 
ALTER TABLE `artworks` ADD `thumbFocusX` TINYINT NULL DEFAULT '50' AFTER `thumbUrl`,
                       ADD `thumbFocusY` TINYINT NULL DEFAULT '20' AFTER `thumbFocusX`;
UPDATE `settings` SET `value` = '3' WHERE `settings`.`name` = 'dbVersion'
