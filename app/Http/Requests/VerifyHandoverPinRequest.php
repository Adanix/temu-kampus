<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyHandoverPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('handover_pin')) {
            $this->merge([
                'handover_pin' => preg_replace('/[\s\-]+/', '', (string) $this->input('handover_pin')),
            ]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'handover_pin' => ['required', 'digits:6'],
        ];
    }
}
