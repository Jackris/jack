<?php
class Cart
{
    const PRICE_BUTTER  = 310;
    const PRICE_MILK    = 100;
    const PRICE_EGGS    = 120;

    protected $products = array();

    public function add($product, $quantity)
    {
        $this->products[$product] = $quantity;
    }

    public function getQuantity($product)
    {
        return $this->products[$product] ?? false;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getTotal($tax)
    {
        $total = 0.00;

        $callback = function ($quantity, $product) use ($tax, &$total)
        {
            $pricePerItem = constant(
                __CLASS__ . "::PRICE_" . strtoupper($product)
            );

            $total += ($pricePerItem * $quantity) * ($tax + 1.0);
        };
        //print_r($callback);
        array_walk($this->products, $callback);
        print_r($callback);

        return round($total, 2);
    }
}

$my_cart = new Cart;

// Добавляем элементы в корзину
$my_cart->add('butter', 1);
$my_cart->add('milk', 3);
$my_cart->add('eggs', 6);

// Выводим общую сумму с налогом 13 % на продажу
print $my_cart->getTotal(0.13) . "\n";

print_r ($my_cart->getProducts());