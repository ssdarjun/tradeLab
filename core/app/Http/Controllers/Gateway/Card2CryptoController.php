<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Constants\Status;
use App\Models\Card2Crypto;

class Card2CryptoController extends Controller
{

    public function index()
    {
        $gateway = Card2Crypto::first();

        if ($gateway->status != 1) {
            return redirect()->back()->withNotify([['error', 'Crypto 2 Card Payment Gateway not active']]);
        }

        $pageTitle = 'Deposit Money (Card 2 Crypto)';
        $conversationRates = $this->conversationRate();
        return view($this->activeTemplate . 'user.payment.deposit-card2crypto', compact('pageTitle', 'conversationRates'));
    }

    public function conversationRate()
    {
        $gateway = Card2Crypto::first();

        if (!$gateway || !is_array($gateway->rate)) {
            return [];
        }

        $rates = $gateway->rate;
        return $rates;
    }

    public function redirectToPayment(Request $request)
    {
        $gateway = Card2Crypto::first();

        if ($gateway->status != 1) {
            return redirect()->back()->withNotify([['error', 'Crypto 2 Card Payment Gateway not active']]);
        }

        $request->validate([
            'email'          => 'required|email',
            'amount'   => 'required|numeric|gt:0|min:' . number_format($gateway->min_amount, 2, '.', '') . '|max:' . number_format($gateway->max_amount, 2, '.', ''),
            'currency'       => 'required|in:USD,EUR,CAD,INR',
            'provider'       => 'required|in:wert,werteur,switchere,stripe,sardine,revolut,guardarian,particle,transak,banxa,simplex,changenow,mercuryo,rampnetwork,moonpay,alchemypay,robinhood,coinbase,utorg,unlimit,bitnovo,simpleswap,finchpay,topper,swipelux,kado,itez,transfi,interac,upi',
        ]);

        $conversationRates = $this->conversationRate();
        $wallet = $gateway->wallet_address;
        $user = auth()->user();
        $card2crypto_verify_token = getTrx().getTrx();
        $trx = getTrx();

        $callback = route('card2crypto.handleCallback').'?trx='.$trx.'&card2crypto_verify_token=' . $card2crypto_verify_token;

        /*
        $charge = 0;
        $rate = 1 / $conversationRates[$request->currency];
        $payable = $request->amount + $charge;
        $final_amo = $payable * $rate;
        */
        
        $charge = $gateway->fixed_charge + ($request->amount * $gateway->percent_charge / 100);
        $rate = $conversationRates[$request->currency];
        $payable = $request->amount + $charge;
        $final_amo = $payable * $rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = 0;
        $data->method_currency = $request->currency;
        $data->amount =  $request->amount;
        $data->charge = $charge;
        $data->rate = $rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = $trx;
        $data->status = 0;
        $data->card2crypto_wallet = $wallet;
        $data->card2crypto_verify_token = $card2crypto_verify_token;
        $data->save();
        
        $response = Http::get("https://api.card2crypto.org/control/wallet.php", [
            'address' => $wallet,
            'callback' => $callback
        ]);

        $wallet = $response->json();

        if ($response->successful() && (isset($wallet['address_in']) && $wallet['address_in'])) {
            $encryptedWallet = $wallet['address_in'];
            $amount = $final_amo;
            $provider = $request->input('provider');
            $email = urlencode($request->input('email'));
            $currency = $request->input('currency');

            $paymentUrl = "https://pay.card2crypto.org/process-payment.php?address=".$encryptedWallet."&amount=".$amount."&provider=".$provider."&email=".$email."&currency=".$currency;

            return redirect()->away($paymentUrl);
        } else {
            \Log::error("Card2Crypto: Failed to generate payment link", ['response' => $wallet]);
            return redirect()->back()->withNotify([['error', 'Failed to generate payment link']]);
        }
    }

    public function handleCallback(Request $request)
    {
        // Validate and log everything from Card2Crypto
        \Log::info('Card2Crypto Callback', $request->all());

        $valueCoin = $request->input('value_coin');

        if (!in_array($request->coin, ['polygon_usdc', 'polygon_usdt'])) {
            return response()->json(['error' => 'Invalid coin'], 400);
        }

        $trx = $request->input('trx');
        if (!$trx) {
            return response()->json(['error' => 'Trx Missing'], 400);
        }

        $card2crypto_verify_token = $request->input('card2crypto_verify_token');
        if (!$card2crypto_verify_token) {
            return response()->json(['error' => 'Token Missing'], 400);
        }

        $deposit = Deposit::where('trx', $request->trx)
                        ->where('card2crypto_verify_token', $request->card2crypto_verify_token)
                        ->where('status', 0)
                        ->first();
        if (!$deposit) {
            return response()->json(['error' => 'Deposit not found or already processed'], 404);
        }

        // Compare paid amount with expected amount (allowing float tolerance)
        if (abs($deposit->final_amo - $valueCoin) > 0.0001) {
            \Log::error('Mismatched payment amount', $request->all());
            return response()->json(['error' => 'Mismatched payment amount'], 400);
        }

        $deposit->card2crypto_callback_data = json_encode($request->all());
        $deposit->update();

        PaymentController::userDataUpdate($deposit);
        
        return response()->json(['message' => 'Payment processed successfully']);
    }
}
