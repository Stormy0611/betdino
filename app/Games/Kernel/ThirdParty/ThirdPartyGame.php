<?php namespace App\Games\Kernel\ThirdParty;

use App\Games\Kernel\Game;
use App\Games\Kernel\ProvablyFairResult;
use App\Games\Kernel\ThirdParty\Phoenix\PhoenixGame;
use App\Utils\Exception\UnsupportedOperationException;

/**
 * Class ThirdPartyGame
 *
 * Game metadata should follow this format:
 * 1. id: Should start with provider id, be divided with ":" and end with game id (numeric or with an actual game id), eg. "provider:232" or "provider:anotherslotsgame"
 * 2. icon: Icon is replaced by game image. Metadata category will define game icon.
 *
 * @package App\Games\Kernel\ThirdParty
 */
abstract class ThirdPartyGame extends Game {

    protected ?array $data;

    public function __construct(?array $data = null) {
        $this->data = $data;
    }

    abstract function provider(): string;

    /**
     * Creates array of ThirdPartyGame instances.
     * @return array of ThirdPartyGame instances based on it's provider
     */
    public abstract function createInstances(): array;

    public function result(ProvablyFairResult $result): array {
        throw new UnsupportedOperationException();
    }

    public static function providers(): array {
        return [
            new PhoenixGame()
        ];
    }

    public static function findProvider(string $id) {
        foreach (self::providers() as $provider) if($provider->provider() === $id) return $provider;
        return null;
    }

}
