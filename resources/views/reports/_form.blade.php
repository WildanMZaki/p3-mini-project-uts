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
        <input type="datetime-local" name="occurred_at"
               value="{{ old('occurred_at', $report?->occurred_at?->format('Y-m-d\TH:i')) }}" required>
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

<div class="row">
    <label>
        Latitude
        <input type="text" name="latitude" inputmode="decimal" placeholder="-3.2145000"
               value="{{ old('latitude', $report?->latitude) }}" required>
    </label>
    <label>
        Longitude
        <input type="text" name="longitude" inputmode="decimal" placeholder="105.4512000"
               value="{{ old('longitude', $report?->longitude) }}" required>
    </label>
</div>

<label>
    Deskripsi kondisi yang dilihat
    <textarea name="description" maxlength="2000" required>{{ old('description', $report?->description) }}</textarea>
</label>
