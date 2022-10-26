<?php

use App\ActivityLog\ActivityLogEntry;
use App\AdminActivity;
use App\Currency\Currency;
use App\Games\Kernel\Game;
use App\Games\Kernel\Module\Module;
use App\Games\Kernel\ProvablyFair;
use App\Permission\RootPermission;
use App\Settings;
use App\Transaction;
use App\User;
use App\Utils\APIResponse;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use MongoDB\BSON\Decimal128;
use Spatie\Analytics\Period;

Route::post('/unbanBreak', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), 'edit')) return APIResponse::reject(1);
    User::where('_id', $request->id)->first()->update(['break' => now()]);
    return APIResponse::success();
});

Route::post('/unbanDepositBreak', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), 'edit')) return APIResponse::reject(1);
    User::where('_id', $request->id)->first()->update(['depositLimitBreak' => null, 'depositLimit' => null, 'depositLimitValue' => null]);
    return APIResponse::success();
});

Route::post('/info', function() {
    $get = function($type) {
        $total = App::make(\Arcanedev\LogViewer\Contracts\LogViewer::class)->total($type);
        return $total > 999 ? 999 : $total;
    };

    return APIResponse::success([
        'withdraws' => \App\Withdraw::where('status', 0)->count(),
        'version' => json_decode(file_get_contents(base_path('package.json')))->version,
        'logs' => [
            'critical' => $get('critical'),
            'error' => $get('error')
        ]
    ]);
});

Route::post('checkDuplicates', function(Request $request) {
    $user = User::where('_id', $request->id)->first();
    if($user->bot) return APIResponse::reject(1, 'Can\'t verify bots');

    return APIResponse::success([
        'user' => $user->makeVisible('register_multiaccount_hash')->makeVisible('login_multiaccount_hash')->toArray(),
        'same_register_hash' => \App\User::where('register_multiaccount_hash', $user->register_multiaccount_hash)->get()->toArray(),
        'same_login_hash' => \App\User::where('login_multiaccount_hash', $user->login_multiaccount_hash)->get()->toArray(),
        'same_register_ip' => \App\User::where('register_ip', $user->register_ip)->get()->toArray(),
        'same_login_ip' => \App\User::where('login_ip', $user->login_ip)->get()->toArray()
    ]);
});

Route::post('ethereumNativeSendDeposits', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission())) return APIResponse::reject(1);

    foreach (\App\Invoice::where('currency', 'native_eth')->where('redirected', '!=', true)->get() as $invoice) {
        Currency::find('native_eth')->send(User::where('_id', $invoice->user)->first()->wallet_native_eth, $request->toAddr, floatval((new Decimal128($invoice->sum))->jsonSerialize()['$numberDecimal']));
        $invoice->update([ 'redirected' => true ]);
    }
    return APIResponse::success();
});

Route::post('users/{page}', function(string $page) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);

    $result = [];

    $users = User::where('bot', '!=', true)->skip($page * 30)->take(30)->get()->makeVisible([
        'email'
    ]);

    foreach ($users as $user) {
        $result[] = array_merge([
            'inviteCode' => $user->inviteCode(),
            'numberOfInvites' => User::where('referral', $user->_id)->count(),
            'vipLevel' => $user->vipLevel(),
            'depositedTotal' => \App\Invoice::where('user', $user->_id)->where('status', 1)->sum('usd_converted'),
            'withdrawnUsdTotal' => \App\Withdraw::where('user', $user->_id)->sum('usd_converted')
        ], $user->toArray());
    }

    return APIResponse::success($result);
});

Route::post('/searchUsers', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);
    return APIResponse::success(User::where('name', 'like', "%{$request->search}%")
        ->orWhere('email', 'like', "%{$request->search}%")->get()->toArray());
});

Route::post('transactions/{user}/{page}', function(string $user, string $page) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);
    return APIResponse::success(\App\Transaction::latest()->where('user', $user)->where('demo', '!=', true)->skip(intval($page) * 30)->take(30)->get()->toArray());
});

Route::post('transactionsSearch', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);
    return APIResponse::success(Transaction::latest()->where('user', $request->user)->where('data', 'like', '%' . $request->search . '%')->get()->toArray());
});

Route::post('userAdvanced', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);

    $user = User::where('_id', $request->id)->first();

    $currencies = [];
    foreach (Currency::all() as $currency) {
        $currencies = array_merge($currencies, [
            $currency->id() => [
                'games' => DB::table('games')->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->count(),
                'wins' => DB::table('games')->where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->where('currency', $currency->id())->count(),
                'losses' => DB::table('games')->where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->where('currency', $currency->id())->count(),
                'wagered' => DB::table('games')->where('demo', '!=', true)->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->sum('wager'),
                'deposited' => DB::table('invoices')->where('status', 1)->where('user', $user->_id)->where('currency', $currency->id())->sum('sum')
            ]
        ]);
    }

    return APIResponse::success([
        'games' => DB::table('games')->where('user', $user->_id)->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->count(),
        'wins' => DB::table('games')->where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->count(),
        'losses' => DB::table('games')->where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->count(),
        'currencies' => $currencies
    ]);
});

Route::post('user', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission())) return APIResponse::reject(1);
    $user = User::where('_id', $request->id)->first();

    $currencies = [];
    foreach (Currency::all() as $currency) {
        $currencies = array_merge($currencies, [
            $currency->id() => [
                'balance' => $user->balance($currency)->get()
            ]
        ]);
    }

    return APIResponse::success([
        'user' => $user->makeVisible($user->hidden)->toArray(),
        'currencies' => $currencies
    ]);
});

Route::prefix('wallet')->group(function() {
    Route::post('invoices', function(Request $request) {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission())) return APIResponse::reject(1);

        $page = intval($request->page);
        $pageSize = 20;

        $invoices = [];

        foreach (\App\Invoice::latest()->skip(($page - 1) * $pageSize)->take($pageSize)->get() as $invoice) {
            $invoices[] = [
                'data' => $invoice->toArray(),
                'user' => User::where('_id', $invoice->user)->first()->toArray()
            ];
        }

        return [
            'maxPages' => ceil(\App\Invoice::count() / $pageSize),
            'invoices' => $invoices
        ];
    });
    Route::post('withdraws', function(Request $request) {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission())) return APIResponse::reject(1);

        $page = intval($request->page);
        $pageSize = 20;

        $withdraws = [];

        foreach (\App\Withdraw::latest()->skip(($page - 1) * $pageSize)->take($pageSize)->get() as $withdraw) {
            $withdraws[] = [
                'data' => $withdraw->toArray(),
                'user' => User::where('_id', $withdraw->user)->first()->toArray()
            ];
        }

        return [
            'maxPages' => ceil(\App\Withdraw::count() / $pageSize),
            'withdraws' => $withdraws
        ];
    });
    Route::prefix('invoice')->group(function() {
        Route::post('accept', function(Request $request) {
            $invoice = \App\Invoice::where('_id', $request->id)->first();
            if($invoice->status !== 0) return APIResponse::reject(1, 'Invalid deposit status, must be 0');

            $user = User::where('_id', $invoice->user)->first();
            $currency = Currency::find($invoice->currency);
            $user->balance($currency)->add($invoice->sum, Transaction::builder()->message('Deposit manual confirmation')->get());

            $user->update([
                'depositLimitValue' => (auth('sanctum')->user()->depositLimitValue ?? 0) + $invoice->sum
            ]);

            if(!$user->bonus_50_deposit) {
                $user->update(['bonus_50_deposit' => true]);
                $user->balance($currency)->createBonus($invoice->sum / 2, '50% Deposit Bonus', 20);
            }

            $user->depositPromotion($currency, $invoice->sum);

            $invoice->update(['status' => 1]);
            return [];
        });
        Route::post('cancel', function(Request $request) {
            $invoice = \App\Invoice::where('_id', $request->id)->first();

            if($invoice->status === 1)
                User::where('_id', $invoice->user)->first()->balance(Currency::find($invoice->currency))->subtract($invoice->sum, Transaction::builder()->message('Deposit manual cancellation')->get());

            $invoice->update(['status' => 2]);
            return [];
        });
    });
    Route::post('accept', function(Request $request) {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission(), 'edit')) return APIResponse::reject(1);

        $withdraw = \App\Withdraw::where('_id', $request->id)->first();
        if($withdraw == null || $withdraw->status != 0) return APIResponse::reject(1, 'Invalid state');

        \App\User::where('_id', $withdraw->user)->first()->notify(new \App\Notifications\WithdrawAccepted($withdraw));
        $withdraw->update([
            'status' => 1,
            'note' => $request->note
        ]);
        return APIResponse::success();
    });
    Route::post('decline', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission(), 'edit')) return APIResponse::reject(1);

        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 0) return APIResponse::reject(1, 'Invalid state');

        $withdraw->update([
            'decline_reason' => request('reason'),
            'status' => 2
        ]);
        \App\User::where('_id', $withdraw->user)->first()->notify(new \App\Notifications\WithdrawDeclined($withdraw));
        \App\User::where('_id', $withdraw->user)->first()->balance(Currency::find($withdraw->currency))->add($withdraw->sum_original, Transaction::builder()->message('Declined withdraw')->get());
        return APIResponse::success();
    });
    Route::post('ignore', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission(), 'edit')) return APIResponse::reject(1);

        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 0) return APIResponse::reject(1, 'Invalid state');
        $withdraw->update([
            'status' => 3
        ]);
        return APIResponse::success();
    });
    Route::post('unignore', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission(), 'edit')) return APIResponse::reject(1);

        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 3) return APIResponse::reject(1, 'Invalid state');
        $withdraw->update([
            'status' => 0
        ]);
        return APIResponse::success();
    });
    Route::middleware('superadmin')->get('autoSetup', function() {
        foreach (Currency::all() as $currency) $currency->setupWallet();
        return APIResponse::success();
    });
    Route::post('/transfer', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission())) return APIResponse::reject(1);

        try {
            $currency = Currency::find(request('currency'));
            $currency->send($currency->option('transfer_address'), request('address'), floatval(request('amount')));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::critical($e);
            return APIResponse::reject(1);
        }
        return APIResponse::success();
    });
});

Route::prefix('notifications')->group(function() {
    /*
    Route::post('/browser', function() {
        \Illuminate\Support\Facades\Notification::send(\App\User::where('notification_bonus', true)->get(),
            new \App\Notifications\BrowserOnlyNotification(request('title'), request('message')));
        return APIResponse::success();
    });
    */
    Route::post('/standalone', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\NotificationPermission(), 'create')) return APIResponse::reject(1);

        \Illuminate\Support\Facades\Notification::send(\App\User::get(),
            new \App\Notifications\CustomNotification(request('title'), request('message')));
        return APIResponse::success();
    });
    Route::post('/global', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\NotificationPermission(), 'create')) return APIResponse::reject(1);

        \App\GlobalNotification::create([
            'icon' => request('icon'),
            'text' => request('text')
        ]);
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => true, 'text' => request('text'), 'icon' => request('icon')]);
        return APIResponse::success();
    });
    Route::post('/global_remove', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\NotificationPermission(), 'delete')) return APIResponse::reject(1);

        $n = \App\GlobalNotification::where('_id', request('id'));
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => false, 'text' => $n->first()->text, 'icon' => $n->first()->icon]);
        $n->delete();
        return APIResponse::success();
    });
});

Route::post('/ban', function() {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), 'delete')) return APIResponse::reject(1);

    $user = \App\User::where('_id', request('id'))->first();
    (new \App\ActivityLog\BanUnbanLog())->insert(['type' => $user->ban ? 'unban' : 'ban', 'id' => $user->_id]);
    $user->update([
        'ban' => $user->ban ? false : true
    ]);
    return APIResponse::success();
});

Route::middleware('superadmin')->post('/toggle_module', function() {
    $game = Game::find(request('api_id'));
    $module = Module::find(request('module_id'));
    \App\Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->toggleModule($module)->save();
    return APIResponse::success();
});

Route::middleware('superadmin')->post('/option_value', function() {
    $game = Game::find(request('api_id'));
    $module = Module::find(request('module_id'));
    \App\Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->set($module, request('option_id'), request('value') ?? '')->save();
    return APIResponse::success();
});

Route::post('/toggle', function() {
    if(!auth('sanctum')->user()->checkPermission(new RootPermission())) return;

    if(\App\DisabledGame::where('name', request('name'))->first() == null) {
        \App\DisabledGame::create(['name' => request('name')]);
        (new \App\ActivityLog\DisableGameActivity())->insert(['state' => true, 'api_id' => request('name')]);

        Cache::put('disabledGame:'.\request('name'), true);
    } else {
        \App\DisabledGame::where('name', request('name'))->delete();
        (new \App\ActivityLog\DisableGameActivity())->insert(['state' => false, 'api_id' => request('name')]);

        Cache::put('disabledGame:'.\request('name'), false);
    }
    return APIResponse::success();
});

Route::post('/balance', function() {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), 'edit')) return APIResponse::reject(1);

    \App\User::where('_id', request('id'))->update([
        request('currency') => new Decimal128(strval(request('balance')))
    ]);

    (new \App\ActivityLog\BalanceChangeActivity())->insert(['currency' => request('currency'), 'balance' => request('balance'), 'id' => request('id')]);
    return APIResponse::success();
});

Route::post('/currencyOption', function() {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission(), 'edit')) return APIResponse::reject(1);

    Currency::find(request('currency'))->option(request('option'), request('value'));
    return APIResponse::success();
});

Route::post('/currencySettings', function() {
    $foundEmpty = false;
    $foundCount = 0;

    $options = [];

    foreach (Currency::getAllSupportedCoins() as $currency) {
        if($currency->option('withdraw_address') === '' || $currency->option('transfer_address') === '' || $currency->option('withdraw_address') === '1' || $currency->option('transfer_address') === '1') {
            $foundEmpty = true;
            $foundCount++;
        }

        $currencyOptions = [];

        foreach ($currency->getOptions() as $option) array_push($currencyOptions, [
            'id' => $option->id(),
            'readOnly' => $option->readOnly(),
            'value' => $currency->option($option->id()),
            'name' => $option->name()
        ]);

        $options = array_merge($options, [
            $currency->id() => $currencyOptions
        ]);
    }

    return APIResponse::success([
        'foundEmpty' => $foundEmpty,
        'foundCount' => $foundCount,
        'options' => $options,
        'coins' => Currency::toCurrencyArray(Currency::getAllSupportedCoins())
    ]);
});

Route::post('/toggleCurrency', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission(), 'edit')) return APIResponse::reject(1);

    $currenciesJson = json_decode(Settings::get('currencies', '["native_btc"]', true));
    $currencies = [];

    foreach ($currenciesJson as $id) {
        $currency = Currency::find($id);
        if($currency->walletId() == $request->walletId) continue;
        array_push($currencies, $currency->id());
    }

    if($request->type !== 'disabled') array_push($currencies, $request->type.'_'.$request->walletId);

    Settings::set('currencies', json_encode($currencies));
    return APIResponse::success();
});

Route::post('/notifications/data', function() {
    return APIResponse::success([
        'subscribers' => \App\User::where('notification_bonus', true)->count(),
        'global' => \App\GlobalNotification::get()->toArray()
    ]);
});

Route::post('/currencyBalance', function() {
    $balance = [];

    foreach (Currency::all() as $currency) {
        $cold = $currency->coldWalletBalance();
        $hot = $currency->hotWalletBalance();

        $balance = array_merge($balance, [
            $currency->id() => [
                'status' => $currency->isRunning(),
                'deposit' => $cold,
                'withdraw' => $hot
            ]
        ]);
    }
    return APIResponse::success($balance);
});

Route::middleware('superadmin')->post('/activity', function() {
    $activity = [];
    foreach(AdminActivity::latest()->get()->reverse() as $log) {
        if (ActivityLogEntry::find($log->type) == null) continue;
        $user = \App\User::where('_id', $log->user)->first();
        if(!$user) continue;

        $activity[] = [
            'user' => $user->toArray(),
            'entry' => $log->toArray(),
            'time' => Carbon::parse($log->time)->diffForHumans(),
            'html' => ActivityLogEntry::find($log->type)->display($log)
        ];
    }

    return APIResponse::success($activity);
});

Route::middleware('superadmin')->prefix('settings')->group(function() {
    Route::post('get', function() {
        return APIResponse::success([
            'mutable' => \App\Settings::where('internal', '!=', true)->where('hidden', '!=', true)->get()->toArray(),
            'immutable' => \App\Settings::where('internal', true)->where('hidden', '!=', true)->get()->toArray()
        ]);
    });
    Route::post('create', function() {
        \App\Settings::create(['name' => request('key'), 'description' => request('description'), 'value' => null]);
        return APIResponse::success();
    });
    Route::post('edit', function() {
        \App\Settings::where('name', request('key'))->first()->update([
            'value' => request('value') === 'null' ? null : request('value')
        ]);
        return APIResponse::success();
    });
    Route::post('remove', function() {
        \App\Settings::where('name', request('key'))->delete();
        return APIResponse::success();
    });
});

Route::middleware('superadmin')->prefix('chat_bot')->group(function() {
    Route::post('status', function() {
        return APIResponse::success([
            'status' => !(Settings::get('[Chat Bot] Stop', 'true', true) === 'true')
        ]);
    });
    Route::post('settings', function() {
        return APIResponse::success([
            [
                'name' => 'Message interval (seconds)',
                'value' => Settings::get('Message interval (seconds)', 15, true)
            ],
            [
                'name' => 'Message interval random delay (seconds)',
                'description' => 'Random delay to make an illusion that chat messages are not botted',
                'value' => Settings::get('Message interval randomness (seconds)', 15, true)
            ],
            [
                'name' => 'Chat bot channel',
                'description' => 'Botted chat channels',
                'type' => 'textarea',
                'value' => Settings::get('Chat bot channel', "casino_en
casino_pt-br
sport_en", true)
            ],
            [
                'name' => 'Chat bot messages',
                'description' => 'One of these messages will be randomly picked.',
                'type' => 'textarea',
                'value' => Settings::get('Chat bot messages', 'Hello
hi
WOW', true)
            ]
        ]);
    });
    Route::post('start', function() {
        Settings::toggle('[Chat Bot] Stop', 'false', 'true', 'true');
        dispatch(new \App\Jobs\Bot\Chat\ChatBotScheduler());
        return APIResponse::success();
    });
});

Route::post('bannerEdit', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\BannerPermission())) return APIResponse::reject(1);
    $key = null;
    switch ($request->editKey) {
        case 'state': $key = '[Banner] Enabled'; break;
        case 'title': $key = '[Banner] Title'; break;
        case 'image': $key = '[Banner] Image URL'; break;
        case 'content': $key = '[Banner] Content'; break;
    }
    Settings::set($key, $request->editValue);
    return APIResponse::success();
});

Route::post('forceVip', function (Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), 'edit')) return APIResponse::reject(1);
    User::where('_id', $request->id)->update([
        'forced_vip' => $request->level === -1 ? null : $request->level
    ]);
    return APIResponse::success();
});

Route::post('bannerSettings', function() {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\BannerPermission())) return APIResponse::reject(1);
    return APIResponse::success([
        'enabled' => Settings::get('[Banner] Enabled', 'false', true) === 'true',
        'title' => Settings::get('[Banner] Title', 'Banner Title', true),
        'image' => Settings::get('[Banner] Image URL', 'https://phoenix-gambling.com/images/phoenixCasino/phoenix_default.png', true),
        'content' => Settings::get('[Banner] Content', "<div>This text will show for everyone after page is loaded.</div>
<div><a href=\"https://example.com\">You can insert links</a> and other HTML elements.</div>", true)
    ]);
});

Route::post('editVIP', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\VIPControlPermission())) return APIResponse::reject(1);
    (new \App\VIP\VIP())->level($request->level)->set($request->key, $request->value ?? '');
    return APIResponse::success();
});

Route::middleware('superadmin')->prefix('bot')->group(function() {
    Route::post('status', function() {
        return APIResponse::success([
            'status' => !(Settings::get('[Bet Bot] Stop', 'true', true) === 'true')
        ]);
    });
    Route::post('settings', function() {
        return APIResponse::success([
            [
                'name' => 'create_new_bot_every_ms',
                'value' => Settings::get('create_new_bot_every_ms', 20000, true)
            ],
            [
                'name' => 'hidden_bets_probability',
                'value' => Settings::get('hidden_bets_probability', 20, true)
            ],
            [
                'name' => 'hidden_profile_probability',
                'value' => Settings::get('hidden_profile_probability', 20, true)
            ],
            [
                'name' => 'min_amount_of_games_from_one_bot',
                'value' => Settings::get('min_amount_of_games_from_one_bot', 20, true)
            ],
            [
                'name' => 'max_amount_of_games_from_one_bot',
                'value' => Settings::get('max_amount_of_games_from_one_bot', 50, true)
            ],
            [
                'name' => 'min_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('min_delay_between_games_from_one_bot_ms', 1000, true)
            ],
            [
                'name' => 'max_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('max_delay_between_games_from_one_bot_ms', 5000, true)
            ]
        ]);
    });
    Route::post('start', function() {
        Settings::toggle('[Bet Bot] Stop', 'false', 'true', 'true');
        dispatch(new \App\Jobs\Bot\BotScheduler());
        return APIResponse::success();
    });
});

Route::middleware('superadmin')->post('modules', function(Request $request) {
    $demo = $request->boolean('demo');
    $game = Game::find($request->game);

    $supportedModules = [];

    foreach (Module::modules() as $module) {
        $instance = new $module($game, null, null, null);

        $settings = [];
        foreach ($instance->settings() as $setting) {
            array_push($settings, [
                'id' => $setting->id(),
                'name' => $setting->name(),
                'description' => $setting->description(),
                'defaultValue' => $setting->defaultValue(),
                'type' => $setting->type(),
                'value' => \App\Modules::get($game, $demo)->get($instance, $setting->id())
            ]);
        }

        if($instance->supports()) array_push($supportedModules, [
            'id' => $instance->id(),
            'name' => $instance->name(),
            'description' => $instance->description(),
            'supports' => $instance->supports(),

            'isEnabled' => \App\Modules::get($game, $demo)->isEnabled($instance),
            'settings' => $settings
        ]);
    }

    return APIResponse::success($supportedModules);
});

Route::prefix('stats')->group(function() {
    Route::post('games', function() {
        return APIResponse::success([
            'games' => view('admin.games')->toHtml()
        ]);
    });
    Route::post('analytics', function() {
        return APIResponse::success([
            'analytics' => view('admin.analytics')->toHtml()
        ]);
    });
    Route::post('deposits', function() {
        return APIResponse::success([
            'dashboard' => view('admin.dashboard')->toHtml()
        ]);
    });
});

Route::prefix('promocode')->group(function() {
    Route::post('get', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\PromocodePermission())) return APIResponse::reject(1);
        return APIResponse::success(\App\Promocode::get()->toArray());
    });
    Route::post('remove', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\PromocodePermission(), 'delete')) return APIResponse::reject(1);
        \App\Promocode::where('_id', request()->get('id'))->delete();
        return APIResponse::success();
    });
    Route::post('remove_inactive', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\PromocodePermission(), 'delete')) return APIResponse::reject(1);
        foreach(\App\Promocode::get() as $promocode) {
            if(($promocode->expires->timestamp != Carbon::minValue()->timestamp && $promocode->expires->isPast())
                || ($promocode->usages != -1 && $promocode->times_used >= $promocode->usages)) $promocode->delete();
        }
        return APIResponse::success();
    });
    Route::post('create', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\PromocodePermission(), 'create')) return APIResponse::reject(1);
        request()->validate([
            'code' => 'required',
            'usages' => 'required',
            'expires' => 'required',
            'sum' => 'required',
            'currency' => 'required'
        ]);

        \App\Promocode::create([
            'code' => request('code') === '%random%' ? \App\Promocode::generate() : request('code'),
            'currency' => request('currency'),
            'used' => [],
            'sum' => floatval(request('sum')),
            'usages' => request('usages') === '%infinite%' ? -1 : intval(request('usages')),
            'times_used' => 0,
            'expires' => request('expires') === '%unlimited%' ? Carbon::minValue() : Carbon::createFromFormat('d-m-Y H:i', request()->get('expires'))
        ]);
        return APIResponse::success();
    });
});

Route::post('changePassword', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), "edit")) return APIResponse::reject(1);
    User::where('_id', $request->id)->first()->update([ "password" => Hash::make($request->password) ]);
    return APIResponse::success();
});

Route::post('createUser', function(Request $request) {
    if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), "create")) return APIResponse::reject(1);

    $request->validate([
        'email' => ['required', 'unique:users', 'email'],
        'name' => ['required', 'unique:users', 'string', 'max:64', 'regex:/^[a-zA-Z0-9]{5,64}$/u'],
        'password' => ['required', 'string', 'min:5']
    ]);

    $roles = [];

    foreach ($request->roles as $roleId) {
        $roles[] = [
            "id" => $roleId
        ];
    }

    User::create([
        'name' => $request->name,
        'password' => Hash::make($request->password),
        'avatar' => $avatar ?? '/avatar/' . uniqid(),
        'email' => $request->email,
        'client_seed' => ProvablyFair::generateServerSeed(),
        'roles' => $roles,
        'name_history' => [['time' => Carbon::now(), 'name' => $request->name]],
        'register_ip' => 'Registered via dashboard',
        'login_ip' => 'Registered via dashboard',
        'login_date' => Carbon::now(),
        'register_multiaccount_hash' => base64_encode(random_bytes(18)),
        'login_multiaccount_hash' => base64_encode(random_bytes(18))
    ]);
    return APIResponse::success();
});

Route::prefix('roles')->group(function() {
    Route::post('all', function() {
        if(!auth('sanctum')->user()->checkPermission(new \App\Permission\ControlUsersPermission(), "create")) return APIResponse::reject(1);
        return APIResponse::success(\App\Role::toRolesAndPermissionsArray());
    });
});

Route::middleware('superadmin')->prefix('roles')->group(function() {
    Route::post('new', function(Request $request) {
        \App\Role::create([
            'id' => $request->id,
            'name' => $request->name,
            'permissions' => []
        ]);
        Cache::forget('allRoles');
        return APIResponse::success();
    });
    Route::post('remove', function(Request $request) {
        \App\Role::where('id', $request->id)->delete();
        Cache::forget('allRoles');
        return APIResponse::success();
    });
    Route::post('update', function (Request $request) {
        $role = \App\Role::where('id', $request->roleId);
        $permissions = $role->first()->permissions;

        $permission = \App\Permission\Permissions::findById($request->permissionId);
        $rolePermission = new \App\Permission\RolePermission();

        foreach ($permissions as $dbPermission) {
            if($dbPermission['id'] === $permission->id()) {
                $rolePermission->from($dbPermission['permissions']);

                $permissions = array_filter($permissions, function($a) use ($permission) {
                    return $a['id'] !== $permission->id();
                });
            }
        }

        switch ($request->type) {
            case 'active': $rolePermission->active($request->state); break;
            case 'edit': $rolePermission->edit($request->state); break;
            case 'delete': $rolePermission->delete($request->state); break;
            case 'create': $rolePermission->create($request->state); break;
        }

        $permissions[] = $permission->toArray($rolePermission);

        $role->update(['permissions' => json_encode(array_values($permissions))]);
        Cache::forget('allRoles');
        return APIResponse::success();
    });
    Route::post('toggleRole', function(Request $request) {
        $user = User::where('_id', $request->userId)->first();
        $role = \App\Role::where('id', $request->roleId)->first();

        if($user->hasRole($role)) $user->deleteRole($role);
        else $user->addRole($role);
        return APIResponse::success();
    });
});
