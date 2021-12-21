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

function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
    echo "HTTP/1.1 {$statuscode} {$statusmessage}\n";
    foreach (array_keys($headers) as $key) {
        echo "{$key}: {$headers[$key]}\n";
    }
    echo "\n";
    echo $body;
}

/**
 * Returns a request with the sum of the received numbers in $body or the corresponding error.
 */
function processHttpRequest($method, $uri, $headers, $body) {
    $sumPath = "/sum";
    $numsParameter = "?nums=";
    $validMethod = "GET";

    if (substr($uri, 0, strlen($sumPath)) !== $sumPath) {
        $requestHeaders = generateRequestHeader("not found");
        outputHttpResponse("404", "Not Found", $requestHeaders, $body);
        return;
    }
    if ($method !== $validMethod || !strstr($uri, $numsParameter)) {
        $requestHeaders = generateRequestHeader("Bad Request");
        outputHttpResponse("400", "Bad Request", $requestHeaders, $body);
        return;
    }
    $nums = explode(",", substr($uri, strpos($uri, $numsParameter) + strlen($numsParameter)));
    $numsSum = array_sum($nums);
    $requestHeaders = generateRequestHeader($numsSum);
    outputHttpResponse("200", "OK", $requestHeaders, $numsSum);
}

/**
 * Generates headers for the example.
 */
function generateRequestHeader($body) {
    return array(
        "Date" => date(DATE_RFC822),
        "Server" => "Apache/2.2.14 (Win32)",
        "Connection" => "Closed",
        "Content-Type" => "text/html; charset=utf-8",
        "Content-Length" => strlen($body),
    );
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
        $headers[] = explode(": ", $stringLineArr[$i]);
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