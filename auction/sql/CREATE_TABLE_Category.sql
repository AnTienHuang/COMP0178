DROP TABLE IF EXISTS Category;

CREATE TABLE Category
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
)
ENGINE = InnoDB;

INSERT INTO Category (name)
VALUES ('Electronics'),
       ('Home'),
       ('Kitchen'),
       ('Gift'),
       ('Clothing'),
       ('Toys'),
       ('Game')
