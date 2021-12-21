<?php

/**
 * Accepts an http request as a string.
 * @return string Data from the entered http request.
 */
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/',$line,$m))
            $toread=$m[1]*1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

/**
 * Checks if the user is in the database and returns the result
 */
function processHttpRequest($method, $uri, $headers, $body) {
    $validUri = "/api/checkLoginAndPassword";
    $validContentType = "application/x-www-form-urlencoded";
    $validMethod = "POST";

    if ($uri !== $validUri) {
        $body = "not found";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("404", "Not Found", $requestHeaders, $body);
        return;
    }

    if ($method !== $validMethod || $validContentType !== $headers["Content-Type"]) {
        $body = "Bad Request";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("400", "Bad Request", $requestHeaders, $body);
        return;
    }

    $DB = getDB("./passwords.txt");

    if ($DB === false) {
        $body = "Internal Server Error";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("500", "Internal Server Error", $requestHeaders, $body);
        return;
    }

    $bodyValues = parseBodyValues($body);
    if (array_key_exists("login", $bodyValues) &&
        array_key_exists("password", $bodyValues) &&
        array_search("{$bodyValues["login"]}:{$bodyValues["password"]}", $DB) !== false) {
        $body = '<h1 style="color:green">FOUND</h1>';
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("200", "OK", $requestHeaders, $body);
        return;
    }
    outputHttpResponse("401", "Unauthorized", $headers, "");
}

function getDB($filePath) {
    if (file_exists($filePath)) {
        return explode("\n", file_get_contents($filePath));
    }
    return false;
}

/**
 * Generates headers for the example.
 */
function generateRequestHeader($body) {
    return array(
        "Date" => date(DATE_RFC822),
        "Server" => "Apache/2.2.14 (Win32)",
        "Content-Length" => strlen($body),
        "Connection" => "Closed",
        "Content-Type" => "text/html; charset=utf-8",
    );
}

function parseBodyValues($body) {
    $bodyValues = [];
    foreach (explode("&", $body) as $value) {
        $values = explode("=" ,$value);
        $bodyValues[$values[0]] = $values[1];
    }
    return $bodyValues;
}


function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
    echo "HTTP/1.1 $statuscode $statusmessage\n";

    foreach (array_keys($headers) as $key) {
        echo "$key: $headers[$key]\n";
    }
    echo "\n";
    echo $body;
}

/**
 * Dictionary from the entered http request.
 * @param $string
 * http request as string.
 * @return array dictionary with parsed query values.
 */
function parseTcpStringAsHttpRequest($string) {
    $stringLineArr = explode("\n", $string);
    $firstLineValues = explode(" ", $stringLineArr[0]);

    $headers = [];
    for($i = 1; $i < count($stringLineArr) - 1; $i++) {
        if ($stringLineArr[$i] !== "")
            $headersElement = explode(": ", $stringLineArr[$i]);
            $headers[$headersElement[0]] = $headersElement[1];
    }
    return array(
        "method" => $firstLineValues[0],
        "uri" => $firstLineValues[1],
        "headers" => $headers,
        "body" => end($stringLineArr),
    );
}

$contents = readHttpLikeInput();
$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);