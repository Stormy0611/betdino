<?php

use App\Events\LiveFeedSportGame;
use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Sport;
use App\Transaction;
use App\Utils\APIResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('categories', function() {
   $result = [];
   foreach(Sport::getLine()->getCategories() as $category) array_push($result, $category->toArray());
   return $result;
});

Route::post('live', function(Request $request) {
    $request->validate(['type' => 'required']);

    $category = Sport::getLine()->findCategory($request->type);
    if($category == null) return APIResponse::reject(1, 'Invalid category');

    return APIResponse::success($category->toArray());
});

Route::post('regions', function(Request $request) {
    $request->validate(['category' => 'required']);

    $regions = Sport::getLine()->getRegions($request->category);
    $result = [];

    foreach($regions as $region)
        array_push($result, $region->toArray());

    return APIResponse::success($result);
});

Route::post('line', function(Request $request) {
    $result = [];

    foreach(Sport::getLine()->getLeagueGames($request->league_id) as $game)
        array_push($result, $game->toArray());

    return APIResponse::success($result);
});

Route::post('game', function(Request $request) {
    $request->validate(['id' => 'required']);

    $game = Sport::getLine()->findGame($request->id);
    if($game == null) return APIResponse::reject(1, "Invalid game");
    return APIResponse::success($game->toArray());
});

Route::middleware('auth:sanctum')->post('bet', function(Request $request) {
    $request->validate([
        'type' => 'required',
        'bets' => 'required'
    ]);

    $type = $request->type;

    if($type !== 'multi' && $type !== 'single') return APIResponse::reject(4, 'Invalid betting type: only single/multi are accepted as input value');

    foreach($request->bets as $bet) {
        $game = Sport::getLine()->findGame($bet['game']['id']);

        if($game == null) return APIResponse::reject(2, 'Invalid game id');

        $category = Sport::getLine()->findCategory($bet['category']);
        if($category == null) return APIResponse::reject(2, 'Invalid category');

        $market = null;
        $runner = null;

        foreach($game->markets() as $gameMarket) {
            if($gameMarket->name() === $bet['market']['name']) {
                foreach($gameMarket->getRunners() as $gameRunner) {
                    if($gameRunner->name() === $bet['runner']['name']) {
                        $market = $gameMarket;
                        $runner = $gameRunner;
                        break;
                    }
                }
            }
        }

        if($market == null || $runner == null)
            return APIResponse::reject(1, 'Market or runner does not exist');

        if(!$market->isOpen() || !$runner->isOpen() || !$game->isOpen())
            return APIResponse::reject(1, 'This game does not accept bets');

        if(!$runner->supported())
            return APIResponse::reject(5, 'This runner is not supported by BetDino API');

        if(auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->get() < ($type === 'single' ? floatval($bet['value']) : floatval($request->multiBetValue)))
            return APIResponse::reject(3, 'Not enough balance');

        auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->subtract(($type === 'single' ? floatval($bet['value']) : floatval($request->multiBetValue)), Transaction::builder()->message('Sport Bet, sportradar id: ' . $game->sportRadarId())->get());

        $sportBet = \App\SportBet::create([
            'user' => auth('sanctum')->user()->_id,
            'sportradar_id' => $game->sportRadarId(),
            'status' => 'ongoing',
            'game_id' => $game->id(),
            'game' => $game->name(),
            'market' => $bet['market']['name'],
            'runner' => $bet['runner']['name'],
            'odds' => $runner->price(),
            'bet' => ($type === 'single' ? floatval($bet['value']) : floatval($request->multiBetValue)),
            'currency' => auth('sanctum')->user()->clientCurrency()->id(),
            'category' => $category->id(),
            "icon" => $category->icon(),
            'snapshot' => SportGameSnapshot::createSnapshot($game, $bet['market']['name'])->toArray()
        ]);

        event(new LiveFeedSportGame($sportBet));
    }

    return APIResponse::success();
});

Route::get('/image/{link}', function($link) {
    $link = Crypt::decryptString($link);
    $fileContent = file_get_contents($link);

    return response()->stream(function() use($fileContent) {
        echo $fileContent;
    }, 200, ['Content-Type' => 'image/png']);
});
