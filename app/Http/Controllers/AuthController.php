<?php

namespace App\Http\Controllers;

use Adldap\Models\User as AdldapUser;
use App\Http\Presenters\LoginPresenter;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Orchestra\Model\Role;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * The username string to use for authentication.
     *
     * @var string
     */
    protected $username = 'email';

    /**
     * Constructor.
     *
     * @param LoginPresenter $presenter
     */
    public function __construct(LoginPresenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        $form = $this->presenter->form();

        return view('pages.auth.login.index', compact('form'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(LoginRequest $request)
    {
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        try {
            if (Auth::attempt($credentials, $request->has('remember'))) {
                return $this->handleUserWasAuthenticated($request, $throttles);
            }
        } catch (\Exception $e) {
            // Catch LDAP bind errors.
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param bool                     $throttles
     *
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if ($request->user()->adldapUser instanceof AdldapUser) {
            $this->handleLdapUserWasAuthenticated($request->user(), $request->user()->adldapUser);
        }

        flash()->success('Success!', "You're logged in!");

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Attaches roles depending on the users active directory group.
     *
     * @param User       $user
     * @param AdldapUser $adldapUser
     */
    protected function handleLdapUserWasAuthenticated(User $user, AdldapUser $adldapUser)
    {
        if ($adldapUser->inGroup('Help Desk')) {
            $admin = Role::admin();

            if ($admin instanceof Role) {
                $user->attachRole($admin->getKey());
            }
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only($this->loginUsername(), 'password');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        // Log the user out
        Auth::logout();

        // Flush their session
        Session::flush();

        flash()->success('Success!', "You've been logged out!");

        return redirect('/');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    /**
     * Returns the failed login message.
     *
     * @return string
     */
    public function getFailedLoginMessage()
    {
        return 'The email or password was incorrect.';
    }

    /**
     * Returns the login path.
     *
     * @return string
     */
    public function loginPath()
    {
        return route('auth.login.index');
    }

    /**
     * Get the redirect path when users are authenticated.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route('welcome.index');
    }

    /**
     * Determine if the class is using the ThrottlesLogins trait.
     *
     * @return bool
     */
    protected function isUsingThrottlesLoginsTrait()
    {
        return in_array(
            ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );
    }
}
