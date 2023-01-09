@extends('layout.admin-layout.admin-master')
@section('title', 'Dashboard')
@section('content')
@section('time')
    <div class="container">
        <div class="row text-center mt-5">
            <h1>Hello, {{ Auth::user()->name }}</h1>
        </div>

        <div class="row text-center mt-1">
            <h6 id="time"></h6>
        </div>

        <hr>

        <div class="row my-2">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                Finished Projects
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <button class="btn btn-info" data-bs-toggle="offcanvas"
                                    data-bs-target="#finishedProjects">See All</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($limitFinishedProject as $item)
                            <div class="row p-0">
                                <div class="row p-0">
                                    <div class="col-md-6 text-end p-4">
                                        <p><a href="/admin/project/{{ $item->uuid }}"> {{ $item->project_title }}</a></p>
                                    </div>
                                    <div class="col-md-6 text-end p-4">
                                        <a class="btn btn-secondary" href="/admin/unfinish-project/{{ $item->project_id }}">UNFINISH PROJECT</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="row p-0">
                                <p>{{ __('No Finished Projects Yet!') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                Projects
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <button class="btn btn-info" data-bs-toggle="offcanvas" data-bs-target="#allProjects">See
                                    All</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-start">
                                <button class="btn btn-secondary w-100" data-bs-toggle="offcanvas"
                                    data-bs-target="#defaultOffcanvas"><i class="bx bx-book"></i> Default</button>
                            </div>

                            <div class="col-md-6 text-start">
                                <button class="btn btn-warning w-100" data-bs-toggle="offcanvas"
                                    data-bs-target="#reOffcanvas">Research Project</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 text-start">
                                <button class="btn btn-danger w-100" data-bs-toggle="offcanvas"
                                    data-bs-target="#igpOffcanvas">IGP</button>
                            </div>
                            <div class="col-md-6 text-start">
                                <button class="btn btn-success w-100" data-bs-toggle="offcanvas"
                                    data-bs-target="#epOffcanvas">Extension Project</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="defaultOffcanvas" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Projects in Default</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($default as $def)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $def->project_title }}
                            </div>
                            <div class="col-md-6">
                                <a href="/admin/project/{{ $def->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open"></i> Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Project Yet in Default</h4>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="reOffcanvas" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Projects in Research Project</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($research as $ref)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $ref->project_title }}
                            </div>
                            <div class="col-md-6">
                                <a href="/admin/project/{{ $ref->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open"></i> Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Project Yet in Research Project</h4>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="igpOffcanvas" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Projects in IGP</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($igp as $i)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $i->project_title }}
                            </div>
                            <div class="col-md-6">
                                <a href="/admin/project/{{ $i->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open"></i> Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Project Yet in IGP</h4>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="epOffcanvas" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Projects in Extension Project</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($extension as $e)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $e->project_title }}
                            </div>
                            <div class="col-md-6">
                                <a href="/admin/project/{{ $e->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open"></i> Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Project Yet in Extension Project</h4>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="finishedProjects" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Finished Projects</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($finishedProjects as $e)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ $e->project_title }}
                            </div>
                            <div class="col-md-6 mt-2">
                                <a href="/admin/project/{{ $e->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open w-100"></i> Open Project</a>
                            </div>
                            <div class="col-md-6 mt-2">
                                <a class="btn btn-secondary w-100" href="/admin/unfinish-project/{{ $item->project_id }}">UNFINISH PROJECT</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Finished Project Yet</h4>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Offcanvas -->
    <div id="allProjects" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">All Projects</h4>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            @forelse($project as $item)
                <div class="card notification-card notification-invitation mb-2 mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $item->project_title }}
                            </div>
                            <div class="col-md-6">
                                <a href="/admin/project/{{ $item->uuid }}" class="btn btn-primary"><i
                                        class="bx bx-envelope-open"></i> Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container p-3">
                    <h4 class="text-center">No Project Yet</h4>
                </div>
            @endforelse
        </div>
    </div>
@endsection
