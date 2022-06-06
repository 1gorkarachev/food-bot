@extends('dashboard.layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>{{ $user->full_name }}</h3>
        </div>
        <div class="card-body row">
            <form method="POST" action="{{ route('dashboard.profile.self-update') }}" class="col-6">
                @csrf

                <fieldset disabled>
                    <div class="mb-3">
                        <label for="disabledTextInput" class="form-label">Name</label>
                        <input type="text" id="disabledTextInput" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="disabledTextInput" class="form-label">Surname</label>
                        <input type="text" id="disabledTextInput" class="form-control" value="{{ $user->surname }}">
                    </div>
                    <div class="mb-3">
                        <label for="disabledTextInput" class="form-label">Email</label>
                        <input type="text" id="disabledTextInput" class="form-control" value="{{ $user->email }}">
                    </div>
                </fieldset>

                <fieldset>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Birthday</label>
                        <input type="date" id="dateInput" class="form-control" name="birth" value="{{ $user->birth }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Phone</label>
                        <input type="text" id="textInput" class="form-control" name="phone" value="{{ $user->phone }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">City</label>
                        <input type="text" id="textInput" class="form-control" name="city" value="{{ $user->city }}">
                    </div>
                </fieldset>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-end">
                    <button class="btn btn-success px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection



