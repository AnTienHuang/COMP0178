DROP TABLE IF EXISTS Item;

CREATE TABLE Item
(
  itemId INTEGER AUTO_INCREMENT PRIMARY KEY,
  sellerId INTEGER AUTO_INCREMENT FOREIGN KEY,
  title VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  status VARCHAR(20) NOT NULL,
  CONSTRAINT check_status CHECK (status IN ('Open', 'Closed-Won', 'Closed-No-bid'))
  category VARCHAR(40) NOT NULL,
  CONSTRAINT category CHECK (status IN ('Consumable', 'Home Accessory', 'Kitchenware', 'Technology'))
  startTime datetime NOT NULL,
  endTime datetime NOT NULL,
  reservedPrice decimal,
  startingPrice decimal NOT NULL
)

ENGINE = InnoDB;