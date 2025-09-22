<?php

class AClass
{
    public static function who()
    {
        echo __CLASS__ . PHP_EOL;
    }

    public static function test()
    {
        self::who();
    }
}

class BClass extends AClass
{
    public static function who()
    {
        echo __CLASS__ . PHP_EOL;
    }
}

BClass::test();
BClass::who();
?>