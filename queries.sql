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
VALUES ('Василий Пупкин', 'vasyaPup@mail.ru', SHA1('secret'), 'ava_1.svg', 'Почта:vasyaPup@mail.ru', DATE_ADD(NOW(), INTERVAL -2 MONTH)),
       ('Василиса Пупкина', 'vasilisaPupkina@mail.ru', SHA1('superSecret'), 'ava_2.svg', 'Почта, телеграф, телефон',
        DATE_ADD(NOW(), INTERVAL -3 WEEK));

# Добавление существующего списка объявлений
INSERT INTO lots (category_id, owner_id, creation_date, name, image, price, step, description, completion_date)
VALUES (1, 1, DATE_ADD(NOW(), INTERVAL -10 DAY), '2014 Rossignol District Snowboard', 'lot-1.jpg', 10999.00, 1000.00,
        'Маневренный сноуборд c симметричной геометрией в сочетании с классическим прогибом.',
        DATE_ADD(NOW(), INTERVAL 14 DAY)),
       (1, 1, DATE_ADD(NOW(), INTERVAL -7 DAY), 'DC Ply Mens 2016/2017 Snowboard', 'lot-2.jpg', 159999.00, 5000.00,
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью',
        DATE_ADD(NOW(), INTERVAL 8 HOUR)),
       (2, 1, DATE_ADD(NOW(), INTERVAL -11 DAY), 'Крепления Union Contact Pro 2015 года размер L/XL', 'lot-3.jpg', 8000.00, 500.00,
        'Высокий профиль удерживает ботинок по всей длине подошвы.',
        DATE_ADD(NOW(), INTERVAL 1 DAY)),
       (3, 2, DATE_ADD(NOW(), INTERVAL -22 DAY), 'Ботинки для сноуборда DC Mutiny Charocal', 'lot-4.jpg', 10999.00, 500.00,
        'Просто хорошие ботинки',
        DATE_ADD(NOW(), INTERVAL 2 DAY)),
       (4, 2, '2019-02-04 20:20:20', 'Куртка для сноуборда DC Mutiny Charocal', 'lot-5.jpg', 7500.00, 500.00,
        'Просто удобная куртка',
        DATE_ADD(NOW(), INTERVAL 5 DAY)),
       (6, 2, '2019-02-05 20:12:17', 'Маска Oakley Canopy', 'lot-6.jpg', 5400.00, 300.00, 'Просто маска',
        DATE_ADD(NOW(), INTERVAL 1 MONTH));

# Добавление пары ставок для любого объявления
INSERT INTO bids (user_id, lot_id, placement_date, declared_price)
VALUES (2, 5, DATE_ADD(NOW(), INTERVAL -1 DAY), 7700.00),
       (1, 5, DATE_ADD(NOW(), INTERVAL -3 HOUR), 8800.00);

# Получение всех категорий
SELECT id, name
FROM categories;

# Получение самых новых, открытых лотов.
SELECT l.name, price, image, c.name AS category
FROM lots AS l
       JOIN categories AS c ON l.category_id = c.id
WHERE winner_id IS NULL
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
WHERE b.lot_id = @lotid
ORDER BY b.placement_date DESC
LIMIT 3

# Простой "нечитабельный" вариант + сортировка по свежести с учетом того, что поле id с автоинкрементом:
# SELECT id, user_id, lot_id, placement_date, declared_price from bids where lot_id = @lotid ORDER BY id DESC;