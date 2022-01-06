<?php

$dbName = "{$_SERVER['DOCUMENT_ROOT']}/v1/database/TaskDB.json";

function getJsonDB()
{
    global $dbName;
    if (!file_exists($dbName)) return null;
    $file = file_get_contents($dbName);
    return json_decode($file, true);
}

function putJsonDB($dictionary)
{
    global $dbName;
    if ($dictionary === null) return;
    $json = json_encode($dictionary);
    if ($json !== null) file_put_contents($dbName, $json);
}
