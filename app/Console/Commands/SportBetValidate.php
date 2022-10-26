<?php

namespace App\Console\Commands;

use App\Currency;
use App\Sport\Provider\SportGameSnapshot;
use App\Sport\Provider\SportRadar\SportRadarData;
use App\Sport\Sport;
use App\SportBet;
use App\Transaction;
use App\User;
use Illuminate\Console\Command;

class SportBetValidate extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'win5x:validateSportBets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validates sport bets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        foreach(SportBet::where('status', 'ongoing')->get() as $sportBet) {
            if($sportBet->sportradar_id === -1) continue;

            try {
                $market = Sport::getLine()->findMarket($sportBet->market, $sportBet->runner);
                $user = User::where('_id', $sportBet->user)->first();
                if (!$market || !$user) continue;

                $snapshot = SportGameSnapshot::fromArray($sportBet->snapshot);

                if (!$market->getData($snapshot->id())->match()->isFinished()) continue;

                $status = $market->isWinner($sportBet->runner, $snapshot);

                if ($status === 'refund')
                    $user->balance(Currency\Currency::find($sportBet->currency))->add($sportBet->bet, Transaction::builder()->message('Sport Bet Outcome: Refund')->build());

                if ($status === 'win')
                    $user->balance(Currency\Currency::find($sportBet->currency))->add($sportBet->bet * $sportBet->odds, Transaction::builder()->message('Sport Bet Outcome: Win')->build());

                $sportBet->update([
                    'status' => $status
                ]);
            } catch (\Exception $e) {
                echo 'Exception ';
                echo json_encode($sportBet->toArray());
            }
        }
    }
}
