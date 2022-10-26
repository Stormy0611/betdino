<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketBothTeamsWillScore extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === 'Обе команды забьют' && ($runner === 'Да' || $runner === 'Нет');
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());
        if($runner === 'Да' && ($data->match()->homeScore() > 0 && $data->match()->awayScore() > 0)) return $this->win();
        if($runner === 'Нет' && (($data->match()->homeScore() === 0 && $data->match()->awayScore() === 0))
            || ($data->match()->homeScore() === 0 && $data->match()->awayScore() > 0)
            || ($data->match()->awayScore() === 0 && $data->match()->homeScore() > 0)) return $this->win();
        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.bothTeamsWillScore")
            ->runner($runner === 'Да' ? 'sport.market.yes' : 'sport.market.no');
    }

}
