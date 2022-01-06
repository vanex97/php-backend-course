<?php

session_set_cookie_params([
    'path' => './v3/',
    'samesite' => 'none',
    'secure' => 'true'
]);

require_once './errors.php';

function route($filename) {
    if (!file_exists($filename)) responseError(500);
    require $filename;
}

$action = $_GET['action'];

switch($action) {
    case 'getItems':
        route('./tasks/getItems.php');
        break;
    case 'addItem':
        route('./tasks/addItem.php');
        break;
    case 'changeItem':
        route('./tasks/changeItem.php');
        break;
    case 'deleteItem':
        route('./tasks/deleteItem.php');
        break;
    case 'register':
        route('./auth/register.php');
        break;
    case 'login':
        route('./auth/login.php');
        break;
    case 'logout':
        route('./auth/logout.php');
        break;
    default:
        responseError(405);
}