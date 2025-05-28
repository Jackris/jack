<?php

include '../s.php';
$input = ['a', 'b', 'c', 'd', 'e'];
$input2 = ['modus1' => 'first', 'modus2' => 'sec', 'third', 'four', 'five'];

$output = array_slice($input2, 2);      // возвращает 'c', 'd' и 'e'
s($output);
$output = array_slice($input2, -2, -1);  // возвращает 'd'
s($output);
$output = array_slice($input2, 0, 3);   // возвращает 'a', 'b' и 'c'
s($output);

// обратите внимание на различия в индексах массивов
s(array_slice($input2, 1, -1));
s(array_slice($input2, 1, -1, true));