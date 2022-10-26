@php
    $humanDiff = function(array $array) {
        foreach($array as $value) $array[array_search($value, $array)] = \Carbon\Carbon::parse($value)->toFormattedDateString();
        return $array;
    };
@endphp

<div class="row">
    @if(auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission()))
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pb-0" style="position: relative;">
                    <h5 class="card-title mb-0 header-title">Income</h5>

                    @php
                        $fix = function($n) {
                            return is_float($n) || is_int($n) ? floatval($n) : floatval($n->jsonSerialize()['$numberDecimal']);
                        };

                        $fill_data_currency = function($days, $currency_id) use($fix) {
                            $out = [];
                            for($i = 0; $i < $days; $i++)
                                array_push($out, $fix(\Illuminate\Support\Facades\DB::table('invoices')
                                    ->where('status', 1)
                                    ->where('created_at', '>=', \Carbon\Carbon::today()->subDays($i))
                                    ->where('created_at', '<', \Carbon\Carbon::today()->subDays($i - 1))
                                    ->where('currency', $currency_id)->where('status', 1)->sum('sum')));
                            return array_reverse($out);
                        };
                        $fill_labels = function($days) {
                            $out = [];
                            for($i = 0; $i < $days; $i++)
                                array_push($out, $i > 0 ? $i .' days ago' : 'Today');
                            return array_reverse($out);
                        };

                        $merge = [];
                        foreach (\App\Currency\Currency::all() as $currency) {
                            array_push($merge, [
                                'name' => $currency->name(),
                                'data' => $fill_data_currency(7, $currency->id())
                            ]);
                        }

                        $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                        $chart->setTitle('One week')->setType('area')->setHeight(680)->setXAxis($fill_labels(7))->setDataset($merge);
                    @endphp
                    <div id="{{ $chart->id }}" data-chart="m3" class="apex-charts mt-3"></div>
                    {{ $chart->script() }}
                </div>
            </div>
        </div>
    @endif
    @if(auth('sanctum')->user()->checkPermission(new \App\Permission\WithdrawsPermission()))
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body pt-2">
                    <a href="/admin/wallet/withdraws" class="btn btn-primary btn-sm mt-2 float-right">
                        View
                    </a>
                    <h6 class="header-title mb-4">Latest withdraws</h6>
                    @if(\App\Withdraw::where('status', 0)->count() == 0)
                        <i style="display: flex; margin-left: auto; margin-right: auto;" data-feather="clock"></i>
                        <div class="text-center mt-2">Nothing here</div>
                    @else
                        @foreach(\App\Withdraw::where('status', 0)->latest()->take(5)->get() as $withdraw)
                            @php $user = \App\User::where('_id', $withdraw->user)->first(); if(!$user) continue; @endphp
                            <div class="media border-top pt-3">
                                <img src="{{ $user->avatar }}" class="avatar rounded mr-3" alt="shreyu">
                                <div class="media-body">
                                    <h6 class="mt-1 mb-0 font-size-15">{{ $user->name }}</h6>
                                    <h6 class="text-muted font-weight-normal mt-1 mb-3">{{ number_format($withdraw->sum, str_starts_with($withdraw->currency, "local") ? 2 : 8, '.', ' ') }} {{ \App\Currency\Currency::find($withdraw->currency)->name() }}</h6>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>


<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="media p-3">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total Users</span>
                        <h2 class="mb-0">{{ \App\User::where('bot', '!=', true)->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="media p-3">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Active Users Today</span>
                        <h2 class="mb-0">{{ \App\User::where('bot', '!=', true)->where('latest_activity', '>=', \Carbon\Carbon::today())->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if(auth('sanctum')->user()->checkPermission(new \App\Permission\WalletPermission()))
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Number of deposits today</span>
                            <h2 class="mb-0">{{ \App\Invoice::where('status', 1)->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Number of withdraws today</span>
                            <h2 class="mb-0">{{ \App\Withdraw::where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="media p-3">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Registered Users Today - Total</span>
                        <h2 class="mb-0">{{ \App\User::where('bot', '!=', true)->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="media p-3">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Registered Users Today (Natural)</span>
                        <h2 class="mb-0">{{ \App\User::where('bot', '!=', true)->where('referral', '!=', null)->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="media p-3">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Registered Users Today (Referred)</span>
                        <h2 class="mb-0">{{ \App\User::where('bot', '!=', true)->where('referral', null)->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-6">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total referrals</span>
                                <h2 class="mb-0">{{ \Illuminate\Support\Facades\DB::table('users')->where('referral', '!=', null)->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total active referrals</span>
                                <h2 class="mb-0">
                                    @php
                                        $count = 0;
                                        foreach(\App\User::where('referral', '!=', null)->get() as $referral) {
                                            $user = \App\User::where('_id', $referral->referral)->first();
                                            if(in_array($referral->_id, $user->referral_wager_obtained ?? [])) $count++;
                                        }
                                    @endphp
                                    {{ $count }}
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body pb-0">
                <ul class="nav card-nav float-right">
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=today]').show(); window.dispatchEvent(new Event('resize'));">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=week]').show(); window.dispatchEvent(new Event('resize'));">7d</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=d15]').show(); window.dispatchEvent(new Event('resize'));">15d</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=m1]').show(); window.dispatchEvent(new Event('resize'));">1m</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=m3]').show(); window.dispatchEvent(new Event('resize'));">3m</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=m6]').show(); window.dispatchEvent(new Event('resize'));">6m</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="javascript:void(0)" onclick="$('[data-chart-u]').hide(); $('[data-chart-u=y]').show(); window.dispatchEvent(new Event('resize'));">1y</a>
                    </li>
                </ul>
                <h5 class="card-title mb-0 header-title">New Users</h5>

                @php
                    $fill_data = function() {
                        $out = [];

                        for($i = 0; $i <= 23; $i++) {
                            if (\Carbon\Carbon::now()->timestamp < \Carbon\Carbon::today()->addHours($i)->timestamp) continue;
                            array_push($out, \Illuminate\Support\Facades\DB::table('users')->where('created_at', '>=', \Carbon\Carbon::today()->addHours($i))->where('bot', '!=', true)
                                ->where('created_at', '<=', \Carbon\Carbon::today()->addHours($i+1))->count());
                        }
                        return $out;
                    };
                    $fill_labels = function() {
                        $out = [];
                        for($i = 0; $i <= 23; $i++) {
                            if (\Carbon\Carbon::now()->timestamp < \Carbon\Carbon::today()->addHours($i)->timestamp) continue;
                            array_push($out, $i.':00 - '.$i.':59');
                        }
                        return $out;
                    };

                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('Today')->setType('area')->setHeight(600)->setXAxis($fill_labels())->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data()
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="today" class="apex-charts mt-3"></div>
                {{ $chart->script() }}

                @php
                    $fill_data = function($days) {
                        $out = [];
                        for($i = 0; $i < $days; $i++)
                            array_push($out, \Illuminate\Support\Facades\DB::table('users')->where('created_at', '>=', \Carbon\Carbon::today()->subDays($i + 1))
                                    ->where('created_at', '<=', \Carbon\Carbon::today()->subDays($i))->count());
                        return array_reverse($out);
                    };
                    $fill_labels = function($days) {
                        $out = [];
                        for($i = 0; $i < $days; $i++)
                            array_push($out, $i > 0 ? $i .' days ago' : 'Today');
                        return array_reverse($out);
                    };

                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('7 days')->setType('area')->setHeight(600)->setXAxis($fill_labels(7))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(7)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="week" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}

                @php
                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('15 days')->setType('area')->setHeight(600)->setXAxis($fill_labels(15))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(15)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="d15" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}

                @php
                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('30 days')->setType('area')->setHeight(600)->setXAxis($fill_labels(30))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(30)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="m1" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}

                @php
                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('3 months')->setType('area')->setHeight(600)->setXAxis($fill_labels(90))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(90)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="m3" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}

                @php
                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('6 months')->setType('area')->setHeight(600)->setXAxis($fill_labels(180))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(180)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="m6" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}

                @php
                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('1 year')->setType('area')->setHeight(600)->setXAxis($fill_labels(365))->setDataset([[
                        'name' => 'Total',
                        'data' => $fill_data(365)
                    ]]);
                @endphp
                <div id="{{ $chart->id }}" data-chart-u="y" class="apex-charts mt-3" style="display: none"></div>
                {{ $chart->script() }}
            </div>
        </div>
    </div>
</div>
