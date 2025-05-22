<?php
include '../s.php';
function sum($carry, $item)
{
    if ($item === 5){
        return $carry;
    } /*elseif (){

    }*/
    s($carry);
    s($item);
    s('____');
    $carry += $item;

    return $carry;
}

function product($carry, $item)
{
    $carry *= $item;

    return $carry;
}

$a = [1, 3, 5, 5, 8];
$x = [];

var_dump(array_reduce($a, "sum", 100)); // int(15)
var_dump(array_reduce($a, "product", 10)); // int(1200), потому что: 10*1*2*3*4*5
var_dump(array_reduce($x, "sum", "Нет данных")); // string(19) "Нет данных"
