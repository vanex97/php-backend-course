<?php

require_once 'database/jsonDBController.php';
require_once 'errors.php';
require_once 'cors.php';

//Checks the request
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'DELETE')
    responseError(400);

//Get and check database.
$taskList = getJsonDB();
if ($taskList === null) {
    responseError(500);
}
//Get and decode request data
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);
//Check get data is valid.
if (!key_exists('id', $data))
    responseError(400);

//Looks for the job to be deleted in task list and deletes it.
foreach ($taskList['items'] as $key => $val) {
    if ($val['id'] === $data['id']) {
        unset($taskList['items'][$key]);
        $taskList['items'] = array_values($taskList['items']);
        putJsonDB($taskList);
        break;
    }
}
echo json_encode(['ok' => true]);