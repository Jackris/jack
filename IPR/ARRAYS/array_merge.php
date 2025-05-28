<?php

include '../s.php';
$array1 = ["color" => "red", 2, 4, 'wut' => ['meme' => 'hello']];
$array2 = ["a", "b", "color" => "green", "wut" => "trapezoid", 4];
//$result = array_merge_recursive($array1, $array2);
$result = array_merge_recursive($array1, $array2);
s($result);