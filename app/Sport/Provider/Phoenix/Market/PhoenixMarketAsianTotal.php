<?php namespace App\Sport\Provider\Phoenix\Market;

use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportMarketHandler;
use App\Sport\Provider\SportMarketTranslation;

class PhoenixMarketAsianTotal extends SportMarketHandler {

    /**
     *
     *
     *    TODO
     *
     *
     */

    function isHandling(string $market, string $runner): bool {
        return $market === 'Азиатский тотал' && (str_starts_with($runner, "Меньше") || str_starts_with($runner, "Больше"));
    }

    function isWinner(string $runner, SportGameSnapshot $snapshot): string {
        $data = $this->getData($snapshot->id());


    }

    public function translation(string $market, string $runner): SportMarketTranslation {
        return (new SportMarketTranslation())->same($market, $runner);
    }

}
