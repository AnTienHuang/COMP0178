DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid
(
  bidId INTEGER AUTO_INCREMENT PRIMARY KEY,
  buyerId INTEGER,
  itemId INTEGER,
  FOREIGN KEY (buyerId) REFERENCES User(userId),
  FOREIGN KEY (itemId) REFERENCES Item(itemId),
  bidStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_bid_status CHECK (bidStatus IN ('Winning', 'Losing', 'Won', 'Lost')),
  bidTime datetime NOT NULL,
  price decimal
)

ENGINE = InnoDB;