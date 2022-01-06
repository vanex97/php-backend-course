<?php

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

session_start();

/* Changes the task in mysql database and returns boolean value of execute. */
function changeItem($mysqli, $userId, $taskId, $text, $checked) {
    $sth = $mysqli->prepare('UPDATE tasks SET text=?, checked=? WHERE task_id=? AND user_id=?');
    $checked = (int) $checked;
    $sth->bind_param('siii', $text, $checked, $taskId, $userId);
    return $sth->execute();
}
//Checks the request.
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'PUT')
    responseError(400);

//Get and decode request data.
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
//Check get data is valid.
if ($data === null)
    responseError(400);

//Get database connection.
$mysqli = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($mysqli === null) {
    responseError(500);
}
//Check user is login.
if (!key_exists('id', $_SESSION)) responseError(401);

$changedRowsNum = changeItem($mysqli, $_SESSION['id'], $data['id'], $data['text'], $data['checked']);

if($changedRowsNum) echo json_encode(['ok' => true]);
else responseError(400);


