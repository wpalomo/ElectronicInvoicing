@extends('layouts.app')

@section('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script id="modal" src="{{ asset('js/app/modal.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#companies').DataTable({
        "order": [[ 2, 'asc' ], [ 1, 'asc' ]]
    });
});
</script>
@endsection

@section('styles')
<link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ ucfirst(trans_choice(__('view.company'), 1)) }}
                    @if(auth()->user()->can('create_companies'))
                        <a href="{{ route('companies.create') }}" class="btn btn-sm btn-primary float-right">{{ trans_choice(__('view.new'), 1) }}</a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table id="companies" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('view.ruc') }}</th>
                                <th>{{ __('view.social_reason') }} - {{ __('view.tradename') }}</th>
                                @if(auth()->user()->can('delete_hard_companies'))
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td align="center"><img class="img-thumbnail" src="{{ url('storage/logo/thumbnail/'.$company->logo) }}" alt="{{ $company->tradename }}"></td>
                                    <td>{{ $company->ruc }}</td>
                                    <td>
                                        @if($company->deleted_at !== NULL)
                                            {{ $company->social_reason }} - {{ $company->tradename }}
                                        @else
                                            @if(auth()->user()->can('update_companies'))
                                                <a href="{{ route('companies.edit', $company) }}">{{ $company->social_reason }} - {{ $company->tradename }}</a>
                                            @elseif(auth()->user()->can('read_companies'))
                                                <a href="{{ route('companies.show', $company) }}">{{ $company->social_reason }} - {{ $company->tradename }}</a>
                                            @else
                                                {{ $company->social_reason }} - {{ $company->tradename }}
                                            @endif
                                        @endif
                                    </td>
                                    @if(auth()->user()->can('delete_hard_companies'))
                                        <td>
                                            @if($company->deleted_at !== NULL)
                                                @if(auth()->user()->can('delete_hard_companies'))
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#confirmation"
                                                        data-title="{{ trans_choice(__('view.are_you_sure_you_want_to_activate_the_model', ['model' => trans_choice(__('view.company'), 0), 'name' => $company->tradename . ' - ' . $company->social_reason]), 1) }}"
                                                        data-body="{{ trans_choice(__('view.all_model_data_will_be_restored', ['model' => trans_choice(__('view.company'), 0)]), 1) }}"
                                                        data-form="{{ route('companies.restore', $company->id) }}"
                                                        data-method="POST"
                                                        data-class="btn btn-sm btn-success"
                                                        data-action="{{ __('view.activate') }}">{{ __('view.activate') }}</button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmation"
                                                        data-title="{{ trans_choice(__('view.are_you_sure_you_want_to_delete_the_model', ['model' => trans_choice(__('view.company'), 0), 'name' => $company->tradename . ' - ' . $company->social_reason]), 1) }}"
                                                        data-body="{{ trans_choice(__('view.warning_all_model_data_will_be_deleted_this_action_can_not_be_undone', ['model' => trans_choice(__('view.company'), 0)]), 1) }}"
                                                        data-form="{{ route('companies.destroy', $company->id) }}"
                                                        data-method="DELETE"
                                                        data-class="btn btn-sm btn-danger"
                                                        data-action="{{ __('view.delete') }}">{{ __('view.delete') }}</button>
                                                @endif
                                            @else
                                                @if(auth()->user()->can('delete_hard_companies'))
                                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#confirmation"
                                                        data-title="{{ trans_choice(__('view.are_you_sure_you_want_to_deactivate_the_model', ['model' => trans_choice(__('view.company'), 0), 'name' => $company->tradename . ' - ' . $company->social_reason]), 1) }}"
                                                        data-body="{{ trans_choice(__('view.the_data_of_the_model_will_remain_in_the_application', ['model' => trans_choice(__('view.company'), 0)]), 1) }}"
                                                        data-form="{{ route('companies.delete', $company) }}"
                                                        data-method="DELETE"
                                                        data-class="btn btn-sm btn-warning"
                                                        data-action="{{ __('view.deactivate') }}">{{ __('view.deactivate') }}</button>
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.confirmation')
@endsection
