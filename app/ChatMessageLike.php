<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatMessageLike extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'chat_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'message'
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
