@extends('admin-master')
    @section('title', 'home')
    @section('content')
    @endsection
    @section('time')
        <div class="container">
            <div class="row text-center mt-5">
                <h1>Hello,  {{ Auth::user()->name }}</h1>
            </div>

            <div class="row text-center mt-1">
                <h6 id="time"></h6>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Featured
                        </div>
                      <div class="card-body">
                        mama mo
                      </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Featured
                        </div>
                      <div class="card-body">
                        mama mo
                      </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection