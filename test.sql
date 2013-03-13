DROP TABLE IF EXISTS users;
CREATE TABLE users (
     id INTEGER
    ,name TEXT
    ,timeCreate INTEGER
    ,PRIMARY KEY (id)
    ,UNIQUE (name)
);
INSERT INTO users (name, timeCreate) VALUES ("admin", 1363064829);
INSERT INTO users (name, timeCreate) VALUES ("user1", 1363064830);
INSERT INTO users (name, timeCreate) VALUES ("user2", 1363064831);
INSERT INTO users (name, timeCreate) VALUES ("user3", 1363064832);
INSERT INTO users (name, timeCreate) VALUES ("user4", 1363064833);
INSERT INTO users (name, timeCreate) VALUES ("user5", 1363064834);
