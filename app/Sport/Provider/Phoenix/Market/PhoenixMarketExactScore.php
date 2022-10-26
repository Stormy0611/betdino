<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketExactScore extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === 'Точный счет' && (str_contains($runner, ":") || $runner === 'Любой другой');
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        if($runner === 'Любой другой') {
            foreach ($snapshot->markets() as $market) {
                foreach($market['runners'] as $runner) {
                    if($runner['name'] === $runner) return $this->lose();
                }
            }

            return $this->win();
        } else {
            if($data->match()->homeScore() . ":" . $data->match()->awayScore() === $runner) return $this->win();
            return $this->lose();
        }
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.exactScore")->runner(str_contains($runner, ":") ? $runner : "sport.market.other");
    }

}
