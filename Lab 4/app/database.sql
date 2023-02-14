CREATE USER 'user'@'localhost' IDENTIFIED BY '$up3rs3cr3tp4ssw0rd';
CREATE DATABASE userdb;
GRANT ALL PRIVILEGES ON userdb.* TO 'user'@'localhost';
USE userdb;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
INSERT INTO users (username, email, password)
VALUES 
    ('user1', 'user1@local.host', 'password1'),
    ('user2', 'user2@local.host', 'password2'),
    ('user3', 'user3@local.host', 'password3'),
    ('user4', 'user4@local.host', 'password4'),
    ('user5', 'user5@local.host', 'password5');