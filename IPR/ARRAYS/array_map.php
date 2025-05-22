<?php
function cube($n)
{
    print_r($n);
    return ($n * $n * $n);
}

$a = [1, 2, 3, 4, 5];
$b = array_map('cube', $a);
//print_r($b);


function show_Spanish(int $n, string $m): string
{
    return "Число {$n} по-испански называется {$m}";
}

function map_Spanish(int $n, string $m): array
{
    return [$n => $m];
}

$a = [1, 2, 3, 4, 5];
$b = ['uno', 'dos', 'tres', 'cuatro', 'cinco'];

$c = array_map('show_Spanish', $a, $b);
print_r($c);

$d = array_map('map_Spanish', $a, $b);
print_r($d);
