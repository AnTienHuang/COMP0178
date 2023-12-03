DROP TABLE IF EXISTS Notification;

CREATE TABLE Notification
(
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  userID VARCHAR(255),
  FOREIGN KEY (userID) REFERENCES User(id)
    ON DELETE CASCADE,
  itemId INTEGER,
  FOREIGN KEY (itemId) REFERENCES Item(id)
    ON DELETE CASCADE,
  notificationType VARCHAR(20) NOT NULL,
  CONSTRAINT check_notification_type CHECK (notificationType IN ('Auction Update', 'Auction Close', 'Bid Update', 'Bid Close', 'WatchList Update')),
  createdTime datetime NOT NULL,
  message VARCHAR(255)
)

ENGINE = InnoDB;
