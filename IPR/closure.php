<?php
class LargeObject {
    protected $array;

    public function __construct() {
        $this->array = array_fill(0, 2000, 15);
    }

    public function getItemProcessor(): Closure {
        return function () { // Внутри функции любые вычисления
            $a = 1;
            $b = 2;
            return $a + $b;
        };
    }

    public function getItemProcessorHello(): Closure {
        return function () { // Внутри функции любые вычисления
            $a = 1;
            $b = 2;
            return $a + $b;
        };
    }
}

function getPeakMemory(): string
{
    return sprintf('%.2F MiB', memory_get_peak_usage() / 1024 / 1024);
}
$start = microtime(true);

$processors = [];
for ($i = 0; $i < 2000; $i++) {
    $lo = new LargeObject();
    $processors[] = $lo->getItemProcessor();
    $processors[] = $lo->getItemProcessorHello();
}

var_dump(getPeakMemory());
