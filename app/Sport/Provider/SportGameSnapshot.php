<?php namespace App\Sport\Provider;

/**
 * Snapshot of the game status at the bet time.
 * @package App\Sport\Provider
 */
abstract class SportGameSnapshot {

    /**
     * Returns SportRadar game id
     * @return int
     */
    abstract function id(): int;

    abstract function market(): string;

    /**
     * Returns JSON-converted array by SportMarket#toArray
     * @return array converted by SportMarket#toArray
     */
    abstract function markets(): array;

    public function toArray(): array {
        return [
            'id' => $this->id(),
            'markets' => $this->markets()
        ];
    }

    public static function fromArray(array $array): SportGameSnapshot {
        return new class($array) extends SportGameSnapshot {
            private array $array;

            public function __construct($array) {
                $this->array = $array;
            }

            function id(): int {
                return $this->array['id'];
            }

            public function market(): string {
                return $this->array['market'];
            }

            function markets(): array {
                return $this->array['markets'];
            }
        };
    }

    public static function createSnapshot(SportGame $game, string $market): SportGameSnapshot {
        return new class($game, $market) extends SportGameSnapshot {
            private SportGame $game;
            private string $market;

            public function __construct($game, string $market) {
                $this->game = $game;
                $this->market = $market;
            }

            function id(): int {
                return $this->game->sportRadarId();
            }

            public function market(): string {
                return $this->market;
            }

            function markets(): array {
                return $this->game->toArray()['markets'];
            }
        };
    }

}
