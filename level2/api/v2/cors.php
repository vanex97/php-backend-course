<?php

//CORS
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: DELETE,PUT');
header('Access-Control-Max-Age: 600');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;