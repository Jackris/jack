<?php
abstract class Bird{
    abstract protected function fly();
    function run(){
        $this->fly();
    }
}

class A extends Bird{
    protected function fly(){
        echo 'Воробей летит';
    }
}

class B extends Bird {
    protected function fly(){
        echo 'Пин не летает';
    }
}