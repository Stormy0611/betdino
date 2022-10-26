<?php namespace App\Sport\Provider;

abstract class SportLeague {

    abstract function id(): ?int;

    abstract function name(): string;

    public function toArray(): array {
        return [
            'id' => $this->id(),
            'name' => $this->name()
        ];
    }

}
