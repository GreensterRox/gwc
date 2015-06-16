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
	location_id int NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kfirst_name (first_name),
	KEY klast_name (last_name),
	KEY kemail (email),
	KEY klocation_id (location_id),
	KEY kcreated (created),
	KEY klast_updated (last_updated)
) CHARACTER SET utf8;

-- main table of things (sub-tables used for meta data)
CREATE TABLE data_pool (
	id int PRIMARY KEY auto_increment,
	reg_id int,
	title varchar(64) NOT NULL,
	summary varchar(1000) NOT NULL,
	details text NOT NULL,
	location_id int NOT NULL,
	meta_table_id int NOT NULL,
	meta_table_record_id int NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kreg_id (reg_id),
	KEY ktitle (title),
	KEY ksummary (summary),
	KEY klocation_id (location_id),
	KEY kmeta_table_id (meta_table_id),
	KEY kmeta_table_record_id (meta_table_record_id),
	KEY kcreated (created),
	KEY klast_updated (last_updated)
) CHARACTER SET utf8;

-- link data to verticals
CREATE TABLE data_verticals_xref (
	id int PRIMARY KEY auto_increment,
	data_pool_id int NOT NULL,
	vertical_id int NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kdata_pool_id (data_pool_id),
	KEY kvertical_id (vertical_id)
) CHARACTER SET utf8;

-- link data to verticals
CREATE TABLE verticals (
	vertical_id int PRIMARY KEY auto_increment,
	name varchar(64) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kname (name)
) CHARACTER SET utf8;

-- link data to classifications
CREATE TABLE data_classifications_xref (
	id int PRIMARY KEY auto_increment,
	data_pool_id int NOT NULL,
	class_id int NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kdata_pool_id (data_pool_id),
	KEY kclass_id (class_id)
) CHARACTER SET utf8;

-- classifications
CREATE TABLE classifications (
	class_id int PRIMARY KEY auto_increment,
	name varchar(64) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kname (name)
) CHARACTER SET utf8;

INSERT INTO classifications (name,created,last_updated) VALUES ("Children's Party Venue",NOW(),NOW()) ,("Children's Entertainer",NOW(),NOW());

-- how we want to represent locations, e.g. postcode, lat, lon, display name, etc
CREATE TABLE locations (
	id int PRIMARY KEY auto_increment,
	postcode varchar(16) NOT NULL,
	latitude decimal (20,16),
	longitude decimal (20,16),
	display_name varchar(64) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kpostcode (postcode),
	KEY kdisplay_name (display_name)
) CHARACTER SET utf8;


-- FYI I've constructed this in the same order as specified in the file
CREATE TABLE geo_names_raw (
	id int PRIMARY KEY auto_increment,
	geonameid int NOT NULL,      
	name varchar(200) NOT NULL,
	asciiname varchar(200) NOT NULL,
	alternatenames varchar(1000),
	latitude decimal (9,5),
	longitude decimal (9,5),
	feature_class char(1),
	feature_code varchar(10),     
	country_code char(2),
	cc2 varchar(60),
	admin1_code varchar(60),
	admin2_code varchar(80),
	admin3_code varchar(20),
	admin4_code varchar(20),      
	population bigint,
	elevation int, 
	dem int,
	timezone  varchar(40),
	modification date 
) CHARACTER SET utf8;

--GB	SO40 2BJ	Totton and Eling	England	ENG	Hampshire County	E10000014	New Forest District	E07000091	50.9271799653319	-1.52185544515378	6*/
CREATE TABLE geo_postcodes_raw (
	id int PRIMARY KEY auto_increment,
	country_code char(2),     
	postcode varchar(16) NOT NULL,
	district1 varchar(200) NOT NULL,
	district2 varchar(200) NOT NULL,
	code1 varchar(32),
	district3 varchar(200) NOT NULL,
	code2 varchar(32),
	latitude decimal (20,16),
	longitude decimal (20,16),
	granularity_level smallint,
	KEY kpostcode (postcode)
) CHARACTER SET utf8;

-- Sites table - This represents one sites searchable data - schema can be custom to represent site needs
--	We don't want read queries going to data_pool table so each site will have published searchable data
--   Always prefixed with 'sites_'
--   TO DO - how do we handle searching by classification given that data can exist in more than one classification ?
--   Another xref table e.g. 'sites_class_xref_party_points'?
--
CREATE TABLE sites_party_points (
	id int PRIMARY KEY auto_increment,
	reg_id int,
	title varchar(64) NOT NULL,
	summary varchar(1000) NOT NULL,
	details text NOT NULL,
	location_id int NOT NULL,
	latitude decimal (20,16),
	longitude decimal (20,16),
	postcode varchar(16) NOT NULL,
	created datetime NOT NULL,
	last_updated datetime NOT NULL,
	KEY kreg_id (reg_id),
	KEY ktitle (title),
	KEY ksummary (summary),
	KEY klocation_id (location_id),
	KEY klat (latitude),
	KEY klon (longitude),
	KEY kcreated (created),
	KEY klast_updated (last_updated)
) CHARACTER SET utf8;

