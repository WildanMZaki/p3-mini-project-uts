@extends('layouts.app')

@php
    $isPelapor = auth()->user()->role === \App\Enums\UserRole::Pelapor;
@endphp

@section('title', $isPelapor ? 'Laporan Saya' : 'Daftar Laporan')

@section('content')
    <div class="toolbar">
        <h1>{{ $isPelapor ? 'Laporan Saya' : 'Daftar Laporan' }}</h1>
        @can('create', \App\Models\Report::class)
            <a href="{{ route('reports.create') }}" class="btn btn-primary">+ Buat Laporan</a>
        @endcan
    </div>

    <div class="card">
        <label class="filters">
            <span>Cek kondisi area (tanpa reload):</span>
            <select data-area-summary="#index-area-summary" data-url="{{ route('areas.summary', '__AREA__') }}">
                <option value="">-- pilih area --</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->fullName() }}</option>
                @endforeach
            </select>
        </label>
        <div id="index-area-summary" style="margin-top: .75rem"></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Area</th>
                    @unless ($isPelapor)
                        <th>Pelapor</th>
                    @endunless
                    <th>Kondisi</th>
                    <th>Waktu Kejadian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td><a href="{{ route('reports.show', $report) }}">{{ $report->code }}</a></td>
                        <td>{{ $report->title }}</td>
                        <td>{{ $report->area->name }}</td>
                        @unless ($isPelapor)
                            <td>{{ $report->reporter->name }}</td>
                        @endunless
                        <td>{{ $report->condition_level->label() }}</td>
                        <td>{{ $report->occurred_at->format('d M Y H:i') }}</td>
                        <td><span class="badge badge-{{ $report->status->value }}">{{ $report->status->label() }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isPelapor ? 6 : 7 }}" class="muted">Belum ada laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $reports->links() }}
@endsection

@push('scripts')
    <script src="{{ asset('js/area-summary.js') }}"></script>
@endpush