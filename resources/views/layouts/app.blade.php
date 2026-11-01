<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    @auth
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="brand">Karhutla Lapor</a>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('reports.index') }}">Laporan</a>
            @can('create', \App\Models\Report::class)
                <a href="{{ route('reports.create') }}">Buat Laporan</a>
            @endcan
            @can('view-recap')
                <a href="{{ route('recap') }}">Rekap</a>
            @endcan
        </div>
        <div class="nav-user">
            <span>{{ auth()->user()->name }} <small>({{ auth()->user()->role->label() }})</small></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link">Logout</button>
            </form>
        </div>
    </nav>
    @endauth

    <main class="container">
        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>