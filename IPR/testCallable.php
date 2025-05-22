<?php

function s(string $text)
{
    //echo '<pre>';
    print_r($text);
    print_r("\n");
    //echo '</pre>';
}

function foo($bar)
{
    return $bar * 2;
}

$x = 'foo';
s($x(5));
s(assert(
    true === is_callable($x)
));
s(assert(
    4 == $x(2)
));

