# create tunnel to sql
#ssh -p 14501 -L 3307:127.0.0.1:3306 -N deployer@e792a814dc.us1.amezmo.co

#Disable ONLY_FULL_GROUP_BY
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

#Fix Umlaute
SET SQL_SAFE_UPDATES=0;
UPDATE `db_name`.`members` SET `first_name` = REPLACE(`first_name`, 'Ã¼', 'ü');
UPDATE `db_name`.`members` SET `last_name` = REPLACE(`last_name`, 'Ã¼', 'ü');
UPDATE `db_name`.`members` SET `town` = REPLACE(`town`, 'Ã¼', 'ü');
UPDATE `db_name`.`members` SET `address` = REPLACE(`address`, 'Ã¼', 'ü');

UPDATE `db_name`.`members` SET `first_name` = REPLACE(`first_name`, 'Ã¤', 'ä');
UPDATE `db_name`.`members` SET `last_name` = REPLACE(`last_name`, 'Ã¤', 'ä');
UPDATE `db_name`.`members` SET `town` = REPLACE(`town`, 'Ã¤', 'ä');
UPDATE `db_name`.`members` SET `address` = REPLACE(`address`, 'Ã¤', 'ä');

UPDATE `db_name`.`members` SET `first_name` = REPLACE(`first_name`, 'Ã¶', 'ö');
UPDATE `db_name`.`members` SET `last_name` = REPLACE(`last_name`, 'Ã¶', 'ö');
UPDATE `db_name`.`members` SET `town` = REPLACE(`town`, 'Ã¶', 'ö');
UPDATE `db_name`.`members` SET `address` = REPLACE(`address`, 'Ã¶', 'ö');
SET SQL_SAFE_UPDATES=1;