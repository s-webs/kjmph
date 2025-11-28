<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticateFormRequest;
use App\MoonShine\Pages\LoginPage;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AuthenticateController extends Controller
{
    public function form()
    {
        return view('pages.auth.login');
    }

    public function authenticate(AuthenticateFormRequest $request): RedirectResponse
    {
        if (!auth()->attempt($request->validated())) {
            return back()->withErrors([
                'email' => __('moonshine::auth.failed')
            ]);
        }

        return redirect(route('home'));
    }

    public function logout(
        #[Auth]
        Guard   $guard,
        Request $request
    ): RedirectResponse
    {
        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
}
