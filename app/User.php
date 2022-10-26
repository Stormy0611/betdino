<?php

namespace App;

use App\Currency\Currency;
use App\Currency\Local\BRL;
use App\Events\BalanceModification;
use App\Events\BonusBalanceTransferred;
use App\Notifications\DatabaseNotification;
use App\Permission\Permission;
use App\Permission\Permissions;
use App\Permission\RolePermission;
use App\Token\NewAccessToken;
use App\VIP\VIP;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MongoDB\BSON\Decimal128;
use NotificationChannels\WebPush\HasPushSubscriptions;
use RobThree\Auth\TwoFactorAuth;
use Laravel\Sanctum\HasApiTokens;

class User extends \Jenssegers\Mongodb\Auth\User {

    use Notifiable, HasPushSubscriptions, HasApiTokens;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'roles', 'client_seed',
        'bonus_claim', 'ignore', 'private_profile', 'private_bets', 'name_history', 'latest_activity',
        'discord_bonus', 'notification_bonus', 'ban', 'mute', 'weekly_bonus', 'weekly_bonus_obtained',
        'tfa', 'tfa_enabled', 'tfa_persistent_key', 'tfa_onetime_key', 'email_notified', 'dismissed_global_notifications',
        'register_ip', 'login_ip', 'login_date', 'register_multiaccount_hash', 'login_multiaccount_hash',
        'referral', 'referral_wager_obtained', 'promocode_limit_reset', 'promocode_limit',
        'withdraw_limit_reset', 'withdraw_limit', 'forced_vip',
        'bot', 'favoriteGames', 'rakeback_claim', 'invite_code', 'break', 'depositLimit', 'depositLimitBreak', 'depositLimitValue',

        'rakeback_btc', 'rakeback_ltc', 'rakeback_doge', 'rakeback_bch', 'rakeback_trx', 'rakeback_eth', 'rakeback_sol', 'rakeback_sol_bones', 'rakeback_brl',

        'vk', 'fb', 'google', 'discord', 'steam',

        'btc', 'ltc', 'eth', 'doge', 'bch', 'trx', 'algo', 'btg', 'celo', 'dash', 'eos', 'xrp', 'xlm', 'xtz', 'wbtc', 'zec', 'rub', 'brl',
        'demo_btc', 'demo_ltc', 'demo_eth', 'demo_doge', 'demo_bch', 'demo_trx', 'demo_algo', 'demo_btg', 'demo_celo', 'demo_dash',
        'demo_eos', 'demo_xrp', 'demo_xlm', 'demo_xtz', 'demo_wbtc', 'demo_zec', 'demo_rub', 'demo_brl',

        'referral_bonus_5', 'referral_bonus_20', 'referral_bonus_100', 'referral_bonus_500', 'referral_bonus_1000',
        'referral_bonus_5000', 'referral_bonus_50000',
        'bonus_50_deposit',

        'wallet_native_btc', 'wallet_native_ltc', 'wallet_native_eth', 'wallet_native_doge', 'wallet_native_bch', 'wallet_native_trx',
        'wallet_bg_btc', 'wallet_bg_bch', 'wallet_bg_trx', 'wallet_bg_eos', 'wallet_bg_eth', 'wallet_bg_ltc',
        'wallet_bg_algo', 'wallet_bg_btg', 'wallet_bg_celo', 'wallet_bg_dash', 'wallet_bg_eos', 'wallet_bg_xrp', 'wallet_bg_xlm',
        'wallet_bg_xtz', 'wallet_bg_wbtc', 'wallet_bg_zec',
        'wallet_trx_private_key',

        'vip_0_bonus_claimed', 'vip_1_bonus_claimed', 'vip_2_bonus_claimed', 'vip_3_bonus_claimed', 'vip_4_bonus_claimed',
        'vip_5_bonus_claimed', 'vip_6_bonus_claimed', 'vip_7_bonus_claimed', 'vip_8_bonus_claimed',
        'vip_9_bonus_claimed', 'vip_10_bonus_claimed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    public $hidden = [
        'password', 'remember_token', 'email', 'ignore', 'ban',
        'discord_bonus', 'notification_bonus', 'latest_activity',
        'tfa', 'tfa_enabled', 'tfa_persistent_key', 'tfa_onetime_key', 'email_notified', 'dismissed_global_notifications',
        'register_ip', 'login_ip', 'login_date', 'register_multiaccount_hash', 'login_multiaccount_hash',
        'referral', 'referral_wager_obtained', 'promocode_limit_reset', 'promocode_limit',
        'bot', 'invite_code',

        'fb', 'google', 'discord', 'steam',

        'btc', 'ltc', 'eth', 'doge', 'bch', 'trx', 'algo', 'btg', 'celo', 'dash', 'eos', 'xrp', 'xlm', 'xtz', 'wbtc', 'zec', 'brl',
        'demo_btc', 'demo_ltc', 'demo_eth', 'demo_doge', 'demo_bch', 'demo_trx', 'demo_algo', 'demo_btg', 'demo_celo', 'demo_dash',
        'demo_eos', 'demo_xrp', 'demo_xlm', 'demo_xtz', 'demo_wbtc', 'demo_zec', 'demo_brl',

        'referral_bonus_5', 'referral_bonus_20', 'referral_bonus_100', 'referral_bonus_500', 'referral_bonus_1000',
        'referral_bonus_5000', 'referral_bonus_50000',

        'wallet_native_btc', 'wallet_native_ltc', 'wallet_native_eth', 'wallet_native_doge', 'wallet_native_bch', 'wallet_native_trx',
        'wallet_bg_btc', 'wallet_bg_bch', 'wallet_bg_trx', 'wallet_bg_eos', 'wallet_bg_eth', 'wallet_bg_ltc',
        'wallet_bg_algo', 'wallet_bg_btg', 'wallet_bg_celo', 'wallet_bg_dash', 'wallet_bg_eos', 'wallet_bg_xrp', 'wallet_bg_xlm',
        'wallet_bg_xtz', 'wallet_bg_wbtc', 'wallet_bg_zec',
        'wallet_trx_private_key'
    ];

    /**
     * Some of the attributes should be hidden even for account owners.
     * @var array
     */
    public $alwaysHidden = [
        'register_multiaccount_hash', 'login_multiaccount_hash', 'register_ip', 'login_ip', 'wallet_trx_private_key', 'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bonus_claim' => 'datetime',
        'mute' => 'datetime',
        'latest_activity' => 'datetime',
        'promocode_limit_reset' => 'datetime',
        'break' => 'datetime',
        'depositLimitBreak' => 'datetime',
        'withdraw_limit_reset' => 'datetime',
        'tfa_persistent_key' => 'datetime',
        'tfa_onetime_key' => 'datetime',
        'login_date' => 'datetime',
        'roles' => 'json',
        'ignore' => 'json',
        'name_history' => 'json',
        'referral_wager_obtained' => 'json',
        'favoriteGames' => 'json',
        'rakeback_claim' => 'datetime',
    ];

    /**
     * @param Permission $permission
     * @param string $checkType edit / delete / create / active
     * @return bool
     */
    public function checkPermission(Permission $permission, string $checkType = 'active'): bool {
        if($checkType != 'active' && !$this->checkPermission($permission)) return false; // If no main permission - refuse to accept sub-permissions

        foreach($this->roles as $role) {
            $dbRole = Role::id($role['id']);
            if($dbRole == null) continue;

            if($dbRole->id === '*') return true;

            foreach($dbRole->permissions as $rolePermission) {
                if($rolePermission['id'] !== $permission->id()) continue;
                if($rolePermission['permissions'][$checkType] == true) return true;
            }
        }

        return false;
    }

    public function hasRole(Role $role): bool {
        return count(array_filter($this->roles, function($a) use($role) {
            return $role->id === $a['id'];
        })) > 0;
    }

    public function depositPromotion(Currency $currency, float $sum): void {
        if($this->referral) {
            $referrer = User::where('_id', $this->referral)->first();

            $commissionPercent = (new VIP())->level($referrer->vipLevel())->referralDepositFee;

            if($commissionPercent > 0) {
                $brl = new BRL();
                $commission = ($commissionPercent * $sum) / 100;
                $referrer->balance($brl)->add($commission, Transaction::builder()->message('Affiliate deposit fee (' . $commissionPercent . '% from ' . $sum . ' .' . $currency->name() . ')')->get());

                AffiliateLog::create([
                    'user' => $referrer->_id,
                    'type' => 'deposit_fee',
                    'amount' => $commission,
                    'currency' => $brl->name(),
                    'referrer' => $this->_id
                ]);
                AffiliateLeaderboard::insert($referrer, $commission);
            }

            $referrals = $referrer->referral_wager_obtained ?? [];
            if(!in_array($this->_id, $referrals)) {
                $amount = (new VIP())->level($referrer->vipLevel())->inviteBonus;
                $referrals[] = $this->_id;
                $referrer->update(['referral_wager_obtained' => $referrals]);
                $referrer->balance(Currency::find('local_brl'))->add($amount, \App\Transaction::builder()->message('Invite bonus')->get());

                $brl = new BRL();

                AffiliateLog::create([
                    'user' => $referrer->_id,
                    'type' => 'activity_bonus',
                    'amount' => $amount,
                    'currency' => $brl->name(),
                    'referrer' => $this->_id
                ]);
                AffiliateLeaderboard::insert($referrer, $amount);

                $invitePromotion = function($invited, $brlAmount) use($brl, $referrer) {
                    $referrer->balance($brl)->add($brlAmount, \App\Transaction::builder()->message('*Millions of Distributor Benefits* - ' . $invited . ' active users')->get());

                    AffiliateLog::create([
                        'user' => $referrer->_id,
                        'type' => 'millions_of_distributor_benefits',
                        'amount' => $brlAmount,
                        'currency' => $brl->name(),
                        'referrer' => $this->_id
                    ]);
                    AffiliateLeaderboard::insert($referrer, $brlAmount);
                    $referrer->update(['referral_bonus_' . $invited => true]);
                };

                $brl = (new BRL());

                if($referrer->referral_bonus_5 === null && count($referrals) >= 5) {
                    $invitePromotion(5, 6);
                } else if($referrer->referral_bonus_20 === null && count($referrals) >= 20) {
                    $invitePromotion(20, 26);
                } else if($referrer->referral_bonus_100 === null && count($referrals) >= 100) {
                    $invitePromotion(100, 80);
                } else if($referrer->referral_bonus_500 === null && count($referrals) >= 500) {
                    $invitePromotion(500, 188);
                } else if($referrer->referral_bonus_1000 === null && count($referrals) >= 1000) {
                    $invitePromotion(1000, 1288);
                } else if($referrer->referral_bonus_5000 === null && count($referrals) >= 5000) {
                    $invitePromotion(5000, 13888);
                } else if($referrer->referral_bonus_50000 === null && count($referrals) >= 50000) {
                    $invitePromotion(50000, 188888);
                }
            }
        }
    }

    public function bettingCommission(Currency $currency, float $betAmount) {
        if(!$this->referral) return;
        $referrer = User::where('_id', $this->referral)->first();

        $bankroll = 15;
        $percent = 1;

        $commission = $betAmount * ($percent / 100) * ($bankroll / 100);

        BettingCommission::create([
            'user' => $referrer->_id,
            'referral' => $this->_id,
            'amount' => $commission,
            'currency' => $currency,
            'brl_amount' => $currency->id() === 'local_brl' ? $commission : (new BRL())->convertFiatToToken($currency->convertTokenToFiat($commission))
        ]);
        AffiliateLeaderboard::insert($referrer, $commission);

        $referrer->balance($currency)->add($commission, Transaction::builder()->message('Wager commission (Affiliates) (from ' . $this->_id . ')')->get());
    }

    public function vipLevel(): int {
        $level = 0;
        $wagered = $this->wagered();
        $deposited = $this->deposited();

        for($i = 0; $i <= 10; $i++) {
            $data = (new VIP())->level($i);
            if($wagered >= $data->wagerRequirement && $deposited >= $data->depositRequirement)
                $level = $i;
        }

        return $this->forced_vip !== null ? $this->forced_vip : $level;
    }

    public function inviteCode(): string {
        $code = md5($this->_id);
        if($this->invite_code === null) $this->update(['invite_code' => $code]);
        return $code;
    }

    public function addRole(Role $role) {
        $roles = $this->roles;

        $roles[] = [
            'id' => $role->id
        ];

        $this->update([
            'roles' => $roles
        ]);
    }

    public function deleteRole(Role $role) {
        $roles = $this->roles;
        if(is_string($roles)) $roles = json_decode($roles);

        $this->update([
           'roles' => array_filter($roles, function($a) use ($role) {
               return $a['id'] !== $role->id;
           })
        ]);
    }

    public function rakeback(Currency $currency, float $betValue) {
        $rakebackPercent = floatval(Settings::get('Cashback percent', '8.8'));

        if($rakebackPercent > 0) {
            $rakebackAvailable = isset($this->toArray()['rakeback_' . $currency->walletId()]) ? floatval($this->toArray()['rakeback_' . $currency->walletId()]) : 0;

            $this->update([
                'rakeback_' . $currency->walletId() => $rakebackAvailable + ($rakebackPercent * $betValue) / 100
            ]);
        }
    }

    public static function getIp() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) return $ip;
                }
            }
        }
        return request()->ip();
    }

    public function isDismissed(GlobalNotification $globalNotification) {
        return in_array($globalNotification->_id, $this->dismissed_global_notifications ?? []);
    }

    public function dismiss(GlobalNotification $globalNotification) {
        $array = $this->$globalNotification->dismissed_global_notifications ?? [];
        array_push($array, $globalNotification->_id);
        $this->update([
            'dismissed_global_notifications' => $array
        ]);
    }

    public function notifications() {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function balance(Currency $currency): UserBalance {
        return new UserBalance($this, $currency);
    }

    public function clientCurrency(): Currency {
        return Currency::find($_COOKIE['currency'] ?? Currency::all()[0]->id()) ?? Currency::all()[0];
    }

    public function depositWallet(Currency $currency) {
        $wallet = $this->makeVisible('wallet_'.$currency->id())->toArray()['wallet_'.$currency->id()] ?? null;
        if($wallet == null) {
            $wallet = $currency->newWalletAddress();
            if($wallet !== 'Error') $this->update([
                'wallet_'.$currency->id() => $wallet
            ]);
        }
        return $wallet;
    }

    public function wagered() {
        return (new BRL())->convertFiatToToken(DB::table('games')->where('user', $this->_id)
            ->where('demo', '!=', true)
            ->where('status', '!=', 'in-progress')
            ->where('status', '!=', 'cancelled')
            ->sum('bet_usd_converted'));
    }

    public function deposited() {
        return (new BRL())->convertFiatToToken(DB::table('invoices')
            ->where('status', 1)
            ->where('user', $this->_id)
            ->sum('usd_converted'));
    }

    public function games() {
        return DB::table('games')->where('user', $this->_id)->where('status', '!=', 'cancelled')->where('status', '!=', 'in-progress')->where('demo', '!=', true)->count();
    }

    public function getInvestmentProfit(Currency $currency, bool $sub, bool $stopAtZero = true) {
        $profit = 0;
        foreach (Investment::where('user', $this->_id)->where('status', 0)->where('currency', $currency->id())->get() as $investment)
            $profit += $investment->getProfit() - ($sub ? $investment->amount : 0);
        return $stopAtZero == false ? $profit : ($profit < 0 ? 0 : $profit);
    }

    public function tfa(): TwoFactorAuth {
        return new TwoFactorAuth('betdino.io/'.$this->name);
    }

    public function validate2FA(bool $persist): bool {
        $token = $persist ? ($this->tfa_persistent_key ?? null) : ($this->tfa_onetime_key ?? null);
        return ($this->tfa_enabled ?? false) === false || ($token != null && !$token->isPast());
    }

    public function reset2FAOneTimeToken() {
        $this->update(['tfa_onetime_key' => null]);
    }

    public function createToken(array $abilities = ['*']) {
        $token = $this->tokens()->create([
            'name' => $this->_id,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->id.'|'.$plainTextToken);
    }

    public function bonus(): ?BonusBalance {
        $balance = isset($_COOKIE['useBonus']) ?
            ($_COOKIE['useBonus'] === 'null' ? null : BonusBalance::where('_id', $_COOKIE['useBonus'])->first()) : null;
        if($balance !== null && $balance->user !== $this->_id
            && $this->clientCurrency()->walletId() === $balance->currency) return null;
        return $balance;
    }

}

class UserBalance {

    private User $user;
    private Currency $currency;
    private bool $quiet = false;
    private bool $demo = false;

    private float $minValue = 0.00000000;

    public function __construct(User $user, Currency $currency) {
        $this->user = $user;
        $this->currency = $currency;
    }

    public function quiet() {
        $this->quiet = true;
        return $this;
    }

    public function demo($set = true) {
        $this->demo = $set;
        return $this;
    }

    public function get(): float {
        $value = floatval(($this->user->{$this->getColumn()} ?? new Decimal128($this->minValue))->jsonSerialize()['$numberDecimal']);

        return floatval(number_format($value, str_starts_with($this->currency->walletId(), "local") ? 2 : 8, '.', ''));
    }

    public function createBonus(float $amount, string $description, $wagerRequirementMultiplier = 20) {
        BonusBalance::create([
            'user' => $this->user->_id,
            'currency' => $this->currency->walletId(),
            'originalValue' => $amount,
            'value' => $amount,
            'wagered' => 0,
            'neededToWager' => $amount * $wagerRequirementMultiplier,
            'description' => $description
        ]);
    }

    public function addBonus(float $amount, BonusBalance $balance, int $delay = 0) {
        $balance->update([
            'value' => $balance->value + $amount
        ]);

        if($this->quiet == false) event(new BalanceModification($this->user, $this->currency, 'add', false, $amount, $delay, $balance));
    }

    public function subtractBonus(float $amount, BonusBalance $balance, int $delay = 0) {
        $balance->update([
            'value' => $balance->value - $amount
        ]);

        if($this->quiet == false) event(new BalanceModification($this->user, $this->currency, 'subtract', false, $amount, $delay, $balance));
    }

    public function addBonusWager(float $wagered, BonusBalance $balance) {
        $balance->update([
            'wagered' => $balance->wagered + $wagered
        ]);

        if($balance->wagered >= $balance->neededToWager) {
            $this->add($balance->value, Transaction::builder()->message('Bonus (' . $balance->description . ') x' . number_format($balance->neededToWager / $balance->originalValue, 2, '.', ''))->get());
            event(new BonusBalanceTransferred($this->user, $balance));
            $balance->delete();
        }
    }

    public function bonusBalances(): array {
        return BonusBalance::orderBy('value', 'desc')->where('user', $this->user->_id)->get()->toArray();
    }

    private function getColumn() {
        return $this->demo ? 'demo_'.$this->currency->walletId() : $this->currency->walletId();
    }

    public function add(float $amount, array $transaction = null, int $delay = 0) {
        $this->user->update([
            $this->getColumn() => new Decimal128(strval($this->get() + $amount))
        ]);

        if($this->quiet == false) event(new BalanceModification($this->user, $this->currency, 'add', $this->demo, $amount, $delay));
        Transaction::create([
            'user' => $this->user->_id,
            'demo' => $this->demo,
            'currency' => $this->currency->id(),
            'new' => $this->get(),
            'old' => $this->get() - $amount,
            'amount' => $amount,
            'quiet' => $this->quiet,
            'data' => $transaction ?? []
        ]);
    }

    public function subtract(float $amount, array $transaction = null) {
        $value = $this->get() - $amount;
        if($value < 0) $value = 0;
        $this->user->update([
            $this->getColumn() => new Decimal128(strval($value))
        ]);

        if($this->quiet == false) event(new BalanceModification($this->user, $this->currency, 'subtract', $this->demo, $amount, 0));
        Transaction::create([
            'user' => $this->user->_id,
            'demo' => $this->demo,
            'currency' => $this->currency->id(),
            'new' => $this->get(),
            'old' => $this->get() + $amount,
            'amount' => -$amount,
            'quiet' => $this->quiet,
            'data' => $transaction ?? []
        ]);
    }

}
