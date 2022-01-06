<?php

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

/* Adds a new user to the database. */
function registerUser($pdo, $login, $pass) {
    $sth = $pdo->prepare('INSERT INTO users (login, pass) VALUES (:login, :pass)');
    try {
        $sth->execute([
            'login' => $login,
            'pass' => password_hash($pass, PASSWORD_DEFAULT)
        ]);
    } catch (PDOException $e) {
        return false;
    }
    return true;
}

if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'POST')
    responseError(400);

//Get request data.
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

if ($data == null || !key_exists('login', $data) && !key_exists('pass', $data)) {
    responseError(400);
}

$pdo = require __DIR__ . '/../database/createConnection.php';

$res = registerUser($pdo, $data['login'], $data['pass']);
if ($res) echo json_encode(['ok' => true]);
else responseError(400);