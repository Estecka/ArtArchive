CREATE DEFINER=`root`@`localhost` PROCEDURE `Check_Slug`(IN `slug` VARCHAR(32) CHARSET ascii)
    NO SQL
IF slug NOT REGEXP "^[A-Za-z0-9_]+$" THEN
	SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'slug contains unauthorized caracters';
END IF