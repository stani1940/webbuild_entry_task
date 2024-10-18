<?php

class VendingMachine
{
    const CURRENCY_POSITION_BEFORE = 1;
    const CURRENCY_POSITION_AFTER = 2;

    private $drinks;
    private $accepted_coins = [0.05, 0.10, 0.20, 0.50, 1.00];
    private $total_amount = 0;


    public function __construct(array $currency_data, array $drinks)
    {
        if (!isset($currency_data['sign'], $currency_data['space'], $currency_data['position']) ||
            !in_array($currency_data['position'], [self::CURRENCY_POSITION_BEFORE, self::CURRENCY_POSITION_AFTER])) {
            die('Invalid currency configuration. Program stopped.');
        }

        // може ли от тук надолу поради някаква причина да няма space ?
        // според нас следващите два реда трябва да се изместят във функцията "formatCurrency()"
        $this->currency_data = $currency_data;
        // възможно ли е тук да не е масив при условие, че конструктора задължава вход на масив?
        // не е възможно премахнал съм излишната ппроверка
        // не виждаме къде и как се проверяват напитките дали отговарят на някакъв правилен формат 'string' => 'float'
        //добавил съм

        // Validate drinks
        if (empty($drinks)) {
            die('Invalid drink configuration. Program stopped.');
        }

        foreach ($drinks as $name => $price) {
            if (!is_string($name) || !is_float($price)) {
                die('Invalid drink configuration. Prices must be float and names must be strings. Program stopped.');
            }
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
        // тази променлива се пълни с монети после се занулява, но никъде не се използва и не разбрахме каква е нейната роля в задачата.-премахната

        echo "<div>Успешно поставихте " . $this->formatCurrency($coin) . ", текущата Ви сума е " . $this->formatCurrency($this->total_amount) . "</div>";
        return $this;
    }

    // тук нямаме забележки
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

    // тук нямаме забележки
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

        // защо total_amount става change, какво се е променило в него ?

        $changeCoins = $this->calculateChange($this->total_amount);

        echo "Получихте ресто " . $this->formatCurrency($this->total_amount) . " в монети от:<br>";

        foreach ($changeCoins as $coinInCents => $count) {
            if ($count > 0) {
                // Convert back from cents to float and format it
                $coin = $coinInCents / 100;
                echo "{$count}x" . $this->formatCurrency($coin) . " ";
            }
        }
        echo "<br>";

        $this->total_amount = 0;

        return $this;
    }

    private function formatCurrency(float $amount): string
    {
        $currency = $this->currency_data['sign'];
        $space = $this->currency_data['space'] ? ' ' : '';

        return $this->currency_data['position'] == self::CURRENCY_POSITION_BEFORE
            ? $currency . $space . number_format($amount, 2)
            : number_format($amount, 2) . $space . $currency;
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

    // това-> този синтаксик няма да работи на по-стари версии на php от 7.4 за това избрах втория вариант
//echo implode( ', ', array_map( fn( $coin ) => $this->formatCurrency( $coin ), $this->accepted_coins ) );
    private function displayAcceptedCoins()
    {
        echo "Автоматът приема монети от: ";

        echo implode( ', ', array_map( function( $coin )  {
            return  $this->formatCurrency( $coin );
        }, $this->accepted_coins ) );

        echo "<br><br>";
    }

}
