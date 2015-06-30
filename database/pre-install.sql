CREATE USER `internal.dev`@'localhost' IDENTIFIED BY 'g34Api5C9L';
CREATE DATABASE `internal.dev`;
GRANT ALL ON `internal.dev`.* TO `internal.dev`@'localhost';
FLUSH PRIVILEGES;
