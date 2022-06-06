@extends('users.layouts.users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>Создать пользователя</h3>
        </div>
        <div class="card-body row">
            <form method="POST" action="{{ route('users.store') }}" class="col-6">
                @csrf

                <fieldset>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Name</label>
                        <input type="text" id="textInput" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Surname</label>
                        <input type="text" id="textInput" class="form-control" name="surname">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Email</label>
                        <input type="text" id="textInput" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Birth</label>
                        <input type="date" id="dateInput" class="form-control" name="birth">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Phone</label>
                        <input type="text" id="textInput" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">City</label>
                        <input type="text" id="textInput" class="form-control" name="city">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Password</label>
                        <input type="text" id="textInput" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="select" class="form-label">Role</label>
                        <select id="select" name="role" class="form-select" aria-label="Default select example">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
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