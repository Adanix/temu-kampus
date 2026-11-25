<?php

namespace App\Http\Requests;

use App\Enums\DemoPersona;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuickLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'persona' => ['required', 'string', Rule::in(DemoPersona::values())],
        ];
    }
}
