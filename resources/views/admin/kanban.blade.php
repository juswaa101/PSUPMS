@extends('layout.admin-layout.kanban.project-kanban')
@section('title', 'Admin - Projects')
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
                @if(Auth::check())
                    <meta name="project_id" content="{{ $project->project_id }}">
                    <dashboard :item="{{ $project }}" :users="{{ $users }}" :fetch="{{ $fetch }}"
                               :staff="{{ $staff }}" :head="{{ $head }}" :logged="{{ Auth::user() }}"
                               :user_assigned="{{ $userAssignedProject }}" :user_head="{{ $user_head }}"
                               :notification="{{ $notification }}" :projects="{{ $projects }}"
                    ></dashboard>
                @endif
            </div>
        </div>
    </div>
@endsection
