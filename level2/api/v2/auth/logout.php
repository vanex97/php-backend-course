<?php
require_once __DIR__ . '/../cors.php';

session_start();
session_destroy();
echo json_encode(['ok' => true]);