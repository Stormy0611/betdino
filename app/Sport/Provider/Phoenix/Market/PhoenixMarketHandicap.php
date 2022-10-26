<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketHandicap extends SportMarketHandler {

    function isHandling(string $market, string $runner): bool {
        if((str_starts_with($runner, "1") || str_starts_with($runner, "2"))
            && str_contains($runner, "(") && str_contains($runner, ")")) {
            $m = intval(explode(" ", $runner)[0]);
            $v = explode(" ", $runner)[1];
            $v = substr($v, 1, strlen($v) - 1);

            return ($market === 'Фора') && ($m == 1 || $m == 2) && ($v === '0' || $v === '-0.5' || $v === '-1' || $v === '-1.5'
                    || $v === '+0.5' || $v === '+1' || $v === '+1.5');
        }

        return false;
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());

        $m = intval(explode(" ", $runner)[0]);
        $v = explode(" ", $runner)[1];
        $v = substr($v, 1, strlen($v) - 1);

        if($m == 1) {
            if($v == '0' && $data->match()->winner() === 'home') return $this->win();
            if($v == '0' && $data->match()->winner() === 'draw') return $this->refund();

            if($v == '-0.5' && $data->match()->winner() === 'home') return $this->win();

            if($v == '-1') {
                if($data->match()->winner() === 'home' && $data->match()->homeScore() >= 2) return $this->win();
                if($data->match()->winner() === 'home' && $data->match()->homeScore() == 1) return $this->refund();
                return $this->lose();
            }

            if($v == '-1.5') {
                if($data->match()->winner() === 'home' && $data->match()->homeScore() >= 2) return $this->win();
                return $this->lose();
            }

            if($v == '+0.5' && $data->match()->winner() === 'away') return $this->win();

            if($v == '+1') {
                if($data->match()->winner() === 'away' && $data->match()->awayScore() >= 2) return $this->win();
                if($data->match()->winner() === 'away' && $data->match()->awayScore() == 1) return $this->refund();
                return $this->lose();
            }

            if($v == '+1.5') {
                if($data->match()->winner() === 'away' && $data->match()->awayScore() >= 2) return $this->win();
                return $this->lose();
            }
        }

        if($m == '2') {
            if($v == '0' && $data->match()->winner() === 'away') return $this->win();
            if($v == '0' && $data->match()->winner() === 'draw') return $this->refund();

            if($v == '-0.5' && $data->match()->winner() === 'away') return $this->win();

            if($v == '-1') {
                if($data->match()->winner() === 'away' && $data->match()->awayScore() >= 2) return $this->win();
                if($data->match()->winner() === 'away' && $data->match()->awayScore() == 1) return $this->refund();
                return $this->lose();
            }

            if($v == '-1.5') {
                if($data->match()->winner() === 'away' && $data->match()->awayScore() >= 2) return $this->win();
                return $this->lose();
            }

            if($v == '+0.5' && $data->match()->winner() === 'home') return $this->win();

            if($v == '+1') {
                if($data->match()->winner() === 'home' && $data->match()->homeScore() >= 2) return $this->win();
                if($data->match()->winner() === 'home' && $data->match()->homeScore() == 1) return $this->refund();
                return $this->lose();
            }

            if($v == '+1.5') {
                if($data->match()->winner() === 'home' && $data->match()->homeScore() >= 2) return $this->win();
                return $this->lose();
            }
        }

        return $this->lose();
    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->market("sport.market.handicap")->runner($runner);
    }

}
