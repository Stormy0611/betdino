<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketSetWinner extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return substr($market, 1) === '-й сет: Победитель' && ($runner === '1' || $runner === '2');
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());
        $set = intval(substr($snapshot->market(), 0, 1));

        if($runner === '1' && ($data->match()->period($set)->homeScore() > $data->match()->period($set)->awayScore())) return $this->win();
        if($runner === '2' && ($data->match()->period($set)->awayScore() > $data->match()->period($set)->homeScore())) return $this->win();
        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.setWinner", ['set' => substr($market, 0, 1)])->runner($runner);
    }

}
