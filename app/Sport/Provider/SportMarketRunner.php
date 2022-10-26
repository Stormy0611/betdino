<?php namespace App\Sport\Provider;

use App\Sport\Sport;

abstract class SportMarketRunner {

    abstract function marketName();

    abstract function name(): string;

    abstract function isOpen(): bool;

    abstract function price(): float;

    abstract function supported(): bool;

    public function toArray() {
        $translation = Sport::getLine()->findMarket($this->marketName(), $this->name());

        return [
            'name' => $this->name(),
            'open' => $this->isOpen(),
            'price' => $this->price(),
            'supported' => $this->supported(),
            'translation' => $translation ? $translation->translation($this->marketName(), $this->name())->toArray() : null
        ];
    }

}
