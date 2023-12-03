DROP TABLE IF EXISTS User;

CREATE TABLE User
(
  id VARCHAR(255) PRIMARY KEY,
  firstName VARCHAR(40) NOT NULL,
  lastName VARCHAR(40) NOT NULL,
  password VARCHAR(255) NOT NULL,
  -- email VARCHAR(40) NOT NULL,
  -- isSeller BOOLEAN NOT NULL
  accountType VARCHAR(6) NOT NULL,
  CONSTRAINT check_account_type CHECK (accountType IN ('Seller', 'Buyer'))

)
ENGINE = InnoDB;

INSERT INTO User (id, firstName, lastName, password, accountType) 
VALUES('test@email.com', 'test_first_name', 'test_last_name', password('aaa'), 'Seller')