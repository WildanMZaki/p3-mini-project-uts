<?php

namespace App\Http\Requests;

use App\Enums\ForwardingChannel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreForwardingRequest extends FormRequest
{
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
            'agency_id' => [
                'required',
                'integer',
                Rule::exists('agencies', 'id')->where('is_active', true),
            ],
            'channel' => ['required', Rule::enum(ForwardingChannel::class)],
            'forwarded_at' => ['required', 'date', 'before_or_equal:now'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'agency_id' => 'pihak terkait',
            'channel' => 'media penerusan',
            'forwarded_at' => 'waktu penerusan',
            'note' => 'catatan',
        ];
    }
}
