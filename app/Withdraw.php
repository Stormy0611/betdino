<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Withdraw extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'withdraws';

    protected $fillable = [
        'user', 'sum_original', 'sum', 'type', 'currency', 'address', 'status', 'decline_reason', 'usd_converted',
        'chavePix', 'cpf', 'pixType', 'whatsApp',
        'note'
    ];

}
