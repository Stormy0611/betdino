<?php namespace App\WebSocket;

use App\AffiliateLog;
use App\Currency\Currency;
use App\Games\Kernel\Data;
use App\Games\Kernel\Game;
use App\Games\Kernel\ThirdParty\ThirdPartyGame;
use Illuminate\Support\Facades\DB;


class PlayWhisper extends WebSocketWhisper {

    public function event(): string {
        return "Play";
    }

    public function process($data): array {
        $game = Game::find($data->api_id);
        if($game == null) return ['code' => -3, 'message' => 'Unknown API game id'];
        if($game->isDisabled()) return ['code' => -5, 'message' => 'Game is disabled'];

        if(!$game instanceof ThirdPartyGame && $this->user != null && !$game->ignoresMultipleClientTabs() && DB::table('games')->where('game', $data->api_id)->where('user', $this->user->_id)->where('demo', false)->where('status', 'in-progress')->count() > 0) return ['code' => -8, 'message' => 'Game already has started'];

        if($this->user == null && !$data->demo) return ['code' => -2, 'message' => 'Not authorized'];

        $currency = Currency::find($data->currency);

        if(!$game instanceof ThirdPartyGame && !$game->usesCustomWagerCalculations() && floatval($data->bet) < 0.00000001) return ['code' => -1, 'message' => 'Invalid wager value'];
        if(!$game instanceof ThirdPartyGame && $this->user != null && ($this->user->balance($currency)->demo($data->demo)->get() < floatval($data->bet))) return ['code' => -4, 'message' => 'Not enough money'];

        $data = new Data($this->user, [
            'api_id' => $data->api_id,
            'bet' => $data->bet ?? 0,
            'currency' => $data->currency,
            'demo' => $data->demo,
            'quick' => $data->quick ?? false,
            'data' => (array) $data->data
        ]);

        if(!$data->demo())
            $data->user()->rakeback($currency, $data->bet());

        return $game->process($data);
    }

}

