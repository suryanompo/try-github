<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Task::class, $this->route('project')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:todo,in_progress,review,done'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul task wajib diisi.',
            'assigned_to.exists' => 'Assignee yang dipilih tidak valid.',
            'priority.required' => 'Prioritas task wajib dipilih.',
            'status.required' => 'Status task wajib dipilih.',
            'due_date.after_or_equal' => 'Batas waktu tidak boleh sebelum tanggal mulai.',
        ];
    }
}
