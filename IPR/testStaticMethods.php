<?php
class Foo
{
    public static $Foo_static = 'foo';

    public function staticValue() {
        return self::$Foo_static;
    }
}

class Bar extends Foo
{
    public static $Foo_static = 'foo2';
    public static function fooStatic() {
        return self::$Foo_static;
    }

    public static function setVar(string $val) {
        self::$Foo_static = $val;
    }
}
//print Bar::$Foo_static . "\n";
$bar = new Bar();
$foo = new Foo();
Bar::$Foo_static = '55';
print $bar::fooStatic() . "\n";
$bar::setVar('hello!');

Bar::$Foo_static = '66';
print Bar::$Foo_static. "\n";
print $bar::fooStatic() . "\n";