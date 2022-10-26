<?php namespace App\Sport\Provider;

abstract class SportLineProvider {

    abstract function getCategories(): array;

    abstract function getRegions(string $category): array;

    abstract function getLeagueGames(string $league_id): array;

    abstract function findGame(string $id): ?SportGame;

    abstract function findMarket(string $market, string $runner): ?SportMarketHandler;

    public function findCategory(string $id): ?SportCategory {
        foreach($this->getCategories() as $category)
            if($category->id() === $id) return $category;

        return null;
    }

}
