<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketBasketballDryWin extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return $market === 'Один из сетов завершится со счетом 6:0 или 0:6' && ($runner === 'Да' || $runner === 'Нет');
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        for($period = 1; $period <= $data->match()->periods(); $period++) {
            $periodData = $data->match()->period($period);
            if($runner === 'Да' && (($periodData->homeScore() === 0 && $periodData->awayScore() === 6)
                || ($periodData->homeScore() === 6 && $periodData->awayScore() === 0))) return $this->win();
        }

        return $runner === 'Нет' ? $this->win() : $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.basketballDryWin")
            ->runner($runner === 'Да' ? "sport.market.yes" : 'sport.market.no');
    }

}
