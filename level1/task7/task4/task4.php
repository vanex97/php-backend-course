<?php

function getDB($filePath) {
    if (file_exists($filePath)) {
        return explode("\n", file_get_contents($filePath));
    }
    return false;
}

$DB = getDB("./passwords.txt");

if ($DB === false) {
    http_response_code(500);
    exit();
}

if (array_key_exists("login", $_POST) &&
    array_key_exists("password", $_POST) &&
    array_search("{$_POST["login"]}:{$_POST["password"]}", $DB) !== false) {
    echo '<h1 style="color:green">FOUND</h1>';
}
else {
    http_response_code(401);
}

