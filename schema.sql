drop DATABASE IF EXISTS yeti;

CREATE DATABASE yeti DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE yeti;

CREATE TABLE categories
(
  id   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20)
);

CREATE TABLE users
(
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP    NOT NULL        DEFAULT CURRENT_TIMESTAMP,
  email             VARCHAR(128) NOT NULL UNIQUE DEFAULT '',
  name              CHAR(30)     NOT NULL        DEFAULT '',
  user_password     VARCHAR(254) NOT NULL        DEFAULT '',
  avatar            CHAR(32)     NOT NULL        DEFAULT '',
  contacts          TEXT         NOT NULL
);

CREATE TABLE lots
(
  id              INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id     INT UNSIGNED   NOT NULL DEFAULT 0,
  owner_id        INT UNSIGNED   NOT NULL DEFAULT 0,
  winner_id       INT UNSIGNED,
  creation_date   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  name            CHAR(64)       NOT NULL DEFAULT '',
  description     TEXT           NOT NULL,
  image           CHAR(50)       NOT NULL DEFAULT '',
  price           DECIMAL(10, 2) NOT NULL DEFAULT 0,
  completion_date DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  step            DECIMAL(10, 2) NOT NULL DEFAULT 0
);

CREATE TABLE bids
(
  id             INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id        INT UNSIGNED   NOT NULL DEFAULT 0,
  lot_id         INT UNSIGNED   NOT NULL DEFAULT 0,
  placement_date TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  declared_price DECIMAL(10, 2) NOT NULL DEFAULT 0
);


CREATE INDEX winner_completion ON lots (winner_id, completion_date);

CREATE INDEX category_lots ON lots (category_id, winner_id, completion_date);

CREATE FULLTEXT INDEX lot_ft_search  ON lots(name, description);

CREATE UNIQUE INDEX  user_lot ON bids (user_id, lot_id);


ALTER TABLE lots
  ADD CONSTRAINT FK_user_lots FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE bids
  ADD CONSTRAINT FK_lot_bids FOREIGN KEY (lot_id) REFERENCES lots (id) ON DELETE CASCADE;
ALTER TABLE bids
  ADD CONSTRAINT FK_user_bids FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;
