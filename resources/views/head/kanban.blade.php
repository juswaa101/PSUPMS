@extends('layout.head.kanban.project-kanban')
@section('title', 'Head - Projects')
@section('meta')
    {{-- get the current authenticated user's id and set in meta --}}
    @if (isset(Auth::user()->id))
        <meta name="user-id" content="{{ Auth::user()->id }}">
    @endif
@endsection

@section('content')
    <div class="pad">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (Auth::check())
                    <meta name="project_id" content="{{ $project->project_id }}">
                    <heads :item="{{ $project }}" :users="{{ $users }}" :fetch="{{ $fetch }}"
                        :staff="{{ $staff }}" :head="{{ $head }}" :logged="{{ Auth::user() }}"
                        :user_assigned="{{ $userAssignedProject }}" :user_head="{{ $user_head }}"
                        :is_head="{{ $isProjectHead }}" :notification="{{ $notification }}"
                        :projects="{{ $projects }}" :invitation="{{ $invitation }}"
                        :kanban_task="{{ $kanbanTask }}" :kanban_board="{{ $kanbanBoard }}"></heads>
                @endif
            </div>
        </div>
    </div>
@endsection
