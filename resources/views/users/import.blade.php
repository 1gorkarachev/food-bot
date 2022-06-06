@extends('users.layouts.users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>Import users</h3>
        </div>
        <div class="card-body">
            <div class="mb-2">
                @if (session('success'))
                    <div class="alert alert-success mb-2">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="formFile" class="form-label">Select file to import users (.xlsx):</label>
                    <div class="row">
                        <div class="col-10">
                            <input class="form-control" type="file" id="formFile" name="file">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-success">Import</button>
                        </div>
                    </div>
                </form>
                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
