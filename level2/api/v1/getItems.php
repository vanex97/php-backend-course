<?php

require_once 'cors.php';
require_once 'database/jsonDBController.php';
require_once 'errors.php';

//Checks the request.
if ($_SERVER['CONTENT_TYPE'] !== 'application/json;' || $_SERVER['REQUEST_METHOD'] != 'POST')
    responseError(400);

$taskList = getJsonDB();

if ($taskList === null) {
    responseError(500);
}
$items = ["items" => $taskList['items']];

echo json_encode($items);