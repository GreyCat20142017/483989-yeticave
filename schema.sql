DROP DATABASE IF EXISTS yeti;

CREATE DATABASE yeti DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE yeti;

CREATE TABLE categories
(
  category_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(20)
);

CREATE TABLE users
(
  user_id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP    NOT NULL        DEFAULT CURRENT_TIMESTAMP,
  email             VARCHAR(128) NOT NULL UNIQUE DEFAULT '',
  name              CHAR(30)     NOT NULL        DEFAULT '',
  user_password     CHAR(20)     NOT NULL        DEFAULT '',
  avatar            CHAR(128)    NOT NULL        DEFAULT '',
  contacts          TEXT         NOT NULL
);

CREATE TABLE lots
(
  lot_id          INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id     INT UNSIGNED   NOT NULL DEFAULT 0,
  owner_id        INT UNSIGNED   NOT NULL DEFAULT 0,
  winner_id       INT UNSIGNED,
  creation_date   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  name            CHAR(64)       NOT NULL DEFAULT '',
  description     VARCHAR(128)   NOT NULL DEFAULT '',
  image           CHAR(128)      NOT NULL DEFAULT '',
  price           DECIMAL(10, 2) NOT NULL DEFAULT 0,
  completion_date DATETIME,
  step            DECIMAL(6, 2)  NOT NULL DEFAULT 0
);

create table requests
(
  request_id   INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED   NOT NULL DEFAULT 0,
  lot_id       INT UNSIGNED   NOT NULL DEFAULT 0,
  request_date TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  request_sum  DECIMAL(10, 2) NOT NULL DEFAULT 0
);


CREATE INDEX winner_completion ON lots (winner_id, completion_date);

CREATE INDEX category_creation ON lots (category_id, creation_date);