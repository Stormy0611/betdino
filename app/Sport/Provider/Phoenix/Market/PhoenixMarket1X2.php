<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarket1X2 extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === '1X2' && in_array($runner, ["1", "X", "2"]);
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        return (($data->match()->winner() === 'draw' && $runner === 'X')
            || ($data->match()->winner() === 'home' && $runner === '1')
            || ($data->match()->winner() === 'away' && $runner === '2')) ? $this->win() : $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->same($market, $runner);
    }

}
