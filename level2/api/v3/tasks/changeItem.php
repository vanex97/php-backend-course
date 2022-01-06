<?php

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

session_start();

/* Changes the task in database and returns boolean value of execute. */
function changeItem($pdo, $userId, $taskId, $text, $checked) {
    $sth = $pdo->prepare('UPDATE tasks set text=:text, checked=:checked where task_id=:taskId and user_id=:userId');
    $sth->execute([
        'userId' => $userId,
        'taskId' => $taskId,
        'text' => $text,
        'checked' => (int) $checked
    ]);
    return $sth->rowCount();
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
$pdo = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($pdo === null) {
    responseError(500);
}
//Check user is login.
if (!key_exists('id', $_SESSION)) responseError(401);

$changedRowsNum = changeItem($pdo, $_SESSION['id'], $data['id'], $data['text'], $data['checked']);

if($changedRowsNum) echo json_encode(['ok' => true]);
else responseError(400);


