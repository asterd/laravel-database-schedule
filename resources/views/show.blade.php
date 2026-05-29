@extends('schedule::layout.master')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>

    <div class="container">
        @include('schedule::messages')
        <div class="card">
            <div class="card-header">{{ trans('schedule::schedule.titles.show') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 my-3">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr class="d-flex">
                                    <th class="col-2">{{ trans('schedule::schedule.fields.command') }}</th>
                                    <th class="col-4">{{ trans('schedule::schedule.fields.arguments') }}</th>
                                    <th class="col-3">{{ trans('schedule::schedule.fields.options') }}</th>
                                    <th class="col-2">{{ trans('schedule::schedule.fields.expression') }}</th>
                                    <th class="col-1">{{ trans('schedule::schedule.fields.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($histories as $history)
                                <tr class="d-flex">
                                    <td class="col-2">{{ $history->command }}</td>
                                    <td class="col-4">
                                        @if(isset($history->params))
                                            @foreach($history->params as $param => $value)
                                                {{ $param }}={{$value}}<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="col-3">
                                        @if(isset($history->options))
                                            @foreach($history->options as $param => $value)
                                                @if(is_integer($param))
                                                    {{ $value }}
                                                @else
                                                    {{ $param }}={{ $value }}
                                                @endif
                                                <br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="col-2">{{ $history->created_at }}</td>
                                    <td class="col-1">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary p-1"
                                                data-toggle="modal"
                                                data-target="#history-output-modal-{{ $history->getKey() }}"
                                                title="{{ trans('schedule::schedule.fields.output') }}">
                                            <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary copy-output p-1"
                                                data-clipboard-text="{{ $history->output }}"
                                                title="{{ trans('schedule::schedule.fields.output') }}">
                                            <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="d-flex">
                                    <td colspan="2" class="col-12">
                                        <pre style="overflow: scroll;white-space: pre-line; max-width: 100%; height: 80px">
                                            {{ $history->output }}
                                        </pre>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @foreach($histories as $history)
                            <div class="modal fade"
                                 id="history-output-modal-{{ $history->getKey() }}"
                                 tabindex="-1"
                                 role="dialog"
                                 aria-labelledby="history-output-modal-title-{{ $history->getKey() }}"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable"
                                     role="document"
                                     style="max-width: calc(100vw - 2rem);">
                                    <div class="modal-content" style="height: calc(100vh - 2rem);">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="history-output-modal-title-{{ $history->getKey() }}">
                                                {{ $history->command }} - {{ $history->created_at }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <pre class="bg-dark text-light p-3 mb-0 h-100"
                                                 style="overflow: auto; white-space: pre-wrap;">{{ $history->output }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- JavaScript -->
                <script>
                    new ClipboardJS('.copy-output');

                    @if($histories->contains(function ($history) {
                        return str_contains($history->output ?? '', 'Queued at ')
                            && !str_contains($history->output ?? '', 'Finished at ');
                    }))
                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);
                    @endif
                </script>

                <div class='d-flex'>
                    <div class='mx-auto'>
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <div class="row">
                    <div class="col-6 text-left">
                        <a href="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@index') }}" class="btn btn-secondary">{{ trans('schedule::schedule.buttons.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
