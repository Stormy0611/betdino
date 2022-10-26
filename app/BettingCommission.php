<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class BettingCommission extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'betting_commission';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'referral', 'amount', 'currency', 'brl_amount'
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

}
