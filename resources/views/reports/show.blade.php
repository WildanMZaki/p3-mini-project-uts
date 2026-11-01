@extends('layouts.app')

@section('title', $report->code)

@section('content')
    <div class="toolbar">
        <div>
            <h1>{{ $report->code }} — {{ $report->title }}</h1>
            <span class="badge badge-{{ $report->status->value }}">{{ $report->status->label() }}</span>
        </div>
        <div class="actions">
            <a href="{{ route('reports.edit', $report) }}" class="btn">Perbaiki</a>
            <form method="POST" action="{{ route('reports.destroy', $report) }}"
                  onsubmit="return confirm('Hapus laporan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
    </div>

    <div class="card">
        <dl class="detail">
            <dt>Pelapor</dt>
            <dd>{{ $report->reporter->name }}</dd>

            <dt>Area</dt>
            <dd>{{ $report->area->fullName() }}, {{ $report->area->province }}</dd>

            <dt>Detail lokasi</dt>
            <dd>{{ $report->location_detail }}</dd>

            <dt>Koordinat</dt>
            <dd>{{ $report->latitude }}, {{ $report->longitude }}</dd>

            <dt>Waktu kejadian</dt>
            <dd>{{ $report->occurred_at->format('d M Y H:i') }}</dd>

            <dt>Tingkat kondisi</dt>
            <dd>{{ $report->condition_level->label() }}</dd>

            <dt>Deskripsi</dt>
            <dd>{!! nl2br(e($report->description)) !!}</dd>

            <dt>Dikirim</dt>
            <dd>{{ $report->created_at->format('d M Y H:i') }}</dd>
        </dl>
    </div>
@endsection
