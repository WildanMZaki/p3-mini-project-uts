@extends('layouts.app')

@section('title', 'Rekap Laporan')

@section('content')
    <h1>Rekap Laporan</h1>
    <p class="muted">Gambaran umum kondisi laporan pada aplikasi komunitas berdasarkan data aktual.</p>

    <form method="GET" action="{{ route('recap') }}" class="card filters">
        <label>
            Area
            <select name="area_id">
                <option value="">Semua area</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" @selected(($filters['area_id'] ?? null) == $area->id)>{{ $area->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Kejadian dari
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
        </label>
        <label>
            Sampai
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
        </label>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('recap') }}" class="btn">Reset</a>
    </form>

    <div class="grid">
        <div class="card">
            <div class="muted">Total laporan</div>
            <div class="stat">{{ $summary['total'] }}</div>
        </div>
        <div class="card">
            <div class="muted">Masih aktif</div>
            <div class="stat">{{ $summary['active'] }}</div>
        </div>
        <div class="card">
            <div class="muted">Menunggu diteruskan</div>
            <div class="stat">{{ $summary['awaiting_forward'] }}</div>
        </div>
        <div class="card">
            <div class="muted">Proses ditutup</div>
            <div class="stat">{{ $summary['closed'] }}</div>
        </div>
    </div>

    <h2>Rekap per Area</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Area</th>
                    <th>Total</th>
                    @foreach ($statuses as $status)
                        <th>{{ $status->label() }}</th>
                    @endforeach
                    <th>Api terlihat/meluas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row->area->fullName() }}</td>
                        <td>{{ (int) $row->total }}</td>
                        @foreach ($statuses as $status)
                            <td>{{ (int) $row->{$status->value} }}</td>
                        @endforeach
                        <td>{{ (int) $row->critical }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($statuses) + 3 }}" class="muted">Tidak ada data untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($rows->isNotEmpty())
                <tfoot>
                    <tr>
                        <td>Jumlah</td>
                        <td>{{ (int) $rows->sum('total') }}</td>
                        @foreach ($statuses as $status)
                            <td>{{ (int) $rows->sum($status->value) }}</td>
                        @endforeach
                        <td>{{ (int) $rows->sum('critical') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection
