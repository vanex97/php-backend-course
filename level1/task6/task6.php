<?php

function getVisitNumber($filePath) {
    if (file_exists($filePath)) {
        return file_get_contents($filePath);
    }
    return false;
}

$visitNumberFileName = "./visitNumber.txt";
$visitNumber = getVisitNumber($visitNumberFileName);

if ($visitNumber !== false && is_numeric($visitNumber)) {
    echo "<h1 style='text-align: center; font-size: 100px'>$visitNumber</h1>";
    file_put_contents($visitNumberFileName, ++$visitNumber);
}
else {
    echo "Something wrong.";
}