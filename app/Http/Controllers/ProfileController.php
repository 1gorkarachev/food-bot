<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\ProfileSelfUpdatePequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.profile', compact('user'));
    }

    public function selfUpdate(ProfileSelfUpdatePequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();
        $user->fill($validated)->save();

        return back();
    }

    public function uploadSelfImage(ProfileSelfUpdatePequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();
        dd($validated);
        $user->profile_disk->putFile();
    }
}
