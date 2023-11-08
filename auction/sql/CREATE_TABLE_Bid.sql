DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  buyerId INTEGER,
  FOREIGN KEY (buyerId) REFERENCES User(id)
    ON DELETE CASCADE,
  itemId INTEGER,
  FOREIGN KEY (itemId) REFERENCES Item(id)
    ON DELETE CASCADE,
  bidStatus VARCHAR(20) NOT NULL,
  CONSTRAINT check_bid_status CHECK (bidStatus IN ('Winning', 'Losing', 'Won', 'Lost')),
  bidTime datetime NOT NULL,
  price decimal
)

ENGINE = InnoDB;

-- INSERT INTO Bid (bidStatus, bidTime, buyerId, itemId, price)
-- VALUES ('Winning', '2023-11-06 22:16:17', 1, 15, 5000)