<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketExactScoreNoOutcomeOther extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === 'Точный счет (без исхода Любой другой)' && str_contains($runner, ":");
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());
        if($runner === $data->match()->homeScore() . ':' . $data->match()->awayScore())
            return $this->win();
        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.exactScoreNoOutcomeOther")->runner($runner);
    }

}
