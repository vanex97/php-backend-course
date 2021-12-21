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
echo(json_encode($http, JSON_PRETTY_PRINT));