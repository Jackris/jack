<?php
function odd($var)
{
    // Функция возвращает информацию о том, нечётно ли входное целое число
    return $var & 1;
}

function even($var)
{
    print_r($var / 2);
    echo "\n";
    // Функция возвращает информацию о том, чётно ли входное целое число
    return !($var & 1);
}

$array1 = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
$array2 = [6, 7, 8, 9, 10, 11, 12];

echo "Нечётные:\n";
print_r(array_filter($array1, "odd"));

echo "Чётные:\n";
print_r(array_filter($array2, "even"));
/*print_r(5 | 2);
print_r(5 & 2);
print_r(10 | 1);*/
print_r(6 & 1);