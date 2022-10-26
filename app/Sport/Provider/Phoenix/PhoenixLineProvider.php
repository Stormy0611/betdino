<?php namespace App\Sport\Provider\Phoenix;

use App\Sport\Provider\Phoenix\Market\PhoenixMarket1X2;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketAsianTotal;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketBasketballDryWin;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketBothTeamsWillScore;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketDoubleOutcome;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketExactScore;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketExactScoreNoOutcomeOther;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketHandicap;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketPlayerWinsAnySet;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketSetWinner;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketWinner;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketWinnerWithoutDraw;
use App\Sport\Provider\Phoenix\Market\PhoenixMarketWinnerWithOvertimes;
use App\Sport\Provider\SportCategory;
use App\Sport\Provider\SportGame;
use App\Sport\Provider\SportLeague;
use App\Sport\Provider\SportLineProvider;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportRegion;
use App\Sport\Sport;

class PhoenixLineProvider extends SportLineProvider {

    public static string $apiKey = "PhoenixGambling-Win5X-z!3akOP4AX2Hazy71lUUoQasVcL02";

    function getCategories(): array {
        $json = json_decode(Sport::cachedRequest("https://phoenix-gambling.com/proxy/categories/" . PhoenixLineProvider::$apiKey, 60 * 60), true);
        $result = [];

        foreach($json as $value)
            array_push($result, new PhoenixCategory(preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]/',' $0', $value['family'])));

        return $result;
    }

    public function getAll(): SportCategory {
        return new PhoenixCategory("all");
    }

    function findGame(string $id): ?SportGame {
        try {
            $json = json_decode(Sport::cachedRequest("https://phoenix-gambling.com/proxy/game/" . $id . "/" . PhoenixLineProvider::$apiKey, 2), true);
            if (!isset($json['markets'])) return null;
            return new PhoenixGame($json, false);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getLeagueGames(string $league_id): array {
        $data = json_decode(Sport::cachedRequest("https://phoenix-gambling.com/proxy/league/" . $league_id . "/" . PhoenixLineProvider::$apiKey, 2), true);
        $result = [];

        foreach ($data['data'] as $data) {
            if (!isset($data['markets'])) continue;
            array_push($result, new PhoenixGame($data, false));
        }

        return $result;
    }

    function findMarket(string $market, string $runner): ?SportMarketHandler {
        $array = [
            new PhoenixMarket1X2(),
            new PhoenixMarketWinner(),
            new PhoenixMarketWinnerWithoutDraw(),
            new PhoenixMarketExactScore(),
            new PhoenixMarketBothTeamsWillScore(),
            new PhoenixMarketDoubleOutcome(),
            new PhoenixMarketHandicap(),
            new PhoenixMarketExactScoreNoOutcomeOther(),
            new PhoenixMarketWinnerWithOvertimes(),
            new PhoenixMarketSetWinner(),
            new PhoenixMarketBasketballDryWin(),
            new PhoenixMarketPlayerWinsAnySet()
            // TODO new PhoenixMarketTotal(),
            // TODO new PhoenixMarketAsianTotal()
        ];

        foreach($array as $handler) if($handler->isHandling($market, $runner)) return $handler;
        return null;
    }

    public function getRegions(string $category): array {
        $result = [];

        $data = json_decode(Sport::cachedRequest("https://phoenix-gambling.com/proxy/categories/" . PhoenixLineProvider::$apiKey, 2), true);

        foreach($data as $family) {
            if($family['family'] !== str_replace(" ", "", $category)) continue;

            foreach($family['regions'] as $region) {
                array_push($result, new class($region) extends SportRegion {
                    private array $region;

                    public function __construct($region) {
                        $this->region = $region;
                    }

                    function name(): string {
                        return $this->region['nameDefault'];
                    }

                    function getLeagues(): array {
                        $leagues = [];

                        foreach ($this->region['leagues'] as $league) {
                            array_push($leagues, new class($league) extends SportLeague {
                                private $league;

                                public function __construct($league) {
                                    $this->league = $league;
                                }

                                function id(): ?int {
                                    return $this->league['id'];
                                }

                                function name(): string {
                                    return $this->league['nameDefault'];
                                }
                            });
                        }

                        return $leagues;
                    }
                });
            }
        }

        return $result;
    }

}
