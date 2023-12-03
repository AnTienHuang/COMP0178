DROP TABLE IF EXISTS Item_Category;

CREATE TABLE Item_Category (
    itemId int NOT NULL,
    categoryId int NOT NULL,
    PRIMARY KEY (itemId, categoryId),
    KEY pkey (categoryId), -- create index on category id as well to improve performance
    FOREIGN KEY (categoryId) REFERENCES category (id)
       ON DELETE CASCADE
       ON UPDATE CASCADE,
    FOREIGN KEY (itemId) REFERENCES item (id)
       ON DELETE CASCADE
       ON UPDATE CASCADE
)Engine=InnoDB

INSERT INTO Item_Category(itemId, categoryId)
SELECT id, 1
FROM Item
WHERE startTime = '2023-01-01 00:00:00'
AND sellerId = 'test@email.com'