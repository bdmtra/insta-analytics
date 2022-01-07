@extends('layouts.main')

@section('content')

    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif

    {{ Form::open(array('url' => 'account/store')) }}

    <div class="input-group mt-5">
        {{ Form::text('username', Input::old('username'), array('class' => 'form-control', 'placeholder' => 'Username')) }}
        <div class="input-group-append">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>

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
