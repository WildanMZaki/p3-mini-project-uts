@extends('layouts.app')

@section('title', 'Perbaiki Laporan')

@section('content')
    <h1>Perbaiki Laporan {{ $report->code }}</h1>

    <div class="card">
        <form method="POST" action="{{ route('reports.update', $report) }}" class="form">
            @csrf
            @method('PUT')
            @include('reports._form')
            <div class="actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('reports.show', $report) }}" class="btn">Batal</a>
            </div>
        </form>
    </div>
@endsection
