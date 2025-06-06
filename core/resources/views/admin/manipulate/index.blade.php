@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Crypto')</th>
                                <th>@lang('Start Time')</th>
                                <th>@lang('End Time')</th>
                                <th>@lang('Prediction Override')</th>
                                <th>@lang('Min. Amount')</th>
                                <th>@lang('max. Amount')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($games as $game)
                            <tr>
                                <td>{{$loop->index+$games->firstItem()}}</td>
                                <td>{{$game->crypto->name ?? ''}}</td>
                                <td>{{$game->start_time}}</td>
                                <td>{{$game->end_time}}</td>
                                <td>{{$game->prediction_override == 1 ? 'High' : 'Low'}}</td>
                                <td>{{$game->min}}</td>
                                <td>{{$game->max}}</td>
                                <td>
                                    <button type="button"  class="btn btn-sm btn-outline--primary editBtn" data-game='@json($game)'>
                                        <i class="la la-pencil"></i>@lang('Edit')
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                            data-question="@lang('Are you sure to delete this trade manipulate setting')?"
                                            data-action="{{route('admin.trade.manipulate.delete',$game->id) }}">
                                        <i class="las la-trash"></i>@lang('Delete')
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($games->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($games)}}
            </div>
            @endif
        </div>
    </div>
</div>

<div id="cryptoModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('title')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Crypto')</label>
                        <select class="form-control" name="crypto_id" required>
                            <option selected disabled value="">@lang('Select One')</option>
                           @foreach($cryptos as $crypto)
                                <option value="{{ $crypto->id }}">{{ $crypto->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        // Get current datetime in format: "YYYY-MM-DDTHH:MM"
                        $now = now()->format('Y-m-d\TH:i');
                    @endphp

                    <div class="form-group">
                        <label>@lang('Start Time')</label>
                        <input type="datetime-local" class="form-control" name="start_time" required min="{{ $now }}">
                    </div>

                    <div class="form-group">
                        <label>@lang('End Time')</label>
                        <input type="datetime-local" class="form-control" name="end_time" required min="{{ $now }}">
                    </div>

                     <div class="form-group">
                        <label>@lang('Pediction Override')</label>
                        <select class="form-control" name="prediction_override" required>
                            <option selected disabled>@lang('Select One')</option>
                             <option value="1">High</option>
                             <option value="2">Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>@lang('Min. Amount')</label>
                        <input type="number" step="0.01" min="0" class="form-control" default="0" name="min" required>
                    </div>

                    <div class="form-group">
                        <label>@lang('Max. Amount')</label>
                        <input type="number" step="0.01" min="0" class="form-control" default="0" name="max" required>
                    </div>

                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<x-confirmation-modal/>
@endsection

@push('breadcrumb-plugins')
<button type="button" class="btn btn-sm btn-outline--primary addBtn">
    <i class="las la-plus"></i>@lang('Add New')
</button>
@endpush

@push('script')
<script>
    "use strict";
    (function ($) {

        let modal = $('#cryptoModal');

        $('.addBtn').on('click', function (e) {
            let action = `{{ route('admin.trade.manipulate.save') }}`;
            modal.find('form').trigger('reset');
            modal.find('.modal-title').text("@lang('Add Trade Manipulate Setting')")
            modal.find('form').prop('action', action);
            $(modal).modal('show');
        });

        $('.editBtn').on('click', function (e) {
            let action = `{{ route('admin.trade.manipulate.save',':id') }}`;
            let data   = $(this).data('game');
            modal.find('form').prop('action', action.replace(':id', data.id));
            modal.find('form').trigger('reset');

            function formatDateTime(dt) {
                if (!dt) return '';
                return dt.replace(' ', 'T').substring(0, 16);
            }

            modal.find("select[name=crypto_id]").val(data.crypto_id);
            modal.find("input[name=start_time]").val(formatDateTime(data.start_time));
            modal.find("input[name=end_time]").val(formatDateTime(data.end_time));
            modal.find("select[name=prediction_override]").val(data.prediction_override);
            modal.find("input[name=min]").val(data.min);
            modal.find("input[name=max]").val(data.max);

            modal.find('.modal-title').text(`@lang('Update Trade Manipulate Setting')`);
            $(modal).modal('show');
        });
    })(jQuery);
</script>
@endpush
