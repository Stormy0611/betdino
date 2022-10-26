<?php namespace App\Sport\Provider;

abstract class SportGame {

    abstract function id(): string;

    abstract function name(): string;

    abstract function isLive(): bool;

    abstract function isOpen(): bool;

    abstract function competitors(): array;

    abstract function markets(): array;

    abstract function league(): ?SportLeague;

    abstract function liveStatus(): ?SportLiveStatus;

    abstract function sportRadarId(): int;

    public function toArray(): array {
        $competitors = [];
        $markets = [];

        foreach ($this->competitors() as $competitor)
            array_push($competitors, $competitor->toArray());

        foreach ($this->markets() as $market)
            array_push($markets, $market->toArray());

        return [
            'id' => $this->id(),
            'live' => $this->isLive(),
            'data' => $this->sportRadarId(),
            'name' => $this->name(),
            'open' => $this->isOpen(),
            'competitors' => $competitors,
            'markets' => $markets,
            'league' => $this->league() ? $this->league()->toArray() : null,
            'liveStatus' => $this->liveStatus()->toArray()
        ];
    }

}
