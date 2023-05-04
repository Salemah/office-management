<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>
        @yield('title')
    </title>
    @php
        $dashboard_settings = \Illuminate\Support\Facades\DB::table('dashboard_settings')->first();
        $user = \Illuminate\Support\Facades\DB::table('users')->find(Auth::id());
        $clients = \Illuminate\Support\Facades\DB::table('clients')->get();
    @endphp

    <!-- Link File -->
    @include('layouts.dashboard.partials.style')


</head>
<body>

{{-- Sidebar --}}
@include('layouts.dashboard.partials.sidebar')
{{--/.Sidebar--}}

<div class="wrapper d-flex flex-column min-vh-100 bg-light">
    {{-- Header --}}
    @include('layouts.dashboard.partials.header')
    {{--./ Header --}}

    <div class="body flex-grow-1 px-3">
        <div class="container-lg">
            @yield('content')
        </div>
    </div>
</div>

<!-- Script File -->
@include('layouts.dashboard.partials.script')

</body>
</html>
