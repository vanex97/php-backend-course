<?php

session_start();

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

/* Adds the task in database and returns insert id. */
function addTask($pdo, $userId, $taskText) {
    $sth = $pdo->prepare('insert into tasks (user_id, text, checked) values (:userId, :taskText, false)');
    $sth->execute([
        'userId' => $userId,
        'taskText' => $taskText
    ]);
    return $pdo->lastInsertId();
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
$pdo = require __DIR__ . '/../database/createConnection.php';

//Check connection.
if ($pdo === null) {
    responseError(500);
}

//Check user is login.
if (!key_exists('id', $_SESSION)) responseError(401);

$taskId = addTask($pdo, $_SESSION['id'], $data['text']);

echo json_encode(["id" => $taskId]);
