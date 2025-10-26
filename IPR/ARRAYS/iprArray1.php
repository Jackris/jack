<?php
$products = [
    ['name' => 'Наушники Bluetooth',         'price' => 2500, 'discount_percent' => 0],
    ['name' => 'Мышь проводная',             'price' => 800, 'discount_percent' => 0],
    ['name' => 'Клавиатура механическая',     'price' => 4200, 'discount_percent' => 5],
    ['name' => 'USB-хаб',                     'price' => 1200, 'discount_percent' => 0],
    ['name' => 'Чехол для ноутбука',         'price' => 950, 'discount_percent' => 10],
    ['name' => 'Смартфон X200',                'price' => 25000, 'discount_percent' => 10],
    ['name' => 'Планшет MiniTab',             'price' => 18000, 'discount_percent' => 15],
    ['name' => 'Ноутбук LightBook',            'price' => 56000, 'discount_percent' => 5],
    ['name' => 'Умные часы FitWatch',         'price' => 7500, 'discount_percent' => 20],
    ['name' => 'Фитнес-браслет StepUp',        'price' => 3200, 'discount_percent' => 25],
    ['name' => 'Рюкзак городской',             'price' => 1900, 'discount_percent' => 0],
    ['name' => 'Подставка для ноутбука',     'price' => 1100, 'discount_percent' => 0],
    ['name' => 'Внешний аккумулятор',         'price' => 2100, 'discount_percent' => 10],
    ['name' => 'Карта памяти 128GB',         'price' => 1300, 'discount_percent' => 0],
    ['name' => 'Зарядное устройство USB-C',    'price' => 1450, 'discount_percent' => 5],
    ['name' => 'Монитор 27"',                 'price' => 22000, 'discount_percent' => 8],
    ['name' => 'Коврик для мыши',             'price' => 600, 'discount_percent' => 0],
    ['name' => 'Гарнитура с микрофоном',     'price' => 3100, 'discount_percent' => 12],
    ['name' => 'HDMI-кабель 2m',             'price' => 700, 'discount_percent' => 0],
    ['name' => 'SSD накопитель 512GB',         'price' => 4800, 'discount_percent' => 7],
];

/*print_r(array_filter($products,static function($var){
    return $var['price'] < 1000;
}));*/

print_r(array_map(static function($var){
    $var['final_price'] = $var['price'] + 100;
    return $var;
},$products));