<?php

namespace App\Console\Commands;

use App\Chat;
use App\User;
use Illuminate\Console\Command;

class DeleteBots extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'win5x:deleteBots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all bots from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info('Deleting...');

        User::where('bot', true)->delete();
        Chat::where('bot', true)->delete();

        $this->info('Done');
    }

}
