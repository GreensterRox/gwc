CREATE DATABASE gwc;
use gwc;

CREATE USER 'test_user'@'localhost' IDENTIFIED BY '0nlyth3br4v3gwc';
GRANT SELECT,INSERT,UPDATE,DELETE ON gwc.unit_test TO 'test_user'@'localhost';

CREATE TABLE IF NOT EXISTS unit_test(
	id int PRIMARY KEY auto_increment,
	name varchar(64) NOT NULL,
	value varchar(64) NOT NULL,
	last_updated datetime NOT NULL
) CHARACTER SET utf8;