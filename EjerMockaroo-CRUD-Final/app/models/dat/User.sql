CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol TINYINT NOT NULL
);

INSERT INTO User (id, login, password, rol) VALUES (1,'root','root',1);
INSERT INTO User (id, login, password, rol) VALUES (2,'user','user',0);