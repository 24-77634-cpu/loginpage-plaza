-- Import this file in phpMyAdmin (Import tab) or paste it in the SQL tab.
CREATE DATABASE IF NOT EXISTS login_system;
USE login_system;

CREATE TABLE IF NOT EXISTS users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(30)  NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login  DATETIME     NULL,
  login_count INT          NOT NULL DEFAULT 0
);

-- Demo account: admin / admin123 (password is stored hashed)
INSERT INTO users (username, password)
VALUES ('admin', '$2a$10$bxdnJRHThWKhzwjN048X3uzzIqSG9z/vuPKqR0c49yEqOy90B4oEG');
