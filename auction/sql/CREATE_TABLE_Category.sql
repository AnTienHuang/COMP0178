DROP TABLE IF EXISTS Category;

CREATE TABLE Category
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
)
ENGINE = InnoDB;

INSERT INTO Category (name)
VALUES ('Electronics'),
       ('Home & garden'),
       ('Gift'),
       ('Clothing'),
       ('Toys'),
       ('Games'),
       ('Collectibles & antiques'),
       ('Sporting goods'),
       ('Jewellery & watches'),
       ('Motors'),
       ('Other categories')