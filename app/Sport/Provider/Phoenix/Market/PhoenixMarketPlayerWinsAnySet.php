<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketPlayerWinsAnySet extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        return ($market === 'Первый игрок выиграет один из сетов' || $market === 'Второй игрок выиграет один из сетов') && ($runner === 'Да' || $runner === 'Нет');
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        for($period = 1; $period <= $data->match()->periods(); $period++) {
            $periodData = $data->match()->period($period);

            if($snapshot->market() === 'Первый игрок выиграет один из сетов') {
                if($runner === 'Да' && ($periodData->homeScore() > $periodData->awayScore())) return $this->win();
                else if($runner === 'Нет') return $this->win();
            }

            if($snapshot->market() === 'Второй игрок выиграет один из сетов') {
                if($runner === 'Да' && ($periodData->awayScore() > $periodData->homeScore())) return $this->win();
                else if($runner === 'Нет') return $this->win();
            }
        }

        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market($market === 'Первый игрок выиграет один из сетов' ? 'sport.market.firstPlayerWinsAnySet' : 'sport.market.secondPlayerWinsAnySet')->runner($runner === 'Да' ? 'sport.market.yes' : 'sport.market.no');
    }

}
