<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vending Machine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        .output {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h1>Вендинг Автомат</h1>
<div class="output">
<?php
require_once 'VendingMachine.php';

$machine = new VendingMachine(
    [
        'sign' => 'лв.',
        'space' => '',
        'position' => VendingMachine::CURRENCY_POSITION_AFTER,
    ],
    [
        'Milk' => 0.50,
        'Espresso' => 0.40,
        'Long Espresso' => 0.60,
    ]
);

$machine
    ->buyDrink('espresso')
    ->buyDrink('Espresso')
    ->viewDrinks()
    ->putCoin(2)
    ->putCoin(1)
    ->buyDrink('Espresso')
    ->getCoins()
    ->viewAmount()
    ->getCoins();
?>
</div>
</body>
</html>
