<?php namespace App\Currency\Aggregator;

use App\Currency\Currency;
use App\Invoice;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HpayOneAggregator extends Aggregator {

    private string $merchantNumber = "256018655";
    private string $merchantKey = "LHLMkrgmTcZuRHWgDhYV";
    private string $gateway = "https://api.hpay.one";

    function invoice(Invoice $invoice): string {
        $data = [
            'mchid' => $this->merchantNumber,
            'timestamp' => time(),
            'amount' => $invoice->sum,
            'orderno' => intval(microtime(true) * 1000 * 1000),
            'notifyurl' => url('/api/paymentStatus'),
            'currency' => 'BRL'
        ];

        $sign = $this->sign($data, $this->merchantKey);
        $data['sign'] = $sign;

        $result = $this->curl($this->gateway . '/open/index/createorder', $data, true);

        if(isset($result['data']['pay_info'])) {
            $invoice->update([
                'internal_id' => $result['data']['tx_orderno']
            ]);

            return $result['data']['pay_info'];
        } else Log::warning('Error: ' . json_encode($result));

        return url("/payment_error");
    }

    function status(Request $request): string {
        $data = $request->all();
        unset($data['sign']);
        $sign = $this->sign($data, $this->merchantKey);

        if($sign === $request->sign) {
            if($data['trade_state'] === 'SUCCESS') {
                $invoice = Invoice::where('internal_id', $request->tx_orderno)->first();

                if($invoice != null && $invoice->status === 0) {
                    $invoice->update([
                        'status' => 1
                    ]);

                    $currency = Currency::find($invoice->currency);

                    $user = User::where('_id', $invoice->user)->first();
                    $user->balance($currency)->add($invoice->sum, Transaction::builder()->message('PIX Deposit')->get());

                    $user->update([
                        'depositLimitValue' => (auth('sanctum')->user()->depositLimitValue ?? 0) + $request->sum
                    ]);

                    if(!$user->bonus_50_deposit) {
                        $user->update(['bonus_50_deposit' => true]);
                        $user->balance($this)->createBonus($invoice->sum / 2, '50% Deposit Bonus', 20);
                    }

                    $user->depositPromotion($currency, $invoice->sum);
                }
            }
        }

        return "SUCCESS";
    }

    function validate(Request $request): bool {
        return $request->trade_state !== null;
    }

    function id(): string {
        return "hpay";
    }

    function name(): string {
        return "PIX";
    }

    function icon(): string {
        return "";
    }

    public function supports(): array {
        return [
            "local_brl"
        ];
    }

    private function curl($url, $data = null, $post = false, $text = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($post) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $json = curl_exec($ch);
        curl_close($ch);

        if($text)
            $result = $json;
        else
            $result = json_decode($json, true);

        return $result;
    }

    private function sign($object, $key) {
        $parameters = [];

        foreach ($object as $k => $v) {
            if(isset($v) && strlen($v) > 0){
                $parameters[$k] = $v;
            }
        }

        ksort($parameters);
        $string = $this->formatQuery($parameters);
        $string = $string.'&key='.$key;
        $string = md5($string);
        return strtoupper($string);
    }

    private function formatQuery($paraMap) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
            if ($k != 's')
                $buff .= $k .'='. $v.'&' ;

        $reqPar = "";
        if (strlen($buff) > 0)
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        return $reqPar;
    }

}
