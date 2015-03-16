CREATE DATABASE gwc;
use gwc;

CREATE TABLE admin_users (
	id int PRIMARY KEY auto_increment,
	first_name varchar(64) NOT NULL,
	last_name varchar(64) NOT NULL,
	email varchar(64) NOT NULL,
	password varchar(64) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL
) CHARACTER SET utf8;

CREATE TABLE reg_users (
	id int PRIMARY KEY auto_increment,
	first_name varchar(64) NOT NULL,
	last_name varchar(64) NOT NULL,
	email varchar(64) NOT NULL,
	password varchar(64) NOT NULL,
	telephone1 varchar(64) NOT NULL,
	telephone2 varchar(64) NOT NULL,
	address1 varchar(64) NOT NULL,
	address2 varchar(64) NOT NULL,
	address3 varchar(64) NOT NULL,
	town varchar(64) NOT NULL,
	county varchar(64) NOT NULL,
	postcode varchar(16) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL
) CHARACTER SET utf8;

/* main table of things (sub-tables used for meta data) */
CREATE TABLE data_pool (
	id int PRIMARY KEY auto_increment,
	reg_id int,
	title varchar(64) NOT NULL,
	summary varchar(1000) NOT NULL,
	details text NOT NULL,
	location_id int NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL
) CHARACTER SET utf8;

/* how we want to represent locations, e.g. postcode, lat, lon, display name, etc */
CREATE TABLE locations (
	id int PRIMARY KEY auto_increment,
	postcode varchar(16) NOT NULL,
	latitude decimal (20,16),
	longitude decimal (20,16),
	display_name varchar(64) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL
) CHARACTER SET utf8;


/* FYI I've constructed this in the same order as specified in the file */
CREATE TABLE geo_names_raw (
	id int PRIMARY KEY auto_increment,
	geonameid int NOT NULL,      
	name varchar(200) NOT NULL,
	asciiname varchar(200) NOT NULL,
	alternatenames varchar(1000),
	latitude decimal (9,5),
	longitude decimal (9,5),
	feature class char(1),
	feature code varchar(10),     
	country code char(2),
	cc2 varchar(60),
	admin1 code varchar(60),
	admin2 code varchar(80),
	admin3 code varchar(20),
	admin4 code varchar(20),      
	population bigint,
	elevation int, 
	dem int,
	timezone  varchar(40),
	modification date 
) CHARACTER SET utf8;

/*GB	SO40 2BJ	Totton and Eling	England	ENG	Hampshire County	E10000014	New Forest District	E07000091	50.9271799653319	-1.52185544515378	6*/
CREATE TABLE geo_postcodes_raw (
	id int PRIMARY KEY auto_increment,
	country code char(2),     
	postcode varchar(16) NOT NULL,
	district1 varchar(200) NOT NULL,
	district2 varchar(200) NOT NULL,
	code1 varchar(32),
	district3 varchar(200) NOT NULL,
	code2 varchar(32),
	latitude decimal (20,16),
	longitude decimal (20,16),
	granularity_level smallint
) CHARACTER SET utf8;