<?php

class Cart
{
    const PRICE_BUTTER = 300;
    const PRICE_MILK = 88;
    const PRICE_EGGS = 135;

    protected $products = array();

    public function add($product, $quantity)
    {
        $this->products[$product] = $quantity;
    }

    public function getQuantity($product)
    {
        return isset($this->products[$product]) ? $this->products[$product] :
            FALSE;
    }

    public function getTotal($tax)
    {
        $total = 0.00;

        $callback = function ($quantity, $product) use ($tax, &$total) {
            $pricePerItem = constant(
                __CLASS__ . "::PRICE_" . strtoupper($product)
            );

            $total += ($pricePerItem * $quantity) * ($tax + 1.0);
        };
        print_r($tax);
        print_r("\n");
        print_r($total);

        array_walk($this->products, $callback);
        return round($total, 2);
    }
}

$my_cart = new Cart();

// Добавляем элементы в корзину
$my_cart->add('butter', 1);
$my_cart->add('milk', 3);
$my_cart->add('eggs', 6);

// Выводим общую сумму с налогом 5 % на продажу
$my_cart->getTotal(0.05);
print_r("\n");
print $my_cart->getTotal(0.05) . "\n";
// Результат будет равен 54.29
