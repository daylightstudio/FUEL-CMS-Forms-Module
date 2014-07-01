DROP TABLE IF EXISTS `forms`;
DROP TABLE IF EXISTS `form_entries`;
DELETE FROM `fuel_permissions` WHERE `name` LIKE 'forms/%';
DELETE FROM `fuel_permissions` WHERE `name` LIKE 'form_entries/%';