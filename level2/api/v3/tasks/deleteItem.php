<?php

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

session_start();

/* Deletes the task in database and returns boolean value of execute. */
function deleteItem($pdo, $userId, $taskId) {
    $sth = $pdo->prepare('delete from tasks where task_id=:taskId and user_id=:userId');
    return $sth->execute([
        'userId' => $userId,
        'taskId' => $taskId,
    ]);
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
$pdo = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($pdo === null) {
    responseError(500);
}
//Check user is login
if (!key_exists('id', $_SESSION)) responseError(401);

$changedRowsNum = deleteItem($pdo, $_SESSION['id'], $data['id']);

if($changedRowsNum) echo json_encode(['ok' => true]);
else responseError(400);