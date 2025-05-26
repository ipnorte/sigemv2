ALTER TABLE `asincronos` 
ADD COLUMN `parent_id` INT(11) NULL DEFAULT 0 COMMENT '' AFTER `modified`,
ADD INDEX `idx_parent_id` (`parent_id` ASC)  COMMENT '';

