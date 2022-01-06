<?php

require_once 'database/jsonDBController.php';
require_once 'errors.php';
require_once 'cors.php';

/* Adds a new task to the dictionary */
function addToDo($dictionary, $todoText)
{
    $dictionary['lastId']++;
    $newItem = array(
        'id' => $dictionary['lastId'],
        'text' => $todoText,
        'checked' => false
    );
    $dictionary['items'][] = $newItem;
    return $dictionary;
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

//Lock and open file.
global $dbName;
$file = fopen($dbName, 'r+');
flock($file, LOCK_EX);
//Get and check database.
$taskList = getJsonDB();
if ($taskList === null) {
    fclose($file);
    responseError(500);
}

$taskList = addToDo($taskList, $data['text']);
//Put data to DB.
putJsonDB($taskList);

//Unlock file.
fclose($file);

echo json_encode(["id" => $taskList['lastId']]);
