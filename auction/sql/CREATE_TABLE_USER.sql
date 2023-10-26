DROP TABLE IF EXISTS User;

CREATE TABLE User
(
  userId INTEGER AUTO_INCREMENT PRIMARY KEY,
  firstName VARCHAR(40) NOT NULL,
  lastName VARCHAR(40) NOT NULL,
  password VARCHAR(40) NOT NULL,
  email VARCHAR(40) NOT NULL,
  isSeller BOOLEAN NOT NULL
)
ENGINE = InnoDB;

-- INSERT INTO Users (username, password) VALUES
-- ('someone', SHA('password'))

-- SELECT username FROM Users WHERE username =
-- '$user' AND password = SHA('$password')