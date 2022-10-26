<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketDoubleOutcome extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === 'Двойной исход' && in_array($runner, ["1X", "12", "Х2"]);
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        if($runner === '1X' && ($data->match()->winner() === 'home' || $data->match()->winner() === 'draw')) return $this->win();
        if($runner === '12' && ($data->match()->winner() === 'home' || $data->match()->winner() === 'away')) return $this->win();
        if($runner === 'Х2' && ($data->match()->winner() === 'draw' || $data->match()->winner() === 'away')) return $this->win();
        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.doubleOutcome")->runner($runner);
    }

}
