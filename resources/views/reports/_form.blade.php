@php
    /** @var \App\Models\Report|null $report */
    $report = $report ?? null;
@endphp

<label>
    Area / Wilayah
    <select name="area_id" required>
        <option value="">-- pilih area --</option>
        @foreach ($areas as $area)
            <option value="{{ $area->id }}" @selected(old('area_id', $report?->area_id) == $area->id)>
                {{ $area->fullName() }}
            </option>
        @endforeach
    </select>
</label>

<label>
    Judul laporan
    <input type="text" name="title" maxlength="150" value="{{ old('title', $report?->title) }}" required>
</label>

<div class="row">
    <label>
        Waktu kejadian
        <input type="datetime-local" name="occurred_at" id="occurred_at"
               value="{{ old('occurred_at', $report?->occurred_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" required>
    </label>
    <label>
        Tingkat kondisi
        <select name="condition_level" required>
            <option value="">-- pilih kondisi --</option>
            @foreach ($conditions as $condition)
                <option value="{{ $condition->value }}"
                    @selected(old('condition_level', $report?->condition_level?->value) === $condition->value)>
                    {{ $condition->label() }}
                </option>
            @endforeach
        </select>
    </label>
</div>

<label>
    Detail lokasi
    <input type="text" name="location_detail" maxlength="255"
           placeholder="Contoh: 200 m dari jembatan desa, sisi kanan jalan"
           value="{{ old('location_detail', $report?->location_detail) }}" required>
</label>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <span style="font-weight: 600; font-size: 0.9rem;">Koordinat Lokasi</span>
        <button type="button" id="btn-detect-location" class="btn" style="font-size: 0.8rem; padding: 0.25rem 0.65rem;">
            📍 Gunakan Lokasi Saat Ini
        </button>
    </div>
    <div class="row">
        <label>
            Latitude
            <input type="text" name="latitude" id="latitude" inputmode="decimal" placeholder="-3.2145000"
                   value="{{ old('latitude', $report?->latitude) }}" required>
        </label>
        <label>
            Longitude
            <input type="text" name="longitude" id="longitude" inputmode="decimal" placeholder="105.4512000"
                   value="{{ old('longitude', $report?->longitude) }}" required>
        </label>
    </div>
    <span id="location-hint" class="hint">Klik tombol di atas untuk mengisi koordinat otomatis via GPS browser, atau masukkan manual.</span>
</div>

<label>
    Deskripsi kondisi yang dilihat
    <textarea name="description" maxlength="2000" required>{{ old('description', $report?->description) }}</textarea>
</label>

<label>
    Bukti pendukung (opsional)
    <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf">
    <span class="hint">
        JPG, JPEG, PNG, atau PDF, maksimal 2 MB.
        @if ($report?->hasEvidence())
            File saat ini: {{ $report->evidence_original_name }} (pilih file baru untuk mengganti).
        @endif
    </span>
</label>

@push('scripts')
<script>
    document.getElementById('btn-detect-location')?.addEventListener('click', function () {
        const hint = document.getElementById('location-hint');
        if (!navigator.geolocation) {
            if (hint) hint.textContent = 'Browser Anda tidak mendukung geolokasi.';
            return;
        }

        if (hint) hint.textContent = 'Mendeteksi lokasi...';
        navigator.geolocation.getCurrentPosition(
            function (position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
                if (hint) hint.textContent = 'Koordinat lokasi berhasil dideteksi.';
            },
            function (error) {
                let msg = 'Gagal mendeteksi lokasi.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Izin akses lokasi ditolak oleh browser/pengguna.';
                } else if (error.code === error.TIMEOUT) {
                    msg = 'Waktu permintaan lokasi habis.';
                }
                if (hint) hint.textContent = msg + ' Silakan masukkan manual.';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
</script>
@endpush
