DROP TABLE IF EXISTS WatchList;

CREATE TABLE WatchList
(
  itemId INTEGER,
  userId INTEGER,
  addedTime datetime NOT NULL,
  PRIMARY KEY (itemId, userId)
)

ENGINE = InnoDB;