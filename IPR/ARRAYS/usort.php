<?php
function cmp($a, $b)
{
    echo $a . PHP_EOL;
    echo $b . PHP_EOL;
    echo '___' . PHP_EOL;
    if ($a == $b) {
        return 0;
    }

/*    echo ($a < $b) ? -1 : 1;
    echo PHP_EOL;
    echo '___' . PHP_EOL;*/

    return ($a % 2 == 0) ? -1 : 1;
}

$arr = array(9, 3, 8, 6, 1);

usort($arr, "cmp");
print_r($arr);
//print_r();
/*foreach ($a as $key => $value) {
    echo "$key: $value\n";
}*/