<?php

namespace App\Jobs\Bot\Chat;

use App\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChatBotScheduler implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle() {
        if(Settings::get('[Chat Bot] Stop', 'true', true) === 'true') return;

        $seconds = Settings::get('Message interval (seconds)', 15, true);
        $randomness = Settings::get('Message interval randomness (seconds)', 15, true);

        dispatch(new ChatBotSendMessage());
        dispatch((new ChatBotScheduler())->delay(now()->addSeconds($seconds + mt_rand(0, $randomness))));
    }

}
