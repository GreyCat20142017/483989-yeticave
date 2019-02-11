USE yeti;

# Чистка таблиц
TRUNCATE TABLE bids;
TRUNCATE TABLE lots;
TRUNCATE TABLE categories;
TRUNCATE TABLE users;

# Добавление существующго списка категорий
INSERT INTO categories (name)
VALUES ('Доски и лыжи'),
       ('Крепления'),
       ('Ботинки'),
       ('Одежда'),
       ('Инструменты'),
       ('Разное');

# Добавление пары пользователей
INSERT INTO users (name, email, user_password, avatar, contacts, registration_date)
VALUES ('Василий Пупкин', 'vasyaPup@mail.ru', 'secret', 'ava_1.svg', 'Почта:vasyaPup@mail.ru', '2018-01-01 11:11:11'),
       ('Василиса Пупкина', 'vasilisaPupkina@mail.ru', 'superSecret', 'ava_2.svg', 'Почта, телеграф, телефон',
        '2019-02-02 12:12:12');

# Добавление существующего списка объявлений
INSERT INTO lots (category_id, owner_id, creation_date, name, image, price, description)
VALUES (1, 1, '2019-01-01 10:10:10', '2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999.00, 'Snowbord'),
       (1, 1, '2019-01-02 10:10:10', 'DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 159999.00, ''),
       (2, 1, '2019-01-03 10:10:10', 'Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000.00, ''),
       (3, 2, '2019-02-03 20:20:20', 'Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999.00, ''),
       (4, 2, '2019-02-04 20:20:20', 'Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500.00, ''),
       (6, 2, '2019-02-05 20:20:20', 'Маска Oakley Canopy', 'img/lot-6.jpg', 5400.00, 'Маска');

# Добавление пары ставок для любого объявления
INSERT INTO bids (user_id, lot_id, placement_date, declared_price)
VALUES (2, 5, '2019-02-10 10:10:10', 7700.00),
       (1, 5, '2019-03-10 15:15:10', 8800.00);

# Получение всех категорий
SELECT id, name
FROM categories;

# Получение самых новых, открытых лотов.
SELECT l.name, price, image, c.name AS category
FROM lots AS l
       JOIN categories AS c ON l.category_id = c.id
WHERE completion_date IS NULL
ORDER BY l.creation_date DESC;

# Объявление переменной с идентификатором лота
SET @lotid = 5;

# Показ лота по его id и названия категории, к которой принадлежит лот
SELECT l.id, c.name AS category, l.name, l.creation_date, l.price, completion_date, l.image
FROM lots AS l
       JOIN categories AS c ON l.category_id = c.id
WHERE l.id = @lotid;

# Обновление название лота по его идентификатору;
UPDATE lots
SET name = 'Курточка для сноубордика'
WHERE id = @lotid;

# Получение список самых свежих ставок для лота по его идентификатору. Допустим, необходимое количество свежих = 3
SELECT b.id, u.name AS user_name, l.id as lot_id,l.name AS lot_name, b.placement_date, declared_price
FROM bids AS b
       JOIN users AS u on b.user_id = u.id
       JOIN lots AS l on b.lot_id = l.id
where b.lot_id = @lotid
ORDER BY b.placement_date DESC
LIMIT 3

# Простой "нечитабельный" вариант + сортировка по свежести с учетом того, что поле id с автоинкрементом:
# SELECT id, user_id, lot_id, placement_date, declared_price from bids where lot_id = @lotid ORDER BY id DESC;