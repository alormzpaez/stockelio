<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'state_name' => 'required_without:is_preferred|string',
            'city' => 'required_without:is_preferred|string',
            'locality' => 'required_without:is_preferred|string',
            'address' => 'required_without:is_preferred|string',
            'zip' => 'required_without:is_preferred|string|digits:5|numeric',
            'phone' => 'required_without:is_preferred|string|digits:10|numeric',
            'is_preferred' => 'sometimes|boolean',
        ];
    }
}
