@extends('layouts.app')

@section('title', 'Laporan Saya')

@section('content')
<div class="toolbar">
    <h1>Laporan Saya</h1>
    <a href="#" class="btn btn-primary">+ Buat Laporan</a>
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
            <tr>
                <td colspan="6" class="muted">Belum ada laporan.</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection