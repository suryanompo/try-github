<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($projectId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client' => ['nullable', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:not_started,in_progress,on_hold,completed,overdue'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode proyek wajib diisi.',
            'code.unique' => 'Kode proyek sudah digunakan oleh proyek lain.',
            'name.required' => 'Nama proyek wajib diisi.',
            'manager_id.required' => 'Penanggung jawab (PIC) proyek wajib dipilih.',
            'manager_id.exists' => 'Penanggung jawab (PIC) yang dipilih tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'deadline.required' => 'Deadline proyek wajib diisi.',
            'deadline.after_or_equal' => 'Deadline tidak boleh lebih awal dari tanggal mulai.',
            'priority.required' => 'Prioritas proyek wajib dipilih.',
            'status.required' => 'Status proyek wajib dipilih.',
            'progress.required' => 'Nilai progress wajib diisi.',
            'progress.min' => 'Progress minimal 0%.',
            'progress.max' => 'Progress maksimal 100%.',
        ];
    }
}
