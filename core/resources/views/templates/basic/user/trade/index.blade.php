@extends($activeTemplate.'layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center gy-4">
        <div class="col-12">
            <div class="card custom--card border-0">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Name')</label>
                                <input type="text" name="search" value="{{ request()->search }}"
                                    class="form-control cmn--form--control">
                            </div>
                            <div class="flex-lg-grow-1 "></div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="cmn--btn btn-block h-100"><i class="las la-filter"></i>
                                    @lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-dark table-striped align-middle text-left">
                    <thead>
                        <tr>
                            <th style="width: 50px">@lang('Image')</th>
                            <th>@lang('Name')</th>
                            <th style="width: 120px">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cryptos as $crypto)
                        <tr>
                            <td>
                                <a href="{{ route('user.trade.now', $crypto->name) }}">
                                    <img src="{{ getImage(getFilePath('crypto_currency') . '/' . $crypto->image, getFileSize('crypto_currency')) }}"
                                        alt="{{ $crypto->name }}" width="40" height="40">
                                </a>
                            </td>
                            <td>{{ __($crypto->name) }}</td>
                            <td>
                                <a href="{{ route('user.trade.now', $crypto->name) }}" class="btn btn-sm btn-primary">
                                    @lang('Trade Now')
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endsection

    @push('style')
    <style>
    .deposit__thumb {
        height: auto;
        max-height: 350px !important;
    }
    </style>
    @endpush