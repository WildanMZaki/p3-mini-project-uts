@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Halo, {{ $user->name }}</h1>
    <p class="muted">Anda masuk sebagai <strong>{{ $user->role->label() }}</strong>.</p>

    <div class="grid">
        @foreach ($stats as $label => $value)
            <div class="card">
                <div class="muted">{{ $label }}</div>
                <div class="stat">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="actions">
        @can('create', \App\Models\Report::class)
            <a href="{{ route('reports.create') }}" class="btn btn-primary">+ Buat Laporan</a>
        @endcan
        <a href="{{ route('reports.index') }}" class="btn">Lihat Laporan</a>
        @can('view-recap')
            <a href="{{ route('recap') }}" class="btn">Rekap</a>
        @endcan
    </div>
@endsection