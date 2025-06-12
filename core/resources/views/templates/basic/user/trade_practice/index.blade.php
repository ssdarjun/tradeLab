@extends($activeTemplate.'layouts.master')
@section('content')
<div class="container">
    <div class="row gy-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <button type="button" class="btn btn--base-outline btn-sm confirmationBtn"
                    data-question="@lang('Are you sure you want to add practice balance?')"
                    data-action="{{ route('user.add.practice.balance') }}">
                    <i class="las la-plus"></i> @lang('Add Practice Balance')
                </button>
                <h6 class="my-2">@lang('Practice Balance') : {{showAmount(auth()->user()->demo_balance)}}
                    {{__($general->cur_text)}}
                </h6>
            </div>
        </div>
    </div>

    <div class="row justify-content-center gy-4">
        <div class="col-12">
            <div class="card custom--card border-0">
                <div class="card-body">
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
                                <a href="{{route('user.practice.trade.now', $crypto->name)}}">
                                    <img src="{{getImage(getFilePath('crypto_currency').'/'.$crypto->image, getFileSize('crypto_currency'))}}"
                                        alt="{{ $crypto->name }}" width="40" height="40">
                                </a>
                            </td>
                            <td>{{ __($crypto->name) }}</td>
                            <td>
                                <a href="{{route('user.practice.trade.now', $crypto->name)}}"
                                    class="btn btn-sm btn-primary">
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
    <x-confirmation-modal />
    @endsection

    @push('style')
    <style>
    .deposit__thumb {
        overflow: hidden;
    }
    </style>
    @endpush