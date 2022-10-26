<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class AffiliateLeaderboard extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'affiliate_leaderboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'sum'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public static function insert(User $user, float $amount) {
        $entry = AffiliateLeaderboard::where('user', $user->_id)->first();
        if($entry == null) AffiliateLeaderboard::create(['user' => $user->_id, 'sum' => $amount]);
        else $entry->update(['sum' => $entry->sum + $amount]);
    }

}
