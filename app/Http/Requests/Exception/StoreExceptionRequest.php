<?php

namespace App\Http\Requests\Exception;

use App\Models\ShipmentException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exception_type' => ['required', 'string', Rule::in(ShipmentException::TYPES)],
            'severity' => ['required', 'string', Rule::in(ShipmentException::SEVERITIES)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
