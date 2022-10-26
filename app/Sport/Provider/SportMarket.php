<?php namespace App\Sport\Provider;

abstract class SportMarket {

    abstract function name();

    abstract function isOpen(): bool;

    abstract function getRunners(): array;

    public function toArray() {
        $runners = [];

        foreach ($this->getRunners() as $runner)
            array_push($runners, $runner->toArray());

        return [
            'name' => $this->name(),
            'open' => $this->isOpen(),
            'runners' => $runners
        ];
    }

}
