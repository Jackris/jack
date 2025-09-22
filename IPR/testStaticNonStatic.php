<?php
class First
{
    public static $first_static = 'First';

    public function staticValue() {
        return self::$first_static;
    }
}

class Second extends First
{
    public static $first_static = 'Second';
    public string $secondNonStatic = 'secondNonStatic';
    public static function secondStatic() {
        return self::$first_static;
    }

    public static function setStaticVar(string $val) {
        self::$first_static = $val;
    }
}

$second = new Second();
$first = new First();

$second->setStaticVar('NewValue');
print Second::$first_static . "\n";
print $second::$first_static . "\n";
Second::setStaticVar('NewValue2');
print Second::$first_static . "\n";
print $second::$first_static . "\n";
