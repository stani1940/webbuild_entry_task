<?php

class VendingMachine
{
    const CURRENCY_POSITION_BEFORE = 1;
    const CURRENCY_POSITION_AFTER = 2;

    private $currency;
    private $currency_position;
    private $drinks;
    private $accepted_coins = [0.05, 0.10, 0.20, 0.50, 1.00];
    private $total_amount = 0;
    private $coin_invetory = [];

    public function __construct(array $currency_data, array $drinks)
    {
        if (!isset($currency_data['sign'], $currency_data['space'], $currency_data['position']) ||
            !in_array($currency_data['position'], [self::CURRENCY_POSITION_BEFORE, self::CURRENCY_POSITION_AFTER])) {
            die('Invalid currency configuration. Program stopped.');
        }

        $this->currency = $currency_data['sign'] . ($currency_data['space'] ? ' ' : '');
        $this->currency_position = $currency_data['position'];

        // Validate drinks
        if (empty($drinks) || !is_array($drinks)) {
            die('Invalid drink configuration. Program stopped.');
        }
        $this->drinks = $drinks;
    }

    public function viewDrinks(): VendingMachine
    {
        echo "<div style=' font-weight: bold;'>Напитки: </div><br>";
        foreach ($this->drinks as $drink => $price) {
            echo "<div>" . "{$drink}: " . $this->formatCurrency($price) . "<br></div";
        }

        $this->displayAcceptedCoins();

        return $this;
    }

    public function putCoin($coin): VendingMachine
    {
        if (!in_array($coin, $this->accepted_coins)) {
            $this->displayAcceptedCoins();

            return $this;
        }

        $this->total_amount += $coin;
        $this->coin_invetory[] = $coin;
        echo "<div>Успешно поставихте " . $this->formatCurrency($coin) . ", текущата Ви сума е " . $this->formatCurrency($this->total_amount) . "</div>";
        return $this;
    }

    public function buyDrink($drink): VendingMachine
    {
        if (!isset($this->drinks[$drink])) {
            echo "<div style='color: red'>Исканият продукт не е намерен.</div><br>";
            return $this;
        }

        $price = $this->drinks[$drink];
        if ($this->total_amount < $price) {
            echo "<div style='color: red'>Недостатъчна наличност.</div><br>";
            return $this;
        }

        $this->total_amount -= $price;
        echo "<pre style='font-weight: bold;'>Успешно закупихте '{$drink}' от " . $this->formatCurrency($price) . ", текущата Ви сума е " . $this->formatCurrency($this->total_amount) . "</pre><br>";
        return $this;
    }

    public function viewAmount(): VendingMachine
    {
        echo "<pre>Текущата Ви сума е " . $this->formatCurrency($this->total_amount) . "</pre>";
        return $this;
    }

    public function getCoins(): VendingMachine
    {
        if ($this->total_amount == 0) {
            echo "<div style='color: red'>Няма ресто за връщане.</div>";
            return $this;
        }

        $change = $this->total_amount;
        $changeCoins = $this->calculateChange($change);

        echo "Получихте ресто " . $this->formatCurrency($change) . " в монети от:<br>";

        foreach ($changeCoins as $coinInCents => $count) {
            if ($count > 0) {
                // Convert back from cents to float and format it
                $coin = $coinInCents / 100;
                echo "{$count}x" . $this->formatCurrency($coin) . " ";
            }
        }
        echo "<br>";

        $this->total_amount = 0;
        $this->coin_invetory = [];

        //$this->viewAmount();

        return $this;
    }

    private function formatCurrency($amount): string
    {
        return $this->currency_position == self::CURRENCY_POSITION_BEFORE
            ? $this->currency . number_format($amount, 2)
            : number_format($amount, 2) . $this->currency;
    }

    private function calculateChange(float $amount): array
    {
        $amountInCents = round($amount * 100);

        $change = [];

        $coinsInCents = array_map(function ($coin) {
            return (int) round($coin * 100);
        }, array_reverse($this->accepted_coins));

        foreach ($coinsInCents as $coinInCents) {

            $count = floor($amountInCents / $coinInCents);

            if ($count > 0) {
                $change[$coinInCents] = $count;

                $amountInCents -= $count * $coinInCents;
            }
        }

        return $change;
    }

    private function displayAcceptedCoins()
    {
        echo "Автоматът приема монети от: ";
        foreach ($this->accepted_coins as $coin) {
            echo $this->formatCurrency($coin) . ", ";
        }
        echo "<br><br>";
    }
}
