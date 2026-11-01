@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
    <h1>Buat Laporan Kejadian</h1>

    <div class="card">
        <form method="POST" action="{{ route('reports.store') }}" class="form">
            @csrf
            @include('reports._form')
            <div class="actions">
                <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                <a href="{{ route('reports.index') }}" class="btn">Batal</a>
            </div>
        </form>
    </div>
@endsection
