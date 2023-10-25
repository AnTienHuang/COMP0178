#DROP DATABASE auction_test;

CREATE DATABASE auction_test
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;


GRANT SELECT,UPDATE,INSERT,DELETE
    ON auction_test.*
    TO 'example'@'localhost'
    IDENTIFIED BY 'ucl';


USE Example;


CREATE TABLE Users
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(40) NOT NULL,
  family_name VARCHAR(40) NOT NULL
)
ENGINE = InnoDB;

INSERT INTO Users (first_name, family_name) VALUES ('firstName1','familyName1');
INSERT INTO Users (first_name, family_name) VALUES ('firstName2','familyName2');
INSERT INTO Users (first_name, family_name) VALUES ('firstName3','familyName3');
INSERT INTO Users (first_name, family_name) VALUES ('firstName4','familyName4');
INSERT INTO Users (first_name, family_name) VALUES ('firstName5','familyName5');
INSERT INTO Users (first_name, family_name) VALUES ('firstName6','familyName6');
INSERT INTO Users (first_name, family_name) VALUES ('firstName7','familyName7');
INSERT INTO Users (first_name, family_name) VALUES ('firstName8','familyName8');
INSERT INTO Users (first_name, family_name) VALUES ('firstName9','familyName9');
INSERT INTO Users (first_name, family_name) VALUES ('firstName10','familyName10');