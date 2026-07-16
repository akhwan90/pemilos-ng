<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Catatan tidak boleh kosong.',
            'note.max' => 'Catatan maksimal 5000 karakter.',
        ];
    }
}
