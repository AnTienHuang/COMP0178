DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  buyerId INTEGER NOT NULL,
  FOREIGN KEY (buyerId) REFERENCES User(id)
    ON DELETE CASCADE,
  itemId INTEGER NOT NULL,
  FOREIGN KEY (itemId) REFERENCES Item(id)
    ON DELETE CASCADE,
  bidStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_bid_status CHECK (bidStatus IN ('Winning', 'Losing', 'Won', 'Lost')),
  bidTime datetime NOT NULL,
  price decimal NOT NULL
)

ENGINE = InnoDB;