<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class AffiliateLog extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'affiliate_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'type', 'amount', 'currency', 'referrer'
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
