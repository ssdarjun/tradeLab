@extends($activeTemplate.'layouts.master')
@section('content')
<div class="container">
    <form action="{{route('user.card2crypto.redirectToPayment')}}" method="post">
        @csrf
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card custom--card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Conversation Rate')</h5>
                    </div>
                    <div class="card-body">
                        <div class="mt-3 preview-details">
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($conversationRates as $currencyName => $conversationRate)
                                <li
                                    class="list-group-item d-flex justify-content-between  bg-transparent text-white b-input">
                                    <span><span>1 {{ $currencyName }}</span></span>
                                    <span><span class="min fw-bold">{{ number_format((float)$conversationRate, 15, '.', '') }}</span>
                                        {{__($general->cur_text)}}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Deposit')</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="form-label">@lang('Payer Email')</label>
                            <input type="email" name="email" class="form-control cmn--form--control"
                                value="{{ old('email') }}" autocomplete="off" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Currency')</label>
                            <select name="currency" class="form-control cmn--form--control"
                                value="{{ old('currency') }}" autocomplete="off" required>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="CAD">CAD</option>
                                <!--option value="INR">INR</option-->
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Provider')</label>
                            <select name="provider" class="form-control cmn--form--control"
                                value="{{ old('provider') }}" autocomplete="off" required>
                                <option value="wert">wert.io (USD)</option>
                                <option value="werteur">wert.io (EUR)</option>
                                <option value="switchere">Switchere</option>
                                <option value="stripe">Stripe (USA Only)</option>
                                <option value="sardine">Sardine.ai</option>
                                <option value="revolut">Revolut</option>
                                <option value="guardarian">Guardarian</option>
                                <option value="particle">particle.network</option>
                                <option value="transak">Transak</option>
                                <option value="banxa">Banxa</option>
                                <option value="simplex">Simplex</option>
                                <option value="changenow">ChangeNOW</option>
                                <option value="mercuryo">mercuryo.io</option>
                                <option value="rampnetwork">ramp.network (USD)</option>
                                <option value="moonpay">MoonPay</option>
                                <option value="alchemypay">Alchemy Pay</option>
                                <option value="robinhood">Robinhood (USD)</option>
                                <option value="coinbase">coinbase PAY</option>
                                <option value="utorg">UTORG</option>
                                <option value="unlimit">Unlimit</option>
                                <option value="bitnovo">Bitnovo</option>
                                <option value="simpleswap">SimpleSwap</option>
                                <option value="finchpay">FinchPay</option>
                                <option value="topper">Topper</option>
                                <option value="swipelux">Swipelux</option>
                                <option value="kado">Kado.money</option>
                                <option value="itez">Itez</option>
                                <option value="transfi">Transfi (USD)</option>
                                <option value="interac">Interac (CAD)</option>
                                <option value="upi">UPI/IMPS (INR)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control cmn--form--control"
                                    value="{{ old('amount') }}" autocomplete="off" required>
                                <span class="input-group-text">{{ $general->cur_text }}</span>
                            </div>
                        </div>

                        <button type="submit" class="cmn--btn btn-block">@lang('Submit')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
(function($) {
    "use strict";



})(jQuery);
</script>
@endpush