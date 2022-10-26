<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class SportBet extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'sport-bets';

    protected $fillable = [
        'user', 'sportradar_id', 'status', 'game_id', 'market', 'runner', 'odds', 'bet', 'currency', 'category', 'icon', 'game',
        'snapshot'
    ];

}
