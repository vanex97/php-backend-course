<?php
if (key_exists('nums',$_GET)) {
    $nums = explode(",", $_GET['nums']);
    echo array_sum((array)$nums);
}
else {
    http_response_code(400);
}
