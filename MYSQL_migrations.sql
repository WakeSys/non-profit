/* 
- Log into https://www.amezmo.com/
- go overview in top navigation
- under SSH, click the ... on the right
- edit trusted IPs, and add current IP address from your internet provider: https://whatismyipaddress.com/
- add public key for your machine, in case it's not yet listed below

- in mac terminal create tunnel to sql
	ssh -p 16337 -L 3306:127.0.0.1:3306 -N deployer@c8991caa2a.uk3.amezmo.co

- open mysql work bench
- add new connection
- hostname: 127.0.0.1
- username: az_user
- password: 89ea4a67b9dcd65b
- default schema: amezmo

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