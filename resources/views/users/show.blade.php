@extends('users.layouts.users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>{{ $user->full_name }}</h3>
        </div>
        <div class="card-body row">
            <form method="POST" action="{{ route('users.update', $user) }}" class="col-6">
                @method('PATCH')
                @csrf
                <input name="id" value="{{ $user->id }}" hidden>

                <fieldset>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Name</label>
                        <input type="text" id="textInput" class="form-control" name="name" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Surname</label>
                        <input type="text" id="textInput" class="form-control" name="surname" value="{{ $user->surname }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Email</label>
                        <input type="text" id="textInput" class="form-control" name="email" value="{{ $user->email }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Birth</label>
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
{{--                    <div class="mb-3">--}}
{{--                        <label for="textArea" class="form-label">Текст поздравления</label>--}}
{{--                        <textarea id="textArea" class="form-control" name="birth_text">{{ $user->birth_text }}</textarea>--}}
{{--                    </div>--}}
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Password</label>
                        <input type="text" id="textInput" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="select" class="form-label">Role</label>
                        <select id="select" name="role" class="form-select" aria-label="Default select example">
                            @foreach($roles as $role)
                                <option {{ $user->roles->first()->id === $role->id ? 'selected' : '' }} value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
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
