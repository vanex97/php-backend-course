<?php

session_start();

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

/* Searches the database for a user, verifies the password, and returns its id. */
function loginUser($mysqli, $login, $pass) {
    $sth = $mysqli->prepare('SELECT * FROM users WHERE login=? limit 1');
    $sth->bind_param('s', $login);
    $sth->execute();
    $row = $sth->get_result()->fetch_array();
    if ($row && key_exists('pass', $row) && password_verify($pass, $row['pass']))
        return $row['id'];
    return null;
}
//Checks the request.
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'POST')
    responseError(400);

//Get request data.
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
//Check get data is valid.
if ($data == null || !key_exists('login', $data) && !key_exists('pass', $data)) {
    responseError(400);
}

//Get database connection.
$mysqli = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($mysqli === null) {
    responseError(500);
}

$userId = loginUser($mysqli, $data['login'], $data['pass']);
if ($userId === null) responseError(400);

$_SESSION['id'] = $userId;

echo json_encode(['ok' => true]);
