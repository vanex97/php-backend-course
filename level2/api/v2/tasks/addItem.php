<?php

session_start();

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

/* Adds the task in mysql database and returns insert id. */
function addTask($mysqli, $userId, $taskText) {
    $sth = $mysqli->prepare('INSERT INTO tasks (user_id, text, checked) VALUES (?, ?, false)');
    $sth->bind_param('is', $userId, $taskText);
    $sth->execute();
    return $mysqli->insert_id;
}
//Checks the request
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'POST')
    responseError(400);

//Get and decode request data.
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
//Check get data is valid.
if ($data === null || !key_exists("text", $data) || count($data) != 1)
    responseError(400);

//Get database connection.
$mysqli = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($mysqli === null) {
    responseError(500);
}
//Check user is login.
if (!key_exists('id', $_SESSION)) responseError(401);

$taskId = addTask($mysqli, $_SESSION['id'], $data['text']);

echo json_encode(["id" => $taskId]);
