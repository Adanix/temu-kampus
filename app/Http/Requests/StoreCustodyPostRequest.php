<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustodyPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'campus_zone_id' => ['required', 'integer', 'exists:campus_zones,id'],
            'name_id' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'operating_hours' => ['required', 'string', 'max:80'],
            'contact_ext' => ['nullable', 'string', 'max:40'],
        ];
    }
}
