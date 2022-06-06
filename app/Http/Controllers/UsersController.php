<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UsersImportRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\Users\UsersImport;
use App\Models\User;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::paginate(20);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('users.show', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();
        $role = Role::findById(Arr::get($validated, 'role'));
        $validated['password'] = bcrypt($validated['password']);

        $user->fill($validated)->save();
        $user->removeRole($user->roles()->first())->assignRole($role);

        return back();
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $role = Role::findById(Arr::get($validated, 'role'));
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);
        $user->assignRole($role);

        return redirect()->route('users.index');
    }

    public function importView()
    {
        return view('users.import');
    }

    public function import(UsersImportRequest $request)
    {
        $file = Arr::get($request->validated(), 'file');

        Excel::import(new UsersImport, $file);

        return back()->with('success', 'All good!');
    }
}
