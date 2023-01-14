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
            'project_title' => ['required', 'max:255', Rule::unique('projects')->ignore($project->project_title, 'project_title'), 'regex:/^[\s\w-]*$/'],
            'project_description' => ['required', 'regex:/^[\s\w-]*$/'],
            'project_start_date' => ['required', 'date'],
            'project_end_date' => ['required', 'date', 'after_or_equal:project_start_date'],
            'activity_name' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required|regex:/^[\s\w-]*$/',
            'study_title' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,extension_project|required|regex:/^[\s\w-]*$/',
            'duration' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,extension_project|required|regex:/^[\s\w-]*$/',
            'program_title' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required|regex:/^[\s\w-]*$/',
            'location' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required',
            'service_type' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required',
            'participant_no' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required|integer|regex:/^[\s\w-]*$/',
            'training_no' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required|integer|regex:/^[\s\w-]*$/',
            'responsible_person/department' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,research_project|required|regex:/^[\s\w-]*$/',
            'budget_month' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,extension_project|required|integer|regex:/^[\s\w-]*$/',
            'total_budget_released' => 'exclude_if:template,default|exclude_if:template,igp|exclude_if:template,extension_project|required|integer|regex:/^[\s\w-]*$/'
        ];
    }
}
