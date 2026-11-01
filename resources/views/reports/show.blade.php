@extends('layouts.app')

@section('title', $report->code)

@section('content')
    <div class="toolbar">
        <div>
            <h1>{{ $report->code }} &mdash; {{ $report->title }}</h1>
            <span class="badge badge-{{ $report->status->value }}">{{ $report->status->label() }}</span>
        </div>
        <div class="actions">
            @can('update', $report)
                <a href="{{ route('reports.edit', $report) }}" class="btn">Perbaiki</a>
            @endcan
            @can('delete', $report)
                <form method="POST" action="{{ route('reports.destroy', $report) }}"
                      onsubmit="return confirm('Hapus laporan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    @if ($report->status === \App\Enums\ReportStatus::PerluPerbaikan && $report->isOwnedBy(auth()->user()))
        <div class="alert alert-info">
            Relawan meminta perbaikan: <strong>{{ $report->review_note }}</strong><br>
            Silakan klik <em>Perbaiki</em>; setelah disimpan laporan akan ditinjau ulang.
        </div>
    @endif

    @if ($report->status->isFinal())
        <p class="muted">
            Catatan: status akhir menandakan proses pada aplikasi komunitas selesai,
            bukan pernyataan resmi mengenai kejadian kebakaran.
        </p>
    @endif

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

    <div class="card">
        <h2>Bukti Pendukung</h2>
        @if ($report->hasEvidence())
            @if ($report->evidenceIsImage())
                <p><img src="{{ route('reports.evidence', $report) }}" alt="Bukti laporan {{ $report->code }}" class="evidence-preview"></p>
            @endif
            <p><a href="{{ route('reports.evidence', $report) }}" target="_blank">Buka {{ $report->evidence_original_name }}</a></p>
        @else
            <p class="muted">Tidak ada bukti yang dilampirkan.</p>
        @endif
    </div>

    <div class="card">
        <h2>Peninjauan</h2>
        <dl class="detail">
            <dt>Relawan peninjau</dt>
            <dd>{{ $report->reviewer?->name ?? '-' }}</dd>

            <dt>Catatan tinjauan</dt>
            <dd>{{ $report->review_note ?? '-' }}</dd>

            <dt>Waktu keputusan</dt>
            <dd>{{ $report->reviewed_at?->format('d M Y H:i') ?? '-' }}</dd>
        </dl>

        @can('review', $report)
            @if ($report->reviewer_id !== null && ! $report->reviewer()->is(auth()->user()))
                <p class="muted" style="margin-top: 1rem">
                    Laporan ini sedang ditangani oleh {{ $report->reviewer->name }}.
                </p>
            @elseif ($report->status === \App\Enums\ReportStatus::Baru)
                <form method="POST" action="{{ route('reports.claim', $report) }}" class="actions" style="margin-top: 1rem">
                    @csrf
                    <button type="submit" class="btn btn-primary">Ambil untuk Ditinjau</button>
                </form>
            @elseif ($report->status === \App\Enums\ReportStatus::Ditinjau)
                <form method="POST" action="{{ route('reports.review', $report) }}" class="form" style="margin-top: 1rem">
                    @csrf
                    <label>
                        Keputusan tinjauan
                        <select name="decision" required>
                            <option value="">-- pilih keputusan --</option>
                            @foreach ($decisions as $decision)
                                <option value="{{ $decision->value }}" @selected(old('decision') === $decision->value)>
                                    {{ $decision->label() }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Catatan (kelengkapan, kewajaran, keterkaitan dengan laporan lain)
                        <textarea name="review_note" required minlength="10" maxlength="1000">{{ old('review_note') }}</textarea>
                    </label>
                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
                    </div>
                </form>
            @endif
        @endcan
    </div>

    @if ($report->forwardings->isNotEmpty() || auth()->user()->can('forward', $report))
        <div class="card">
            <h2>Penerusan ke Pihak Terkait</h2>

            @if ($report->forwardings->isEmpty())
                <p class="muted">Belum ada penerusan yang dicatat.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Pihak Terkait</th>
                                <th>Media</th>
                                <th>Waktu</th>
                                <th>Dicatat oleh</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report->forwardings as $forwarding)
                                <tr>
                                    <td>{{ $forwarding->agency->name }}</td>
                                    <td>{{ $forwarding->channel->label() }}</td>
                                    <td>{{ $forwarding->forwarded_at->format('d M Y H:i') }}</td>
                                    <td>{{ $forwarding->coordinator->name }}</td>
                                    <td>{{ $forwarding->note ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @can('forward', $report)
                @if (in_array($report->status, [\App\Enums\ReportStatus::PerluDiteruskan, \App\Enums\ReportStatus::Diteruskan], true))
                    <form method="POST" action="{{ route('reports.forward', $report) }}" class="form" style="margin-top: 1rem">
                        @csrf
                        <div class="row">
                            <label>
                                Pihak terkait
                                <select name="agency_id" required>
                                    <option value="">-- pilih pihak --</option>
                                    @foreach ($agencies as $agency)
                                        <option value="{{ $agency->id }}" @selected(old('agency_id') == $agency->id)>{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                Media penerusan
                                <select name="channel" required>
                                    @foreach ($channels as $channel)
                                        <option value="{{ $channel->value }}" @selected(old('channel') === $channel->value)>{{ $channel->label() }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                Waktu penerusan
                                <input type="datetime-local" name="forwarded_at" value="{{ old('forwarded_at', now()->format('Y-m-d\TH:i')) }}" required>
                            </label>
                        </div>
                        <label>
                            Catatan (opsional)
                            <textarea name="note" maxlength="500">{{ old('note') }}</textarea>
                        </label>
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Catat Penerusan</button>
                        </div>
                    </form>
                @endif
            @endcan

            @can('close', $report)
                @if ($report->status === \App\Enums\ReportStatus::Diteruskan)
                    <form method="POST" action="{{ route('reports.close', $report) }}" class="actions" style="margin-top: 1rem"
                          onsubmit="return confirm('Tandai proses laporan ini selesai?')">
                        @csrf
                        <button type="submit" class="btn">Tandai Proses Selesai</button>
                    </form>
                @endif
            @endcan
        </div>
    @endif

    @if ($related->isNotEmpty())
        <div class="card">
            <h2>Laporan Lain di Area yang Sama (±48 jam)</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Waktu Kejadian</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($related as $item)
                            <tr>
                                <td><a href="{{ route('reports.show', $item) }}">{{ $item->code }}</a></td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->reporter->name }}</td>
                                <td>{{ $item->occurred_at->format('d M Y H:i') }}</td>
                                <td><span class="badge badge-{{ $item->status->value }}">{{ $item->status->label() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
