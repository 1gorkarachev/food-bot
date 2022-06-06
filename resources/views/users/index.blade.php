@extends('users.layouts.users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>Users</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Full name</th>
                    <th>Birthday</th>
                    <th>City</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><a href="{{ route('users.show', $user) }}">{{ $user->full_name }}</a></td>
                        <td>{{ $user->birth }}</td>
                        <td>{{ $user->city }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
@endsection
