<?php

namespace App\Processors\ActiveDirectory;

use Adldap\Contracts\Adldap;
use Adldap\Models\User;
use Adldap\Objects\AccountControl;
use Adldap\Schemas\ActiveDirectory;
use App\Http\Presenters\ActiveDirectory\UserPresenter;
use App\Http\Requests\ActiveDirectory\UserImportRequest;
use App\Http\Requests\ActiveDirectory\UserRequest;
use App\Jobs\ActiveDirectory\ImportUser;
use App\Policies\ActiveDirectory\UserPolicy;
use App\Processors\Processor;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProcessor extends Processor
{
    /**
     * @var UserPresenter
     */
    protected $presenter;

    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * Constructor.
     *
     * @param UserPresenter $presenter
     * @param Adldap        $adldap
     */
    public function __construct(UserPresenter $presenter, Adldap $adldap)
    {
        $this->presenter = $presenter;
        $this->adldap = $adldap;
    }

    /**
     * Displays all active directory users.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function index(Request $request)
    {
        if (UserPolicy::index(auth()->user())) {
            $search = $this->adldap->users()->search();

            if ($request->has('q')) {
                $query = $request->input('q');

                $search = $search
                    ->orWhereContains(ActiveDirectory::COMMON_NAME, $query)
                    ->orWhereContains(ActiveDirectory::DESCRIPTION, $query)
                    ->orWhereContains(ActiveDirectory::OPERATING_SYSTEM, $query);
            }

            $paginator = $search
                ->whereHas(ActiveDirectory::EMAIL)
                ->sortBy(ActiveDirectory::COMMON_NAME, 'asc')->paginate();

            $users = $this->presenter->table($paginator->getResults());

            $navbar = $this->presenter->navbar();

            return view('pages.active-directory.users.index', compact('users', 'navbar'));
        }

        $this->unauthorized();
    }

    /**
     * Displays a form for creating a new user.
     *
     * @return \Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function create()
    {
        if (UserPolicy::create(auth()->user())) {
            $user = $this->adldap->users()->newInstance();

            $form = $this->presenter->form($user);

            return view('pages.active-directory.users.create', compact('form'));
        }

        $this->unauthorized();
    }

    /**
     * Creates a new active directory user.
     *
     * @param UserRequest $request
     *
     * @return bool
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function store(UserRequest $request)
    {
        if (UserPolicy::create(auth()->user())) {
            $user = $this->adldap->users()->newInstance();

            $user->setAccountName($request->input('username'));
            $user->setEmail($request->input('email'));
            $user->setFirstName($request->input('first_name'));
            $user->setLastName($request->input('last_name'));
            $user->setDisplayName($request->input('display_name'));
            $user->setDescription($request->input('description'));
            $user->setProfilePath($request->input('profile_path'));
            $user->setScriptPath($request->input('logon_script'));

            $ac = $this->createUserAccountControl($request, $user);

            $user->setUserAccountControl($ac);

            return $user->save();
        }

        $this->unauthorized();
    }

    /**
     * Displays the information page for the specified user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     *
     * @throws NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function show($username)
    {
        if (UserPolicy::show(auth()->user())) {
            $user = $this->adldap->users()->find($username);

            if ($user instanceof User) {
                return view('pages.active-directory.users.show', compact('user'));
            }

            throw new NotFoundHttpException();
        }

        $this->unauthorized();
    }

    /**
     * Displays the form for editing the specified active directory user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     *
     * @throws NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function edit($username)
    {
        if (UserPolicy::edit(auth()->user())) {
            $user = $this->adldap->users()->find($username);

            if ($user instanceof User) {
                $form = $this->presenter->form($user);

                return view('pages.active-directory.users.edit', compact('form'));
            }

            throw new NotFoundHttpException();
        }

        $this->unauthorized();
    }

    /**
     * Updates the specified active directory user.
     *
     * @param UserRequest $request
     * @param string      $username
     *
     * @return bool
     *
     * @throws NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function update(UserRequest $request, $username)
    {
        if (UserPolicy::edit(auth()->user())) {
            $user = $this->adldap->users()->find($username);

            if ($user instanceof User) {
                $user->setAccountName($request->input('username', $user->getAccountName()));
                $user->setEmail($request->input('email', $user->getEmail()));
                $user->setFirstName($request->input('first_name', $user->getFirstName()));
                $user->setLastName($request->input('last_name', $user->getLastName()));
                $user->setDisplayName($request->input('display_name', $user->getDisplayName()));
                $user->setDescription($request->input('description', $user->getDescription()));
                $user->setProfilePath($request->input('profile_path', $user->getProfilePath()));
                $user->setScriptPath($request->input('logon_script', $user->getScriptPath()));

                $ac = $this->createUserAccountControl($request, $user);

                $user->setUserAccountControl($ac);

                return $user->save();
            }

            throw new NotFoundHttpException();
        }

        $this->unauthorized();
    }

    /**
     * Imports an active directory user.
     *
     * @param UserImportRequest $request
     *
     * @return bool|mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function import(UserImportRequest $request)
    {
        if (UserPolicy::import(auth()->user())) {
            $user = $this->adldap->search()->findByDn($request->input('dn'));

            if ($user instanceof User) {
                return $this->dispatch(new ImportUser($user));
            }

            return false;
        }

        $this->unauthorized();
    }

    /**
     * Creates an account control object by the specified requests parameters.
     *
     * @param UserRequest $request
     * @param User        $user
     *
     * @return AccountControl
     */
    protected function createUserAccountControl(UserRequest $request, User $user)
    {
        $ac = new AccountControl($user->getUserAccountControl());

        if ($request->has('control_normal_account')) {
            $ac->accountIsNormal();
        }

        if ($request->has('control_password_is_expired')) {
            $ac->passwordIsExpired();
        }

        if ($request->has('control_password_does_not_expire')) {
            $ac->passwordDoesNotExpire();
        }

        if ($request->has('control_locked')) {
            $ac->accountIsLocked();
        }

        if ($request->has('control_disabled')) {
            $ac->accountIsDisabled();
        }

        if ($request->has('control_smartcard_required')) {
            $ac->accountRequiresSmartCard();
        }

        return $ac;
    }
}
