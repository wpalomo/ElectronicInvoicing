@extends('layouts.app')

@section('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$.noConflict();
jQuery(document).ready(function($) {
    $('#customers-table').DataTable();
    $('#customerModal').on('show.bs.modal', function(event) {
        $(this).find('#modal-title').text($(event.relatedTarget).data('title'))
        $(this).find('#modal-body').text($(event.relatedTarget).data('body'))
        $(this).find("#modal-form").attr("action", $(event.relatedTarget).data('form'))
        $(this).find("#form-method").val($(event.relatedTarget).data('method'))
        $(this).find('#submit-action').attr("class", $(event.relatedTarget).data('class'))
        $(this).find('#submit-action').text($(event.relatedTarget).data('action'))
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Customers
                    @if(auth()->user()->can('create_branches'))
                        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary float-right">New</a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table id="customers-table" class="display">
                        <thead>
                            <tr>
                                <th>Identification</th>
                                <th>Social reason</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->identification }}</td>
                                    <td>
                                        @if($customer->deleted_at !== NULL)
                                            {{ $customer->social_reason }}
                                        @else
                                            @if(auth()->user()->can('update_customers'))
                                                <a href="{{ route('customers.edit', $customer) }}">{{ $customer->social_reason }}</a>
                                            @elseif(auth()->user()->can('read_customers'))
                                                <a href="{{ route('customers.show', $customer) }}">{{ $customer->social_reason }}</a>
                                            @else
                                                {{ $customer->social_reason }}
                                            @endif
                                        @endif
                                    </td>
                                    <td>USERMAIL</td>
                                    <td>
                                        @if($customer->deleted_at !== NULL)
                                            @if(auth()->user()->can('delete_hard_customers'))
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#customerModal"
                                                    data-title="Are you sure you want to activate the branch {{ $branch->name }}?"
                                                    data-body="All branch data will be restored."
                                                    data-form="{{ route('branches.restore', $branch->id) }}"
                                                    data-method="POST"
                                                    data-class="btn btn-sm btn-success"
                                                    data-action="Activate">Activate</button>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#customerModal"
                                                    data-title="Are you sure you want to delete the branch {{ $branch->name }}?"
                                                    data-body="WARNING: All branch data will be deleted. This action can not be undone."
                                                    data-form="{{ route('branches.destroy', $branch->id) }}"
                                                    data-method="DELETE"
                                                    data-class="btn btn-sm btn-danger"
                                                    data-action="Delete">Delete</button>
                                            @endif
                                        @else
                                            @if(auth()->user()->can('delete_hard_branches'))
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#customerModal"
                                                    data-title="Are you sure you want to deactivate the branch {{ $branch->name }}?"
                                                    data-body="The data of the branch will remain in the application, but the users that depend on it will not be able to access the data. If you want to restore it, contact the administrator."
                                                    data-form="{{ route('branches.delete', $branch) }}"
                                                    data-method="DELETE"
                                                    data-class="btn btn-sm btn-warning"
                                                    data-action="Deactivate">Deactivate</button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#customerModal"
                                                    data-title="Are you sure you want to deactivate the branch {{ $branch->name }}?"
                                                    data-body="The data of the branch will remain in the application, but the users that depend on it will not be able to access the data. If you want to restore it, contact the administrator."
                                                    data-form="{{ route('branches.delete', $branch) }}"
                                                    data-method="DELETE"
                                                    data-class="btn btn-sm btn-warning"
                                                    data-action="Delete">Delete</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"><center>No records found.</center></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="CustomerModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <strong><p id="modal-title"></p></strong>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"><p id="modal-body"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                <form id="modal-form" action="#" method="post">
                    {{ csrf_field() }}
                    <input id="form-method" type="hidden" name="_method" value="" />
                    <button id="submit-action" type="submit" class=""></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
