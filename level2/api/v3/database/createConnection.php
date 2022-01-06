<?php

$user = 'root';
$password = 'root';
$dbName = 'taskDB';
$host = '127.0.0.1';
$port = '3306';

try {
    return new PDO("mysql:host=$host;dbname=$dbName;port=$port", $user, $password);
} catch (Exception $e) {
    return null;
}