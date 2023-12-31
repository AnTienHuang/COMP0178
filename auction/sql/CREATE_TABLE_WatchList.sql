DROP TABLE IF EXISTS WatchList;

CREATE TABLE WatchList
(
  itemId INTEGER NOT NULL,
  userId INTEGER NOT NULL,
  addedTime datetime NOT NULL,
  PRIMARY KEY (itemId, userId),
  KEY pkey (userId) -- create index on userId as well to improve performance
)

ENGINE = InnoDB;