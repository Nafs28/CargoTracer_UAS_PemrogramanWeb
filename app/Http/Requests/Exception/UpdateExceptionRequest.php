<?php

namespace App\Http\Requests\Exception;

use App\Models\ShipmentException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::in(ShipmentException::STATUSES)],
            'severity' => ['sometimes', 'string', Rule::in(ShipmentException::SEVERITIES)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
