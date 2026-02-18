<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParseHomeownersFromCsvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'csv' => [
                'required',
                'file',
                'mimes:csv, txt',
                'max:2048'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'csv.required' => 'Please upload a CSV file.',
            'csv.file' => 'The uploaded item must be a file.',
            'csv.mimes' => 'The file must be a CSV file (.csv or .txt).',
            'csv.max' => 'The CSV file must not be larger than 2MB.',
        ];
    }
}
