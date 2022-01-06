<?php

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

session_start();

/* Deletes the task in mysql database and returns boolean value of execute. */
function deleteItem($mysqli, $userId, $taskId) {
    $sth = $mysqli->prepare('DELETE FROM tasks WHERE task_id=? AND user_id=?');
    $sth->bind_param('ii', $taskId, $userId);
    return $sth->execute();
}
//Checks the request.
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'DELETE')
    responseError(400);

//Get request data.
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
//Check user is login
if (!key_exists('id', $_SESSION)) responseError(401);

$changedRowsNum = deleteItem($mysqli, $_SESSION['id'], $data['id']);

if($changedRowsNum) echo json_encode(['ok' => true]);
else responseError(400);