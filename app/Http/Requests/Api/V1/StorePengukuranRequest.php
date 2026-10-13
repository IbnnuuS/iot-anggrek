<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePengukuranRequest extends FormRequest
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
            'mac_address' => 'required|string|max:32|exists:sensors,mac_address',
            'temperature_c' => 'required|numeric|min:-40|max:80',
            'humidity_pct' => 'required|numeric|min:0|max:100',
            'soil_moisture_pct' => 'nullable|numeric|min:0|max:100',
            'soil_temperature_c' => 'nullable|numeric|min:-40|max:80',
            'soil_ec' => 'nullable|integer|min:0',
            'soil_ph' => 'nullable|numeric|min:0|max:14',
            'nitrogen' => 'nullable|integer|min:0',
            'phosphorus' => 'nullable|integer|min:0',
            'potassium' => 'nullable|integer|min:0',
            'pump_status' => 'nullable|string|max:100',
            'kondisi_lahan' => 'nullable|string|max:255',
            'recorded_at' => 'nullable|date',
        ];
    }
}
