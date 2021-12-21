<?php

/**
 * Accepts an http request as a string.
 * @return string Data from the entered http request.
 */
function readHttpLikeInput()
{
    $f = fopen('php://stdin', 'r');
    $store = "";
    $toread = 0;
    while ($line = fgets($f)) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/', $line, $m))
            $toread = $m[1] * 1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

/**
 * Returns files from the base folder depending on $ host.
 */
function processHttpRequest($method, $uri, $headers, $body)
{
    $validMethod = "GET";

    if (strpos($uri, "..") !== false) {
        $body = "forbidden";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("403", "Forbidden", $requestHeaders, $body);
        return;
    }

    if ($method !== $validMethod || key_exists("Host", $headers)) {
        $body = "Bad Request";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("400", "Bad Request", $requestHeaders, $body);
        return;
    }

    $file = getFileWithHost($headers["Host"], ["student.shpp.me", "another.shpp.me"], $uri);

    if ($file === false) {
        $body = "not found";
        $requestHeaders = generateRequestHeader($body);
        outputHttpResponse("404", "Not Found", $requestHeaders, $body);
        return;
    }

    $requestHeaders = generateRequestHeader($file);
    outputHttpResponse("200", "OK", $requestHeaders, $file);
}

function getFileWithHost($host, $supportedHosts, $uri) {
    if (array_search($host, $supportedHosts) === false)
        return false;

    $hostName = substr($host, 0, strpos($host, "."));
    if ($uri === "/")
        $filePath = "./$hostName/index.html";
    else
        $filePath = "./$hostName$uri";

    if (file_exists($filePath) === false)
        return false;

    return file_get_contents($filePath);
}

/**
 * Generates headers for the example.
 */
function generateRequestHeader($host, $body = false)
{
    $request = array(
        "Host" => $host,
        "Accept" => "image/gif, image/jpeg, */*",
        "Accept-Language" => "en-us",
        "Accept-Encoding" => "gzip, deflate",
        "User-Agent" => "Mozilla/4.0",
    );
    if ($body !== false) {
        $request["Content-Length"] = strlen($body);
    }
    return $request;
}

function parseBodyValues($body)
{
    $bodyValues = [];
    foreach (explode("&", $body) as $value) {
        $values = explode("=", $value);
        $bodyValues[$values[0]] = $values[1];
    }
    return $bodyValues;
}

function outputHttpResponse($statuscode, $statusmessage, $headers, $body)
{
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
function parseTcpStringAsHttpRequest($string)
{
    $stringLineArr = explode("\n", $string);
    $firstLineValues = explode(" ", $stringLineArr[0]);

    $headers = [];
    for ($i = 1; $i < count($stringLineArr) - 1; $i++) {
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