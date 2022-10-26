<?php namespace App\Sport\Provider;

use App\Sport\Provider\SportRadar\SportRadarData;

abstract class SportMarketHandler {

    abstract function isHandling(string $market, string $runner): bool;

    abstract function isWinner(string $runner, SportGameSnapshot $snapshot): string;

    abstract function translation(string $market, string $runner): SportMarketTranslation;

    public function getData(string $game_id): SportRadarData {
        return new SportRadarData($game_id);
    }

    protected function win(): string {
        return "win";
    }

    protected function lose(): string {
        return "lose";
    }

    protected function refund(): string {
        return "refund";
    }

}
