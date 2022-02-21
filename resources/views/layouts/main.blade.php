<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
        {!! ReCaptcha::htmlScriptTagJsApi(['form_id' => 'form-account-store']) !!}
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
