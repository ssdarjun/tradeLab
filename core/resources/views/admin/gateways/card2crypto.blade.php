@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <form action="{{ route('admin.gateway.card2crypto.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="payment-method-item">

                        <div class="payment-method-body">
                            <div class="row mb-none-15">
                                <div class="col-12 col-md-6 mb-15">
                                    <div class="form-group">
                                        <label>@lang('Wallet Address (Receiver)')</label>
                                        <input type="text" class="form-control " name="wallet_address"
                                           value="{{ $gateways->wallet_address ?? '' }}" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-none-15">
                                <div class="col-12 col-md-6 mb-15">
                                    <div class="card border--primary mt-3">
                                        <h5 class="card-header bg--primary">@lang('Rate')</h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-text">1 {{ __($general->cur_text )}} =</div>
                                                    <input type="number" step="any" class="form-control" name="USD"
                                                        required value="{{ $gateways->rate['USD'] ?? '' }}" />
                                                    <div class="input-group-text"><span
                                                            class="currency_symbol"></span>USD</div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-text">1 {{ __($general->cur_text )}} =</div>
                                                    <input type="number" step="any" class="form-control" name="EUR"
                                                        required value="{{ $gateways->rate['EUR'] ?? '' }}" />
                                                    <div class="input-group-text"><span
                                                            class="currency_symbol"></span>EUR</div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-text">1 {{ __($general->cur_text )}} =</div>
                                                    <input type="number" step="any" class="form-control" name="CAD"
                                                        required value="{{ $gateways->rate['CAD'] ?? '' }}" />
                                                    <div class="input-group-text"><span
                                                            class="currency_symbol"></span>CAD</div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-text">1 {{ __($general->cur_text )}} =</div>
                                                    <input type="number" step="any" class="form-control" name="INR"
                                                        required value="{{ $gateways->rate['INR'] ?? '' }}" />
                                                    <div class="input-group-text"><span
                                                            class="currency_symbol"></span>INR</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <div class="card border--primary mt-3">
                                        <h5 class="card-header bg--primary">@lang('Range')</h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>@lang('Minimum Amount')</label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control"
                                                        name="min_amount" required value="{{ $gateways->min_amount ?? '' }}" />
                                                    <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('Maximum Amount')</label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control"
                                                        name="max_amount" required value="{{ $gateways->max_amount ?? '' }}" />
                                                    <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <div class="card border--primary mt-3">
                                        <h5 class="card-header bg--primary">@lang('Charge')</h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>@lang('Fixed Charge')</label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control"
                                                        name="fixed_charge" required
                                                        value="{{ $gateways->fixed_charge ?? '' }}"
                                                         />
                                                    <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('Percent Charge')</label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control"
                                                        name="percent_charge" required
                                                        value="{{ $gateways->percent_charge ?? '' }}"
                                                        >
                                                    <div class="input-group-text">%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-form-generator />
@endsection

@push('script')
<script>

</script>
@endpush