@extends('layouts.app')

@section('main')
<div class="container mt-3">
    <div class="row">
        <div class="col-2">
            <div class="card">
                <div class="card-body">
                    @include('users.layouts.navigation')
                </div>
            </div>
        </div>
        <div class="col-10">
            @yield('content')
        </div>
    </div>
</div>
@endsection
