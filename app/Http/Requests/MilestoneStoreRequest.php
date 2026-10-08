<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use Illuminate\Foundation\Http\FormRequest;

class MilestoneStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Milestone::class, $this->route('project')]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama milestone wajib diisi.',
            'target_date.required' => 'Target tanggal pencapaian wajib diisi.',
            'status.required' => 'Status milestone wajib dipilih.',
        ];
    }
}
