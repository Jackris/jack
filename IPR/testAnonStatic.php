<?php
class Foo
{
    function __construct()
    {
        $func = static function() {
            var_dump(8);
        };

        $func();
    }
};

new Foo();