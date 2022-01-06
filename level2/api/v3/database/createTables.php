<?php

$pdo = require_once 'createConnection.php';

$pdo->exec("CREATE TABLE users (
        id INT UNSIGNED AUTO_INCREMENT,
        login VARCHAR(20) NOT NULL UNIQUE,
        pass VARCHAR(512) NOT NULL,
        PRIMARY KEY(id)
    )");
$pdo->exec("CREATE TABLE tasks (
        task_id INT UNSIGNED AUTO_INCREMENT,
        user_id INT UNSIGNED,
        text VARCHAR(512) NOT NULL,
        checked BOOLEAN,
        PRIMARY KEY (task_id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");