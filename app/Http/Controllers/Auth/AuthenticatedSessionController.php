<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showDashboard()
    {
        if (Gate::allows('access-admin')) {
            return view('admin_dashboard', ['data' => User::all()]);
        }else{
            return view('dashboard');
        }
    }

    public function userEdit($id)
    {
        return view('edit_user', ['data' => User::find($id)]);
    }

    public function userEditSubmit($id, Request $request)
    {
        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        if (Hash::needsRehash($request->password))
        {
            $newPassword = Hash::make($request->password);
            $user->password = $newPassword;
        }
        $user->role = $request->input('role');
        $user->save();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showUsersInfoByFilter(Request $request)
    {
        return view('show_users_info_by_filter', ['data' => User::where('name', $request->filter)
            ->orWhere('email', $request->filter)
            ->orWhere('phone_number', $request->filter)
            ->orWhere('role', $request->filter)
            ->get(), 'request' => $request->filter]);
    }

    public function showUsersInfoByAbc(Request $request)
    {
        return view('show_users_info_by_abc', ['data' => User::orderBy($request->column, $request->filter)
            ->get()]);
    }

    public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }
        return Redirect::back();
    }
}
