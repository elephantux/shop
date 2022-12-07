<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function callback(string $driver)
    {
        $socialUser = Socialite::driver($driver)->user();

        $user = User::query()->firstOrCreate([
            'email' => $socialUser->email,
        ], [
            $driver . '_id' => $socialUser->id,
            'name' => $socialUser->name ?? '',
            'password' => bcrypt(Str::random(8)),
        ]);

        if (!$user->wasRecentlyCreated) {
            $user->update(['github_id' => $socialUser->id]);
        }

        auth()->login($user);

        return redirect()->intended(route('home'));
    }
}
