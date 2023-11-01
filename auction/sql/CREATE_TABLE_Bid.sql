DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid
(
  bidId INTEGER AUTO_INCREMENT PRIMARY KEY,
  buyerId INTEGER,
  FOREIGN KEY (buyerId) REFERENCES User(userId)
    ON DELETE CASCADE,
  itemId INTEGER,
  FOREIGN KEY (itemId) REFERENCES Item(itemId)
    ON DELETE CASCADE,
  bidStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_bid_status CHECK (bidStatus IN ('Winning', 'Losing', 'Won', 'Lost')),
  bidTime datetime NOT NULL,
  price decimal
)

ENGINE = InnoDB;