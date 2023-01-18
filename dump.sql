USE quotes;

CREATE TABLE IF NOT EXISTS authors (
         id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
         name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS quotes (
       id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
       text VARCHAR(255) NOT NULL,
       author_id INT NOT NULL
);

ALTER TABLE quotes
ADD FOREIGN KEY (author_id) REFERENCES authors(id);

INSERT INTO authors (name)
VALUES
        ('Marie Curie'),
        ('Steve Jobs'),
        ('Chinese Proverb'),
        ('Leonardo da Vinci'),
        ('Booker T. Washington'),
        ('Napoleon Hill');

INSERT INTO quotes (text, author_id)
VALUES
    ('We must believe that we are gifted for something and that this thing must be attained.', 1),
    ('Innovation distinguishes between a leader and a follower', 2),
    ('Don''t let the noise of others'' opinions drown out your own inner voice.', 2),
    ('Be not afraid of going slowly. Be afraid only of standing still.', 3),
    ('The best time to plant a tree was 20 years ago. The second best time is now.', 3),
    ('Patience is a bitter plant, but its fruit is sweet.', 3),
    ('A crisis is an opportunity riding the dangerous wind.', 3),
    ('Simplicity is the ultimate sophistication.', 4),
    ('Time stays long enough for anyone who will use it.', 5),
    ('Learning never exhausts the mind.', 5),
    ('Common sense is that which judges the things given to it by other senses.', 5),
    ('A goal is a dream with a deadline.', 6);


















