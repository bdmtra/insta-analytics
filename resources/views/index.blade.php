@extends('layouts.main')

@section('content')

    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif

    {{ Form::open(array('url' => 'account/store', 'id' => 'form-account-store')) }}

    <div class="input-group mt-5">
        {{ Form::text('username', Input::old('username'), array('class' => 'form-control', 'placeholder' => 'Username', 'helper' => '1')) }}
        <div class="input-group-append">
            {!! htmlFormButton('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    <small id="form-account-store-help-text" class="form-text text-muted"></small>

    {{ Form::close() }}

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@stop
