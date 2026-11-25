<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampusZoneRequest extends FormRequest
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
        $zone = $this->route('zone');

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('campus_zones', 'code')->ignore($zone)],
            'name_id' => ['required', 'string', 'max:100'],
            'name_en' => ['required', 'string', 'max:100'],
            'zone_group' => ['required', 'string', 'max:40'],
        ];
    }
}
