<?php namespace App\Games\Kernel\ThirdParty\Phoenix;

use App\BonusBalance;
use App\Currency\Currency;
use App\Currency\Local\LocalCurrency;
use App\Games\Kernel\Data;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\ThirdParty\ThirdPartyGame;
use App\Leaderboard;
use App\Settings;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhoenixGame extends ThirdPartyGame {

    private string $apiKey = "6da77c10-e558-4758-8ef4-79a48d526337";

    public function provider(): string {
        return $this->data && $this->data['category'] === 'Slots' ? 'Slots (Originals)' : 'Originals';
    }

    function metadata(): Metadata {
        return new class($this->data) extends Metadata {
            private ?array $data;

            public function __construct(?array $data) {
                $this->data = $data;
            }

            function id(): string {
                return "external:".(!$this->data ? 'dummy' : $this->data['id']);
            }

            function name(): string {
                return $this->data ? $this->data['name'] : 'Dummy';
            }

            function icon(): string {
                return 'slots';
            }

            public function image(): string {
                return 'https://phoenix-gambling.com'.$this->data['thumbnail'];
            }

            public function category(): array {
                $categories = [ GameCategory::$originals ];
                if($this->data['category'] === 'Slots') $categories[] = GameCategory::$slots;
                if($this->data['category'] === 'Multiplayer') $categories[] = GameCategory::$multiplayer;
                return $categories;
            }

            public function isPlaceholder(): bool {
                return $this->data['comingSoon'];
            }
        };
    }

    public function processCallback(Request $request): array {
        /** @var User $user */

        $playerId = $request->input('session')['playerId'];
        $bonus = null;

        if(str_contains($playerId, "|")) {
            $idSplit = explode("|", $playerId);
            $playerId = $idSplit[1];
            $bonus = BonusBalance::where('_id', $idSplit[0])->first();

            if($bonus == null) return [];
        }

        $user = User::where('_id', $playerId)->first();
        $currencyId = $request->input('session')['currency'];

        $bet = $request->game['bet'];
        $id = $request->game['id'];
        $gameId = $request->input('session')['gameId'];

        $currency = Currency::getByName($currencyId)[0];
        $userBalance = $user->balance($currency);

        switch ($request->type) {
            case "start": {
                if($bonus !== null) {
                    if($bonus->value >= $bet) {
                        $userBalance->subtractBonus($bet, $bonus);
                        return ['result' => true];
                    }
                } else {
                    if ($userBalance->get() >= $bet) {
                        $userBalance->subtract($bet, Transaction::builder()->message('[Phoenix Games] Game Start')->get());
                        return ['result' => true];
                    }
                }

                return [ 'result' => false ];
            }
            case "finish": {
                $payout = $request->game['payout'];

                $game = \App\Game::create([
                    'id' => DB::table('games')->count() + 1,
                    'user' => $user->_id,
                    'game' => 'external:' . $gameId,
                    'multiplier' => $payout,
                    'status' => $request->game['status'],
                    'profit' => $bet * $payout,
                    'data' => [],
                    'type' => 'third-party',
                    'demo' => false,
                    'wager' => $bet,
                    'currency' => $currency->id(),
                    'bet_usd_converted' => $currency->convertTokenToFiat($bet)
                ]);

                if($payout != 1) {
                    if($bonus !== null) {
                        $userBalance->addBonusWager($bet, $bonus);
                    } else {
                        $user->rakeback($currency, $bet);

                        if ($user->vipLevel() > 0 && ($user->weekly_bonus ?? 0) < 100)
                            $user->update(['weekly_bonus' => ($user->weekly_bonus ?? 0) + 0.1]);
                    }
                }

                if($request->game['status'] == "win") {
                    if($bonus !== null)
                        $userBalance->addBonus($bet * $payout, $bonus, $request->data['balanceUpdateDelay']);
                    else
                        $userBalance->add($bet * $payout, Transaction::builder()->message('[Phoenix Games] x' . $payout . ' ' . $gameId)->get(), $request->data['balanceUpdateDelay']);
                }

                if($bonus === null) $user->bettingCommission($currency, $bet);

                event(new \App\Events\LiveFeedGame($game, 0));
                return [];
            }
            default: return [];
        }
    }

    function process(Data $data) {
        $currency = Currency::find($data->currency());

        $bonus = $data->user() == null ? null : $data->user()->bonus();

        return [
            'response' => [
                'id' => '-1',
                'wager' => $data->bet(),
                'type' => 'third-party',
                'link' => json_decode($this->curl('https://phoenix-gambling.com/api/game/create/' . $this->data['id'], [
                    "api_key" => $this->apiKey,
                    "playerId" => $data->guest() ? "-" : ($bonus === null ? $data->user()->_id : $bonus->_id . '|' . $data->user()->_id),
                    "playerName" => $data->guest() ? "Demo" : $data->user()->name,
                    "minBet" => $currency->convertFiatToToken(floatval(Settings::get('Min. bet amount (USD)', '0.20'))),
                    "maxBet" => $currency->convertFiatToToken(floatval(Settings::get('Max. bet amount (USD)', '100.00'))),
                    "currencyName" => $currency->name(),
                    "currencyParam" => $currency instanceof LocalCurrency ? 2 : 8,
                    "isDemo" => $data->demo()
                ]), true)['url']
            ]
        ];
    }

    public function createInstances(): array {
        if(Cache::has('phoenix:list')) {
            $result = [];

            foreach (Cache::get('phoenix:list') as $game) {
                if($game['id'] === 'FeatureTestSlot') continue;

                $result[] = new PhoenixGame($game);
            }
            return $result;
        }

        $json = json_decode($this->curl("https://phoenix-gambling.com/api/game/listFiltered", [
            'filter' => 'betdino'
        ]), true);
        $result = [];

        foreach ($json as $game) {
            $result[] = $game;
        }

        Cache::forever('phoenix:list', $result);
        return $this->createInstances();
    }

    private function curl($url, $params = null) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.88 Safari/537.36"
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        if($params != null) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

        $json_response = curl_exec($curl);

        curl_close($curl);

        return $json_response;
    }

}
