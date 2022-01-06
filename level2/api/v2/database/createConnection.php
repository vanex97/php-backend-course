<?php

$user = 'root';
$password = 'root';
$dbName = 'taskDB';
$host = '127.0.0.1';
$port = '3306';

$mysqli = new mysqli($host, $user, $password, $dbName, $port);

//Check connection
if ($mysqli -> connect_errno) {
    return null;
}
return $mysqli;