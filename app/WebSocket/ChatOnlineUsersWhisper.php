<?php namespace App\WebSocket;

use App\ActivityLog\ActivityLogEntry;
use Illuminate\Support\Facades\Cache;

class ChatOnlineUsersWhisper extends WebSocketWhisper {

    public function event(): string {
        return "OnlineUsersCount";
    }

    public function process($data): array {
        if(Cache::has('onlineUsersCached')) $online = Cache::get('onlineUsersCached');
        else {
            $online = count(ActivityLogEntry::onlineUsers());
            Cache::put('onlineUsersCached', $online, now()->addSeconds(15));
        }
        return [ 'online' => intval($online) ];
    }

}
