<?php

session_start();

require_once __DIR__ . '/../errors.php';
require_once __DIR__ . '/../cors.php';

/* Gets a task list in mysql database and returns then in associative array. */
function getTasks($pdo, $userId) {
    $sth = $pdo->prepare('SELECT task_id, text, checked FROM tasks WHERE user_id=?');
    $sth->bind_param('i', $userId);
    $sth->execute();
    $result = $sth->get_result();
    //Create output dictionary.
    $dictionary = ['items' => []];
    //Adds result tasks in dictionary.
    while ($row = $result->fetch_array()) {
        $item = [
            'id' => $row['task_id'],
            'text' => $row['text'],
            'checked' => (bool) $row['checked']
        ];
        $dictionary['items'][] = $item;
    }
    return $dictionary;
}
//Checks the request.
if ($_SERVER['REQUEST_METHOD'] != 'GET')
    responseError(400);

//Get database connection.
$pdo = require __DIR__ . '/../database/createConnection.php';
//Check connection.
if ($pdo === null) {
    responseError(500);
}
//Check user is login
if (!key_exists('id', $_SESSION)) responseError(401);

$tasks = getTasks($pdo, $_SESSION['id']);
echo json_encode($tasks, true);
