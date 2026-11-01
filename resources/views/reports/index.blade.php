@extends('layouts.app')

@section('title', 'Laporan Saya')

@section('content')
<div class="toolbar">
    <h1>Laporan Saya</h1>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">+ Buat Laporan</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Area</th>
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
                    <td>{{ $report->condition_level->label() }}</td>
                    <td>{{ $report->occurred_at->format('d M Y H:i') }}</td>
                    <td><span class="badge badge-{{ $report->status->value }}">{{ $report->status->label() }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">Belum ada laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $reports->links() }}
@endsection