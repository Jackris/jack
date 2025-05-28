<?php

class Math
{
    public function add(int $a, int $b): int
    {
        return $a + $b + 100;
    }

    public static function addStatic(int $a, int $b): int
    {
        return $a + $b;
    }

    public function __invoke(int $a, int $b): int
    {
        return ($a + $b) * 10;
    }
}

$math = new Math();
//print_r(1);
//Встроенные функция
//print_r(strlen(...));
//это колбеки
strlen(...);
'strlen'(...);

//Массивы
//это колбеки
$n = [$math, 'add'](...);
print_r($n(5, 8));
[Math::class, 'addStatic'](...);
//это колбеки
//Invokable объекты
$math(...);
//это колбеки
//Методы объекта и класса
$math->add(...);
Math::addStatic(...);