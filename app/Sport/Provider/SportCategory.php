<?php namespace App\Sport\Provider;

abstract class SportCategory {

    abstract function id(): string;

    abstract function icon(): String;

    abstract function getGames(): array;

    public function toArray(): array {
        $games = [];

        foreach($this->getGames() as $game)
            array_push($games, $game->toArray());

        return [
            'id' => $this->id(),
            'icon' => $this->icon(),
            'games' => $games
        ];
    }

}
