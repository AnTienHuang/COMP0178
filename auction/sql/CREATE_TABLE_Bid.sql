DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid
(
  bidId INTEGER AUTO_INCREMENT PRIMARY KEY,
  buyerId INTEGER AUTO_INCREMENT FOREIGN KEY,
  itemId INTEGER AUTO_INCREMENT FOREIGN KEY,
  status VARCHAR(20) NOT NULL,
  CONSTRAINT check_status CHECK (status IN ('Winning', 'Losing', 'Won', 'Lost'))
  bidTime datetime NOT NULL,
  price decimal,
)

ENGINE = InnoDB;