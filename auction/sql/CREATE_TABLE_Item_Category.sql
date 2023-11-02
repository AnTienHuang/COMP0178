DROP TABLE IF EXISTS Item_Category;

CREATE TABLE Item_Category (
    itemId int not null,
    categoryId int not null,
    PRIMARY KEY (itemId, categoryId),
    KEY pkey (categoryId),
    FOREIGN KEY (categoryId) REFERENCES category (id)
       ON DELETE CASCADE
       ON UPDATE CASCADE,
    FOREIGN KEY (itemId) REFERENCES item (id)
       ON DELETE CASCADE
       ON UPDATE CASCADE
)Engine=InnoDB