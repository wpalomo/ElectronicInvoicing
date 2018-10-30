@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Edit company
                    <a href="{{ route('companies.index') }}" class="btn btn-sm btn-secondary float-right">Cancel</a>
                </div>

                <form action="{{ route('companies.update', $company) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT">

                    <div class="card-body">
                        @if ($errors->count() > 0)
                            <div class="alert alert-danger" role="alert">
                                <h5>The following errors were found:</h5>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="ruc">RUC</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" value="{{ $company->ruc }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="social_reason">Social reason</label>
                            <input type="text" class="form-control" id="social_reason" name="social_reason"  value="{{ $company->social_reason }}">
                        </div>
                        <div class="form-group">
                            <label for="tradename">Tradename</label>
                            <input type="text" class="form-control" id="tradename" name="tradename"  value="{{ $company->tradename }}">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address"  value="{{ $company->address }}">
                        </div>
                        <div class="form-group">
                            <label for="special_contributor">Special contributor</label>
                            <input type="text" class="form-control" id="special_contributor" name="special_contributor"  value="{{ $company->special_contributor }}">
                        </div>
                        <div class="form-check">
                            @if ($company->keep_accounting)
                                <input class="form-check-input" checked="checked" type="checkbox" id="keep_accounting" name="keep_accounting">
                            @else
                                <input class="form-check-input" type="checkbox" id="keep_accounting" name="keep_accounting">
                            @endif
                            <label class="form-check-label" for="keep_accounting">Keep accounting</label>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $company->phone }}">
                        </div>
                        <div class="form-group">
                            <label for="current_logo">Current logo</label><br>
                            <img class="img-fluid img-thumbnail" src="{{ url('storage/logo/images/'.$company->logo) }}" alt="">
                            <input type="hidden" name="current_logo" value="{{ $company->logo }}">
                        </div>
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" class="form-control-file" id="logo" name="logo">
                        </div>
                        <div class="form-group">
                            <label for="sign">Sign</label>
                            <input type="file" class="form-control-file" id="sign" name="sign">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection