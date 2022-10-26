<?php namespace App\Currency\Local;

use App\Currency\Currency;
use Illuminate\Support\Facades\Cache;

/**
 * Not a cryptocurrency, used alongside with a Aggregator to process payments
 * @package App\Currency\Local
 */
abstract class LocalCurrency extends Currency {

     function style(): string {
        return 'lightgray';
     }

     function newWalletAddress(): string {
         return 'Unsupported operation';
     }

     function isRunning(): bool {
        return true;
     }

     function process(string $wallet = null) {
     }

     function send(string $from, string $to, float $sum) {
     }

     function setupWallet() {
     }

     function coldWalletBalance(): float {
        return -1;
     }

     function hotWalletBalance(): float {
        return -1;
     }

     public function minBet(): float {
         return 0.01;
     }

    public function tokenPrice() {
        if(!Cache::has('exchangeRates'))
            Cache::put('exchangeRates', file_get_contents("https://api.currencyapi.com/v3/latest?apikey=ykWBC7bbO513OGnxq2W8xe4nVhdpZzx8Pi4RZpUR"), now()->addHours(3));

        return json_decode(Cache::get('exchangeRates'), true)['data'][$this->alias()]['value'];
    }

    public function convertFiatToToken(float $usdAmount) {
         return $this->tokenPrice() * $usdAmount;
    }

    public function convertTokenToFiat(float $tokenAmount) {
         return $tokenAmount / $this->tokenPrice();
    }

}
