<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Maximum login attempts allowed before throttling.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Number of minutes to lockout on throttled login.
     *
     * @var int
     */
    protected $decayMinutes = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    protected function redirectTo()
    {
        $user = Auth::user();

        if ($user && ($user->isSaasAdmin() || $user->hasRole('saas_admin'))) {
            return '/admin/saas/dashboard';
        }

        if ($user && ($user->hasRole('admin') || $user->hasRole('operator') || $user->isClinicAdmin() || $user->isClinicOperator())) {
            return '/admin/dashboard';
        }

        if ($user && $user->hasRole('user')) {
            return '/dashboard';
        }

        return '/admin/dashboard';
    }
}
