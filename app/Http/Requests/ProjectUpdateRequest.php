<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Project;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $project = Project::where('project_id', $this->id)->first();
        return [
            'project_title' => ['required', 'max:255', Rule::unique('projects')->ignore($project->project_title, 'project_title'), 'regex:/[a-zA-Z0-9\s]+/'],
            'project_description' => ['required', 'alpha_num'],
            'project_start_date' => ['required', 'date'],
            'project_end_date' => ['required', 'date', 'after_or_equal:project_start_date'],
        ];
    }
}
