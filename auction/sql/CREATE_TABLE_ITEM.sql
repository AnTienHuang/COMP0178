DROP TABLE IF EXISTS Item;

CREATE TABLE Item
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  sellerId INTEGER NOT NULL,
  FOREIGN KEY (sellerId) REFERENCES User(id)
    ON DELETE CASCADE,
  title VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  itemStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_item_status CHECK (itemStatus IN ('Open', 'Closed-Won', 'Closed-No-bid')),
  startTime datetime NOT NULL,
  endTime datetime NOT NULL,
  reservedPrice decimal,
  startingPrice decimal NOT NULL
)

ENGINE = InnoDB;