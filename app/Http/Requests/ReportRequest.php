<?php

namespace App\Http\Requests;

use App\Enums\ConditionLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public const LAT_MIN = -11;
    public const LAT_MAX = 6;
    public const LNG_MIN = 95;
    public const LNG_MAX = 141;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->where('is_active', true),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'location_detail' => ['required', 'string', 'max:255'],
            'latitude' => [
                'required',
                'numeric',
                'decimal:0,7',
                'between:'.self::LAT_MIN.','.self::LAT_MAX,
            ],
            'longitude' => [
                'required',
                'numeric',
                'decimal:0,7',
                'between:'.self::LNG_MIN.','.self::LNG_MAX,
            ],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'condition_level' => ['required', Rule::enum(ConditionLevel::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'area_id' => 'area/wilayah',
            'title' => 'judul laporan',
            'description' => 'deskripsi kondisi',
            'location_detail' => 'detail lokasi',
            'occurred_at' => 'waktu kejadian',
            'condition_level' => 'tingkat kondisi',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'area_id.exists' => 'Area yang dipilih tidak tersedia atau sudah tidak aktif.',
            'latitude.between' => 'Latitude harus berada di wilayah Indonesia ('.self::LAT_MIN.' s.d. '.self::LAT_MAX.').',
            'longitude.between' => 'Longitude harus berada di wilayah Indonesia ('.self::LNG_MIN.' s.d. '.self::LNG_MAX.').',
            'latitude.decimal' => 'Latitude maksimal 7 angka di belakang koma.',
            'longitude.decimal' => 'Longitude maksimal 7 angka di belakang koma.',
            'occurred_at.before_or_equal' => 'Waktu kejadian tidak boleh di masa depan.',
        ];
    }
}
