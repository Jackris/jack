<?php

abstract class Car
{
    // Эти методы потребуется определить в дочернем классе
    abstract protected function getValue();

    abstract public function getTypeOfLicence($necces);

    // Общий метод
    public function printOut()
    {
        print $this->getValue() . "\n";
    }
}

class Truck extends Car
{
    protected function getValue()
    {
        return "TruckClass";
    }

    public function getTypeOfLicence($necces)
    {
        print 'D type of licence'. $necces . "\n";
    }

}

class Light extends Car
{
    public function getValue()
    {
        return "LightCarClass";
    }

    public function getTypeOfLicence($arg = 'default')
    {
        print 'B type of licence' . $arg;
    }

    public function printOut()
    {
        print 'MORPH' . "\n";
    }
}

$truck = new Truck();
$truck->printOut();

$truck->getTypeOfLicence('hello');

$light = new Light();
$light->printOut();
$light->getTypeOfLicence();

