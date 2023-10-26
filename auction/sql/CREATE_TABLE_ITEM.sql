DROP TABLE IF EXISTS Item;

CREATE TABLE Item
(
  itemId INTEGER AUTO_INCREMENT PRIMARY KEY,
  sellerId INTEGER,
  FOREIGN KEY (sellerId) REFERENCES User(userId),
  title VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  itemStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_item_status CHECK (itemStatus IN ('Open', 'Closed-Won', 'Closed-No-bid')),
  category VARCHAR(20) NOT NULL,
  CONSTRAINT check_category CHECK (category IN ('Consumable', 'Home Accessory', 'Kitchenware', 'Technology')),
  startTime datetime NOT NULL,
  endTime datetime NOT NULL,
  reservedPrice decimal,
  startingPrice decimal NOT NULL
)

ENGINE = InnoDB;